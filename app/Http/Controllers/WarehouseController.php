<?php
// app/Http/Controllers/WarehouseController.php
namespace App\Http\Controllers;

use App\Models\RoomInventory; // ЗМІНЕНО: замість WarehouseItem
use App\Models\WarehouseMovement;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    // ID філії "Склад"
    const WAREHOUSE_BRANCH_ID = 6;

    public function index(Request $request)
    {
        // Отримуємо тільки товари зі складу
        $query = RoomInventory::warehouse(); // використовуємо scope

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('equipment_type', 'like', "%{$search}%") // назва товару
                  ->orWhere('inventory_number', 'like', "%{$search}%") // код товару
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->lowStock();
        }

        $items = $query->orderBy('equipment_type')->paginate(20);
        
        // Категорії товарів складу
        $categories = RoomInventory::warehouse()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter();
        
        $lowStockCount = RoomInventory::lowStock()->count();

        return view('warehouse.index', compact('items', 'categories', 'lowStockCount'));
    }

    public function show(RoomInventory $item)
    {
        // Перевіряємо чи це складський товар
        if (!$item->isWarehouseItem()) {
            abort(404, 'Це не складський товар');
        }

        $item->load(['movements' => function($query) {
            $query->with(['user', 'issuedToUser'])->orderBy('created_at', 'desc')->limit(20);
        }]);

        return view('warehouse.show', compact('item'));
    }

    public function create()
    {
        $categories = RoomInventory::warehouse()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter();
            
        return view('warehouse.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_type' => 'required|string|max:255', // назва товару
            'inventory_number' => 'required|string|max:255|unique:room_inventory,inventory_number', // код товару
            'notes' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ]);

        $item = RoomInventory::create([
            'branch_id' => self::WAREHOUSE_BRANCH_ID,
            'room_number' => $request->category ?? 'Загальний', // категорія як "кімната"
            'equipment_type' => $request->equipment_type, // назва товару
            'inventory_number' => $request->inventory_number, // код товару
            'notes' => $request->notes, // опис
            'unit' => $request->unit,
            'quantity' => $request->quantity,
            'min_quantity' => $request->min_quantity,
            'price' => $request->price,
            'category' => $request->category,
            'admin_telegram_id' => Auth::user()->telegram_id ?? 0,
        ]);

        // Створюємо рух для початкового залишку
        if ($item->quantity > 0) {
            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'receipt',
                'quantity' => $item->quantity,
                'balance_after' => $item->quantity,
                'note' => 'Початковий залишок',
                'operation_date' => now()->toDateString(),
            ]);
        }

        return redirect()->route('warehouse.index')->with('success', 'Товар додано успішно');
    }

    public function edit(RoomInventory $item)
    {
        if (!$item->isWarehouseItem()) {
            abort(404, 'Це не складський товар');
        }

        $categories = RoomInventory::warehouse()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter();
            
        return view('warehouse.edit', compact('item', 'categories'));
    }

    public function update(Request $request, RoomInventory $item)
    {
        if (!$item->isWarehouseItem()) {
            abort(404, 'Це не складський товар');
        }

        $request->validate([
            'equipment_type' => 'required|string|max:255',
            'inventory_number' => 'required|string|max:255|unique:room_inventory,inventory_number,' . $item->id,
            'notes' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ]);

        $item->update([
            'equipment_type' => $request->equipment_type,
            'inventory_number' => $request->inventory_number,
            'notes' => $request->notes,
            'unit' => $request->unit,
            'min_quantity' => $request->min_quantity,
            'price' => $request->price,
            'category' => $request->category,
            'room_number' => $request->category ?? 'Загальний',
        ]);

        return redirect()->route('warehouse.show', $item)->with('success', 'Товар оновлено');
    }

    public function receipt(Request $request, RoomInventory $item)
    {
        if (!$item->isWarehouseItem()) {
            abort(404, 'Це не складський товар');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
            'document_number' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $item) {
            $newBalance = $item->quantity + $request->quantity;
            
            $item->update(['quantity' => $newBalance]);

            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'receipt',
                'quantity' => $request->quantity,
                'balance_after' => $newBalance,
                'note' => $request->note,
                'document_number' => $request->document_number,
                'operation_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('warehouse.show', $item)->with('success', 'Надходження зафіксовано');
    }

    public function issue(Request $request, RoomInventory $item)
    {
        if (!$item->isWarehouseItem()) {
            abort(404, 'Це не складський товар');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->quantity,
            'note' => 'nullable|string|max:500',
            'issued_to' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $item) {
            $newBalance = $item->quantity - $request->quantity;
            
            $item->update(['quantity' => $newBalance]);

            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'issue',
                'quantity' => -$request->quantity,
                'balance_after' => $newBalance,
                'note' => $request->note . ($request->issued_to ? " (Видано: {$request->issued_to})" : ''),
                'operation_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('warehouse.show', $item)->with('success', 'Видачу зафіксовано');
    }

    public function movements(Request $request)
    {
        $query = WarehouseMovement::with(['user', 'inventoryItem', 'issuedToUser']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('operation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('operation_date', '<=', $request->date_to);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('warehouse.movements', compact('movements'));
    }
}