<?php
// app/Http/Controllers/WarehouseInventoryController.php
namespace App\Http\Controllers;

use App\Models\WarehouseInventory;
use App\Models\WarehouseInventoryItem;
use App\Models\RoomInventory; // ЗМІНЕНО: замість WarehouseItem
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
        $inventory->load(['items.inventoryItem', 'user']);
        return view('warehouse-inventory.show', compact('inventory'));
    }

    public function create()
    {
        // ЗМІНЕНО: показуємо ВСІ товари з room_inventory
        $inventoryItems = RoomInventory::with('branch')
            ->orderBy('branch_id')
            ->orderBy('equipment_type')
            ->get();
            
        return view('warehouse-inventory.create', compact('inventoryItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*' => 'exists:room_inventory,id', // ЗМІНЕНО
        ]);

        DB::transaction(function() use ($request) {
            $inventory = WarehouseInventory::create([
                'user_id' => Auth::id(),
                'inventory_date' => $request->inventory_date,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemId) {
                $inventoryItem = RoomInventory::find($itemId);
                
                WarehouseInventoryItem::create([
                    'warehouse_inventory_id' => $inventory->id,
                    'inventory_id' => $itemId,
                    'system_quantity' => $inventoryItem->quantity,
                    'actual_quantity' => $inventoryItem->quantity,
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

        $inventory->load(['items.inventoryItem.branch']);
        return view('warehouse-inventory.edit', compact('inventory'));
    }

    public function updateItem(Request $request, $inventoryId, $itemId)
    {
        $inventory = WarehouseInventory::findOrFail($inventoryId);
        $item = WarehouseInventoryItem::where('warehouse_inventory_id', $inventoryId)
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

            // Оновлюємо залишки та створюємо рухи для розбіжностей
            foreach ($inventory->items as $item) {
                if ($item->difference != 0) {
                    $inventoryItem = $item->inventoryItem;
                    $inventoryItem->update(['quantity' => $item->actual_quantity]);

                    WarehouseMovement::create([
                        'user_id' => Auth::id(),
                        'inventory_id' => $inventoryItem->id,
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
        // ЗМІНЕНО: показуємо ВСІ товари
        $items = RoomInventory::with('branch')
            ->orderBy('branch_id')
            ->orderBy('equipment_type')
            ->get();
            
        return view('warehouse-inventory.quick', compact('items'));
    }

    public function processQuickInventory(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:room_inventory,id',
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
                $inventoryItem = RoomInventory::find($itemData['id']);
                $difference = $itemData['actual_quantity'] - $inventoryItem->quantity;

                WarehouseInventoryItem::create([
                    'warehouse_inventory_id' => $inventory->id,
                    'inventory_id' => $itemData['id'],
                    'system_quantity' => $inventoryItem->quantity,
                    'actual_quantity' => $itemData['actual_quantity'],
                    'difference' => $difference,
                    'note' => $itemData['note'] ?? null,
                ]);

                // Оновлюємо залишки якщо є розбіжності
                if ($difference != 0) {
                    $inventoryItem->update(['quantity' => $itemData['actual_quantity']]);

                    WarehouseMovement::create([
                        'user_id' => Auth::id(),
                        'inventory_id' => $inventoryItem->id,
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