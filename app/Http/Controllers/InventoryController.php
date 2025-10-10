<?php
// app/Http/Controllers/InventoryController.php
namespace App\Http\Controllers;

use App\Models\RoomInventory;
use App\Models\Branch;
use App\Models\InventoryTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Список інвентарю з фільтрацією та групуванням
     */
    public function index(Request $request)
    {
        $query = RoomInventory::with('branch');

        // Фільтри
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('balance_code')) {
            $query->where('balance_code', $request->balance_code);
        }

        if ($request->filled('equipment_type')) {
            $query->where('equipment_type', 'like', '%' . $request->equipment_type . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inventory_number', 'like', "%{$search}%")
                  ->orWhere('equipment_type', 'like', "%{$search}%")
                  ->orWhere('balance_code', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        // Статистика для фільтрації
        $filteredStats = [
            'total_items' => $query->count(),
            'total_quantity' => $query->sum('quantity'),
        ];

        // Групування по найменуванню для відображення
        if ($request->filled('group_view') && $request->group_view == '1') {
            $grouped = $query->orderBy('equipment_type')
                ->get()
                ->groupBy('equipment_type')
                ->map(function ($items) {
                    return [
                        'name' => $items->first()->equipment_type,
                        'count' => $items->count(),
                        'total_quantity' => $items->sum('quantity'),
                        'balance_code' => $items->first()->balance_code,
                        'items' => $items,
                    ];
                });

            return view('inventory.index-grouped', compact('grouped', 'filteredStats'));
        }

        // Звичайний список
        $inventory = $query->orderBy('created_at', 'desc')->paginate(20);
        $branches = Branch::where('is_active', true)->get();
        
        // Коди балансів для фільтра
        $balanceCodes = RoomInventory::whereNotNull('balance_code')
            ->distinct()
            ->pluck('balance_code')
            ->sort();

        // Статистика по типах обладнання
        $equipmentStats = RoomInventory::select('equipment_type', 'balance_code')
            ->selectRaw('COUNT(*) as count, SUM(quantity) as total_qty')
            ->groupBy('equipment_type', 'balance_code')
            ->orderBy('count', 'desc')
            ->get();

        return view('inventory.index', compact(
            'inventory', 
            'branches', 
            'equipmentStats', 
            'balanceCodes',
            'filteredStats'
        ));
    }

    /**
     * Перегляд деталей позиції
     */
    public function show(RoomInventory $inventory)
    {
        $inventory->load('branch');
        
        // Історія переміщень цієї позиції
        $transfers = InventoryTransfer::where('inventory_id', $inventory->id)
            ->with(['fromBranch', 'toBranch', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Замени картриджів якщо це принтер
        $cartridgeReplacements = null;
        if (stripos($inventory->equipment_type, 'принтер') !== false || 
            stripos($inventory->equipment_type, 'мфу') !== false) {
            $cartridgeReplacements = \App\Models\CartridgeReplacement::where('printer_inventory_id', $inventory->id)
                ->orderBy('replacement_date', 'desc')
                ->limit(10)
                ->get();
        }

        return view('inventory.show', compact('inventory', 'cartridgeReplacements', 'transfers'));
    }

    /**
     * Форма переміщення товару
     */
    public function transferForm(RoomInventory $inventory)
    {
        $branches = Branch::where('is_active', true)->get();
        return view('inventory.transfer', compact('inventory', 'branches'));
    }

    /**
     * Виконання переміщення
     */
    public function transfer(Request $request, RoomInventory $inventory)
    {
        $request->validate([
            'to_branch_id' => 'required|exists:branches,id',
            'to_room_number' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1|max:' . $inventory->quantity,
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function() use ($request, $inventory) {
                $quantityToTransfer = $request->quantity;
                
                // Зберігаємо інформацію про попереднє місце
                $fromBranchId = $inventory->branch_id;
                $fromRoomNumber = $inventory->room_number;

                if ($quantityToTransfer == $inventory->quantity) {
                    // Переміщуємо всю позицію - просто змінюємо branch_id і room
                    $inventory->update([
                        'branch_id' => $request->to_branch_id,
                        'room_number' => $request->to_room_number,
                    ]);

                    $transferredInventoryId = $inventory->id;
                } else {
                    // Часткове переміщення - зменшуємо кількість і створюємо новий запис
                    $inventory->decrement('quantity', $quantityToTransfer);

                    $newInventory = $inventory->replicate();
                    $newInventory->branch_id = $request->to_branch_id;
                    $newInventory->room_number = $request->to_room_number;
                    $newInventory->quantity = $quantityToTransfer;
                    $newInventory->save();

                    $transferredInventoryId = $newInventory->id;
                }

                // Записуємо в історію переміщень
                InventoryTransfer::create([
                    'inventory_id' => $transferredInventoryId,
                    'from_branch_id' => $fromBranchId,
                    'from_room_number' => $fromRoomNumber,
                    'to_branch_id' => $request->to_branch_id,
                    'to_room_number' => $request->to_room_number,
                    'quantity' => $quantityToTransfer,
                    'user_id' => Auth::id(),
                    'transfer_date' => $request->transfer_date,
                    'notes' => $request->notes,
                ]);

                // Додаємо запис у warehouse_movements як 'transfer'
                \App\Models\WarehouseMovement::create([
                    'user_id' => Auth::id(),
                    'inventory_id' => $transferredInventoryId,
                    'type' => 'transfer',
                    'quantity' => $quantityToTransfer,
                    'balance_after' => $quantityToTransfer,
                    'note' => "Переміщення: {$fromRoomNumber} → {$request->to_room_number}" . 
                             ($request->notes ? " ({$request->notes})" : ''),
                    'operation_date' => $request->transfer_date,
                ]);

                Log::info('Inventory transfer completed', [
                    'user_id' => Auth::id(),
                    'inventory_id' => $transferredInventoryId,
                    'from' => "{$fromBranchId}:{$fromRoomNumber}",
                    'to' => "{$request->to_branch_id}:{$request->to_room_number}",
                    'quantity' => $quantityToTransfer,
                ]);
            });

            return redirect()->route('inventory.show', $inventory)
                ->with('success', "Переміщено {$request->quantity} од. в {$request->to_room_number}");

        } catch (\Exception $e) {
            Log::error('Transfer error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Помилка переміщення: ' . $e->getMessage()]);
        }
    }

    /**
     * Форма масового додавання
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $balanceCodes = RoomInventory::whereNotNull('balance_code')
            ->distinct()
            ->pluck('balance_code')
            ->sort();
        
        return view('inventory.create', compact('branches', 'balanceCodes'));
    }

    /**
     * Масове збереження обладнання
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.equipment_type' => 'required|string|max:255',
            'items.*.balance_code' => 'nullable|string|max:100',
            'items.*.brand' => 'nullable|string|max:100',
            'items.*.model' => 'nullable|string|max:100',
            'items.*.serial_number' => 'nullable|string|max:255',
            'items.*.inventory_number' => 'required|string|max:255|unique:room_inventory,inventory_number',
            'items.*.quantity' => 'nullable|integer|min:1',
            'general_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::transaction(function() use ($request) {
                $createdCount = 0;
                $generalNotes = $request->general_notes;
                
                foreach ($request->items as $itemData) {
                    $notes = $generalNotes;
                    if (!empty($itemData['notes'])) {
                        $notes = $notes ? $notes . "\n" . $itemData['notes'] : $itemData['notes'];
                    }
                    
                    RoomInventory::create([
                        'admin_telegram_id' => Auth::user()->telegram_id ?? 0,
                        'branch_id' => $request->branch_id,
                        'room_number' => $request->room_number,
                        'equipment_type' => $itemData['equipment_type'],
                        'balance_code' => $itemData['balance_code'] ?? null,
                        'brand' => $itemData['brand'] ?? null,
                        'model' => $itemData['model'] ?? null,
                        'serial_number' => $itemData['serial_number'] ?? null,
                        'inventory_number' => $itemData['inventory_number'],
                        'notes' => $notes,
                        'quantity' => $itemData['quantity'] ?? 1,
                        'unit' => 'шт',
                        'min_quantity' => 0,
                    ]);
                    
                    $createdCount++;
                }
                
                Log::info("Bulk inventory add", [
                    'user_id' => Auth::id(),
                    'branch_id' => $request->branch_id,
                    'room' => $request->room_number,
                    'count' => $createdCount
                ]);
            });
            
            return redirect()->route('inventory.index')
                ->with('success', "Успішно додано {$request->items->count()} од. обладнання");
                
        } catch (\Exception $e) {
            Log::error('Bulk add error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Помилка збереження: ' . $e->getMessage()]);
        }
    }

    /**
     * Редагування позиції
     */
    public function edit(RoomInventory $inventory)
    {
        $branches = Branch::where('is_active', true)->get();
        $balanceCodes = RoomInventory::whereNotNull('balance_code')
            ->distinct()
            ->pluck('balance_code')
            ->sort();
        
        return view('inventory.edit', compact('inventory', 'branches', 'balanceCodes'));
    }

    /**
     * Оновлення позиції
     */
    public function update(Request $request, RoomInventory $inventory)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:50',
            'equipment_type' => 'required|string|max:255',
            'balance_code' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
            'inventory_number' => 'required|string|max:255|unique:room_inventory,inventory_number,' . $inventory->id,
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        $inventory->update($request->only([
            'branch_id', 'room_number', 'equipment_type', 'balance_code',
            'brand', 'model', 'serial_number', 'inventory_number', 
            'quantity', 'notes'
        ]));

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Обладнання оновлено');
    }

    /**
     * Видалення позиції
     */
    public function destroy(RoomInventory $inventory)
    {
        $hasCartridges = \App\Models\CartridgeReplacement::where('printer_inventory_id', $inventory->id)->exists();
        $hasTransfers = InventoryTransfer::where('inventory_id', $inventory->id)->exists();
        
        if ($hasCartridges || $hasTransfers) {
            return redirect()->back()
                ->withErrors(['Неможливо видалити - є пов\'язані записи']);
        }

        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', 'Обладнання видалено');
    }

    /**
     * Експорт в Excel
     */
    public function export(Request $request)
    {
        $query = RoomInventory::with('branch');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('balance_code')) {
            $query->where('balance_code', $request->balance_code);
        }

        if ($request->filled('equipment_type')) {
            $query->where('equipment_type', 'like', '%' . $request->equipment_type . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inventory_number', 'like', "%{$search}%")
                  ->orWhere('equipment_type', 'like', "%{$search}%");
            });
        }

        $inventory = $query->orderBy('balance_code')
            ->orderBy('equipment_type')
            ->orderBy('inventory_number')
            ->get();

        $filename = 'inventory_' . date('Y-m-d_H-i') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($inventory) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'ID', 'Код балансу', 'Найменування', 'Філія', 'Кабінет',
                'Бренд', 'Модель', 'Серійний №', 'Інвентарний №', 
                'Кількість', 'Од.виміру', 'Ціна', 'Примітки'
            ], ';');

            foreach ($inventory as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->balance_code,
                    $item->equipment_type,
                    $item->branch->name,
                    $item->room_number,
                    $item->brand,
                    $item->model,
                    $item->serial_number,
                    $item->inventory_number,
                    $item->quantity,
                    $item->unit,
                    $item->price,
                    $item->notes,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Валідація інвентарних номерів (AJAX)
     */
    public function validateInventoryNumbers(Request $request)
    {
        $numbers = $request->input('numbers', []);
        $duplicates = [];
        
        foreach ($numbers as $number) {
            if (RoomInventory::where('inventory_number', $number)->exists()) {
                $duplicates[] = $number;
            }
        }
        
        return response()->json([
            'valid' => empty($duplicates),
            'duplicates' => $duplicates
        ]);
    }
}