<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Branch;
use App\Models\RoomInventory;
use App\Services\InventoryLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryTransferController extends Controller
{
    private InventoryLogService $logService;

    public function __construct(InventoryLogService $logService)
    {
        $this->middleware(['auth', 'role:admin,warehouse_manager']);
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $query = InventoryTransfer::with(['user', 'fromBranch', 'toBranch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_branch_id')) {
            $query->where('from_branch_id', $request->from_branch_id);
        }

        if ($request->filled('to_branch_id')) {
            $query->where('to_branch_id', $request->to_branch_id);
        }

        if ($request->filled('date_from')) {
            $query->where('transfer_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transfer_date', '<=', $request->date_to);
        }

        $transfers = $query->orderBy('transfer_date', 'desc')->paginate(20);
        $branches = Branch::where('is_active', true)->get();

        return view('inventory-transfers.index', compact('transfers', 'branches'));
    }

    public function show(InventoryTransfer $inventoryTransfer)
    {
        $inventoryTransfer->load(['items.inventory', 'user', 'fromBranch', 'toBranch']);
        return view('inventory-transfers.show', compact('inventoryTransfer'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        return view('inventory-transfers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'from_room' => 'nullable|string|max:50',
            'to_room' => 'nullable|string|max:50',
            'transfer_date' => 'required|date',
            'reason' => 'required|string|max:1000',
            'inventory_ids' => 'required|array|min:1',
            'inventory_ids.*' => 'exists:room_inventory,id',
        ]);

        DB::transaction(function() use ($request) {
            $transfer = InventoryTransfer::create([
                'user_id' => Auth::id(),
                'from_branch_id' => $request->from_branch_id,
                'to_branch_id' => $request->to_branch_id,
                'from_room' => $request->from_room,
                'to_room' => $request->to_room,
                'transfer_date' => $request->transfer_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            // Добавляем элементы перемещения
            foreach ($request->inventory_ids as $inventoryId) {
                InventoryTransferItem::create([
                    'transfer_id' => $transfer->id,
                    'inventory_id' => $inventoryId,
                ]);
            }
        });

        return redirect()->route('inventory-transfers.index')
            ->with('success', 'Перемещение создано');
    }

    public function complete(InventoryTransfer $inventoryTransfer)
    {
        if ($inventoryTransfer->status !== 'in_transit') {
            return redirect()->back()->withErrors(['Можно завершить только перемещения в статусе "В пути"']);
        }

        DB::transaction(function() use ($inventoryTransfer) {
            // Обновляем статус перемещения
            $inventoryTransfer->update(['status' => 'completed']);

            // Обновляем местоположение инвентаря
            foreach ($inventoryTransfer->items as $item) {
                $inventory = $item->inventory;
                $oldData = [
                    'branch' => $inventoryTransfer->fromBranch->name,
                    'room' => $inventoryTransfer->from_room,
                ];
                
                $inventory->update([
                    'branch_id' => $inventoryTransfer->to_branch_id,
                    'room_number' => $inventoryTransfer->to_room ?? $inventory->room_number,
                ]);

                $newData = [
                    'branch' => $inventoryTransfer->toBranch->name,
                    'room' => $inventory->room_number,
                ];

                // Логируем перемещение
                $this->logService->logMove($inventory, $oldData, $newData, $inventoryTransfer->reason);

                // Обновляем статус элемента
                $item->update(['status' => 'completed']);
            }
        });

        return redirect()->back()->with('success', 'Перемещение завершено');
    }

    public function cancel(InventoryTransfer $inventoryTransfer)
    {
        if ($inventoryTransfer->status === 'completed') {
            return redirect()->back()->withErrors(['Нельзя отменить завершенное перемещение']);
        }

        $inventoryTransfer->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Перемещение отменено');
    }
}