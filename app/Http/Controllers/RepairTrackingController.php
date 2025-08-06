<?php
// app/Http/Controllers/RepairTrackingController.php
namespace App\Http\Controllers;

use App\Models\RepairTracking;
use App\Models\RepairMaster;
use App\Models\RoomInventory;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $query->whereHas('equipment', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('master_id')) {
            $query->where('repair_master_id', $request->master_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('our_description', 'like', "%{$search}%")
                  ->orWhereHas('equipment', function($eq) use ($search) {
                      $eq->where('inventory_number', 'like', "%{$search}%")
                         ->orWhere('equipment_type', 'like', "%{$search}%");
                  });
            });
        }

        $trackings = $query->orderBy('created_at', 'desc')->paginate(20);
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
        $equipment = RoomInventory::with('branch')->get();
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
            'cost' => 'nullable|numeric|min:0'
        ]);

        RepairTracking::create($request->all());

        return redirect()->route('repair-tracking.index')
            ->with('success', 'Запис про відправку на ремонт створено');
    }

    public function edit(RepairTracking $repairTracking)
    {
        $equipment = RoomInventory::with('branch')->get();
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
            'notes' => 'nullable|string|max:1000'
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

// app/Http/Controllers/RepairMasterController.php
namespace App\Http\Controllers;

use App\Models\RepairMaster;
use Illuminate\Http\Request;

class RepairMasterController extends Controller
{
    public function index()
    {
        $masters = RepairMaster::withCount('repairTrackings')->orderBy('name')->get();
        return view('repair-masters.index', compact('masters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        RepairMaster::create($request->all());

        return redirect()->route('repair-masters.index')
            ->with('success', 'Майстра додано');
    }

    public function update(Request $request, RepairMaster $repairMaster)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20', 
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $repairMaster->update($request->all());

        return redirect()->route('repair-masters.index')
            ->with('success', 'Дані майстра оновлено');
    }

    public function destroy(RepairMaster $repairMaster)
    {
        if ($repairMaster->repairTrackings()->count() > 0) {
            return redirect()->back()
                ->withErrors(['Неможливо видалити майстра, який має записи про ремонти']);
        }

        $repairMaster->delete();
        return redirect()->route('repair-masters.index')
            ->with('success', 'Майстра видалено');
    }
}