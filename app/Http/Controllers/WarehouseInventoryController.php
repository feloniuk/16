<?php
// app/Http/Controllers/WarehouseInventoryController.php
namespace App\Http\Controllers;

use App\Models\WarehouseInventory;
use App\Models\WarehouseInventoryItem;
use App\Models\WarehouseItem;
use App\Models\WarehouseMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseInventoryController extends Controller
{

    public function index(Request $request)
    {
        $query = WarehouseInventory::with('user')->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('inventory_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('inventory_date', '<=', $request->date_to);
        }

        $inventories = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('warehouse-inventory.index', compact('inventories'));
    }

    public function show(WarehouseInventory $inventory)
    {
        $inventory->load(['items.warehouseItem', 'user']);
        return view('warehouse-inventory.show', compact('inventory'));
    }

    public function create()
    {
        $warehouseItems = WarehouseItem::active()->orderBy('name')->get();
        return view('warehouse-inventory.create', compact('warehouseItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*' => 'exists:warehouse_items,id',
        ]);

        DB::transaction(function() use ($request) {
            $inventory = WarehouseInventory::create([
                'user_id' => Auth::id(),
                'inventory_date' => $request->inventory_date,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemId) {
                $warehouseItem = WarehouseItem::find($itemId);
                
                WarehouseInventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'warehouse_item_id' => $itemId,
                    'system_quantity' => $warehouseItem->quantity,
                    'actual_quantity' => $warehouseItem->quantity, // начальное значение
                    'difference' => 0,
                ]);
            }
        });

        return redirect()->route('warehouse-inventory.index')->with('success', 'Інвентаризацію розпочато');
    }

    public function edit(WarehouseInventory $inventory)
    {
        if ($inventory->status === 'completed') {
            return redirect()->back()->withErrors(['Неможливо редагувати завершену інвентаризацію']);
        }

        $inventory->load(['items.warehouseItem']);
        return view('warehouse-inventory.edit', compact('inventory'));
    }

    public function updateItem(Request $request, $inventoryId, $itemId)
    {
        $inventory = WarehouseInventory::findOrFail($inventoryId);
        $item = WarehouseInventoryItem::where('inventory_id', $inventoryId)
                                    ->where('id', $itemId)
                                    ->firstOrFail();

        if ($inventory->status === 'completed') {
            return response()->json(['error' => 'Неможливо редагувати завершену інвентаризацію'], 422);
        }

        $request->validate([
            'actual_quantity' => 'required|integer|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $difference = $request->actual_quantity - $item->system_quantity;

        $item->update([
            'actual_quantity' => $request->actual_quantity,
            'difference' => $difference,
            'note' => $request->note,
        ]);

        return response()->json([
            'success' => true, 
            'difference' => $difference,
            'message' => 'Позицію оновлено'
        ]);
    }

    public function complete(WarehouseInventory $inventory)
    {
        if ($inventory->status === 'completed') {
            return redirect()->back()->withErrors(['Інвентаризація вже завершена']);
        }

        DB::transaction(function() use ($inventory) {
            $inventory->update(['status' => 'completed']);

            // Обновляем остатки и создаем движения для расхождений
            foreach ($inventory->items as $item) {
                if ($item->difference != 0) {
                    $warehouseItem = $item->warehouseItem;
                    $warehouseItem->update(['quantity' => $item->actual_quantity]);

                    WarehouseMovement::create([
                        'user_id' => Auth::id(),
                        'warehouse_item_id' => $warehouseItem->id,
                        'type' => 'inventory',
                        'quantity' => $item->difference,
                        'balance_after' => $item->actual_quantity,
                        'note' => "Інвентаризація #{$inventory->inventory_number}" . 
                                 ($item->note ? " - {$item->note}" : ''),
                        'operation_date' => $inventory->inventory_date,
                    ]);
                }
            }
        });

        return redirect()->route('warehouse-inventory.show', $inventory)
                        ->with('success', 'Інвентаризацію завершено');
    }

    public function quickInventory()
    {
        $items = WarehouseItem::active()->orderBy('name')->get();
        return view('warehouse-inventory.quick', compact('items'));
    }

    public function processQuickInventory(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:warehouse_items,id',
            'items.*.actual_quantity' => 'required|integer|min:0',
            'items.*.note' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function() use ($request) {
            $inventory = WarehouseInventory::create([
                'user_id' => Auth::id(),
                'inventory_date' => now()->toDateString(),
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $warehouseItem = WarehouseItem::find($itemData['id']);
                $difference = $itemData['actual_quantity'] - $warehouseItem->quantity;

                $inventoryItem = WarehouseInventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'warehouse_item_id' => $itemData['id'],
                    'system_quantity' => $warehouseItem->quantity,
                    'actual_quantity' => $itemData['actual_quantity'],
                    'difference' => $difference,
                    'note' => $itemData['note'] ?? null,
                ]);

                // Обновляем остатки если есть расхождения
                if ($difference != 0) {
                    $warehouseItem->update(['quantity' => $itemData['actual_quantity']]);

                    WarehouseMovement::create([
                        'user_id' => Auth::id(),
                        'warehouse_item_id' => $warehouseItem->id,
                        'type' => 'inventory',
                        'quantity' => $difference,
                        'balance_after' => $itemData['actual_quantity'],
                        'note' => "Швидка інвентаризація #{$inventory->inventory_number}" . 
                                 ($itemData['note'] ? " - {$itemData['note']}" : ''),
                        'operation_date' => now()->toDateString(),
                    ]);
                }
            }
        });

        return redirect()->route('warehouse-inventory.index')
                        ->with('success', 'Швидку інвентаризацію завершено');
    }
}