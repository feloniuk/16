<?php

// app/Http/Controllers/RepairTrackingController.php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RepairMaster;
use App\Models\RepairTracking;
use App\Models\RoomInventory;
use Illuminate\Http\Request;

class RepairTrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = RepairTracking::with(['equipment.branch', 'repairMaster']);

        // Фільтрація
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('master_id')) {
            $query->where('repair_master_id', $request->master_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('our_description', 'like', "%{$search}%")
                    ->orWhereHas('equipment', function ($eq) use ($search) {
                        $eq->where('inventory_number', 'like', "%{$search}%")
                            ->orWhere('equipment_type', 'like', "%{$search}%");
                    });
            });
        }

        $trackings = $query->orderBy('created_at', 'desc')->paginate(20);
        $trackings->appends($request->query());

        $branches = Branch::where('is_active', true)->get();
        $masters = RepairMaster::where('is_active', true)->get();

        return view('repair-tracking.index', compact('trackings', 'branches', 'masters'));
    }

    public function show(RepairTracking $repairTracking)
    {
        $repairTracking->load(['equipment.branch', 'repairMaster']);

        return view('repair-tracking.show', compact('repairTracking'));
    }

    public function create()
    {
        $equipment = RoomInventory::with('branch')->orderBy('equipment_type')->get();
        $masters = RepairMaster::where('is_active', true)->get();

        return view('repair-tracking.create', compact('equipment', 'masters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:room_inventory,id',
            'repair_master_id' => 'nullable|exists:repair_masters,id',
            'sent_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:255',
            'our_description' => 'required|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
        ]);

        RepairTracking::create($request->all());

        return redirect()->route('repair-tracking.index')
            ->with('success', 'Запис про відправку на ремонт створено');
    }

    public function edit(RepairTracking $repairTracking)
    {
        $equipment = RoomInventory::with('branch')->orderBy('equipment_type')->get();
        $masters = RepairMaster::where('is_active', true)->get();

        return view('repair-tracking.edit', compact('repairTracking', 'equipment', 'masters'));
    }

    public function update(Request $request, RepairTracking $repairTracking)
    {
        $request->validate([
            'equipment_id' => 'required|exists:room_inventory,id',
            'repair_master_id' => 'nullable|exists:repair_masters,id',
            'sent_date' => 'required|date',
            'returned_date' => 'nullable|date|after_or_equal:sent_date',
            'invoice_number' => 'nullable|string|max:255',
            'our_description' => 'required|string|max:1000',
            'repair_description' => 'nullable|string|max:1000',
            'cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:sent,in_repair,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $repairTracking->update($request->all());

        return redirect()->route('repair-tracking.index')
            ->with('success', 'Запис оновлено');
    }

    public function destroy(RepairTracking $repairTracking)
    {
        $repairTracking->delete();

        return redirect()->route('repair-tracking.index')
            ->with('success', 'Запис видалено');
    }
}
