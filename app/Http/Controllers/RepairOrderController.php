<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepairOrderRequest;
use App\Http\Requests\UpdateRepairOrderRequest;
use App\Models\Branch;
use App\Models\RepairMaster;
use App\Models\RepairOrder;
use App\Models\RoomInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepairOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RepairOrder::with(['user', 'items.equipment.branch', 'repairMaster'])
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('items.equipment', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('master_id')) {
            $query->where('repair_master_id', $request->master_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('items.equipment', function ($eq) use ($search) {
                        $eq->where('inventory_number', 'like', "%{$search}%")
                            ->orWhere('equipment_type', 'like', "%{$search}%");
                    });
            });
        }

        $repairOrders = $query->orderBy('created_at', 'desc')->paginate(20);
        $repairOrders->appends($request->query());

        $branches = Branch::where('is_active', true)->get();
        $masters = RepairMaster::where('is_active', true)->get();

        return view('repair-orders.index', compact('repairOrders', 'branches', 'masters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipment = RoomInventory::with('branch')->orderBy('equipment_type')->get();
        $masters = RepairMaster::where('is_active', true)->get();

        return view('repair-orders.create', compact('equipment', 'masters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRepairOrderRequest $request)
    {
        DB::transaction(function () use ($request) {
            $order = RepairOrder::create([
                ...$request->except('items'),
                'user_id' => auth()->id(),
                'status' => 'draft',
            ]);

            foreach ($request->items as $itemData) {
                $order->items()->create($itemData);
            }

            $order->recalculateTotal();
        });

        return redirect()->route('repair-orders.index')
            ->with('success', 'Заявку на ремонт створено');
    }

    /**
     * Display the specified resource.
     */
    public function show(RepairOrder $repairOrder)
    {
        $repairOrder->load(['user', 'items.equipment.branch', 'repairMaster', 'approver']);

        return view('repair-orders.show', compact('repairOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RepairOrder $repairOrder)
    {
        if (! $repairOrder->canBeEditedBy(auth()->user())) {
            abort(403, 'Ви не можете редагувати цю заявку');
        }

        $equipment = RoomInventory::with('branch')->orderBy('equipment_type')->get();
        $masters = RepairMaster::where('is_active', true)->get();

        return view('repair-orders.edit', compact('repairOrder', 'equipment', 'masters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRepairOrderRequest $request, RepairOrder $repairOrder)
    {
        if (! $repairOrder->canBeEditedBy(auth()->user())) {
            abort(403, 'Ви не можете редагувати цю заявку');
        }

        DB::transaction(function () use ($request, $repairOrder) {
            $repairOrder->update($request->except('items'));
            $repairOrder->items()->delete();

            foreach ($request->items as $itemData) {
                $repairOrder->items()->create($itemData);
            }

            $repairOrder->recalculateTotal();
        });

        return redirect()->route('repair-orders.show', $repairOrder)
            ->with('success', 'Заявку оновлено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RepairOrder $repairOrder)
    {
        if (! $repairOrder->canBeEditedBy(auth()->user()) || $repairOrder->status !== 'draft') {
            abort(403, 'Ви можете видалити тільки власні чернетки');
        }

        $repairOrder->delete();

        return redirect()->route('repair-orders.index')
            ->with('success', 'Заявку видалено');
    }

    /**
     * Submit draft order for approval.
     */
    public function submit(RepairOrder $repairOrder)
    {
        if ($repairOrder->status !== 'draft' || $repairOrder->user_id !== auth()->id()) {
            abort(403, 'Ви можете подати тільки власну чернетку');
        }

        $repairOrder->update(['status' => 'pending_approval']);

        return redirect()->route('repair-orders.show', $repairOrder)
            ->with('success', 'Заявку відправлено на затвердження');
    }

    /**
     * Approve repair order.
     */
    public function approve(RepairOrder $repairOrder)
    {
        if (! $repairOrder->canBeApprovedBy(auth()->user())) {
            abort(403, 'Ви не можете затвердити цю заявку');
        }

        $repairOrder->approve(auth()->user());

        return back()->with('success', 'Заявку затверджено');
    }

    /**
     * Reject repair order.
     */
    public function reject(Request $request, RepairOrder $repairOrder)
    {
        if (! $repairOrder->canBeApprovedBy(auth()->user())) {
            abort(403, 'Ви не можете відхилити цю заявку');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $repairOrder->reject(auth()->user(), $request->rejection_reason);

        return back()->with('error', 'Заявку відхилено');
    }
}
