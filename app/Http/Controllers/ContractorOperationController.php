<?php

namespace App\Http\Controllers;

use App\Models\ContractorOperation;
use App\Models\Contractor;
use App\Models\RoomInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractorOperationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,warehouse_manager']);
    }

    public function index(Request $request)
    {
        $query = ContractorOperation::with(['contractor', 'user', 'inventory']);

        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('operation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('operation_date', '<=', $request->date_to);
        }

        $operations = $query->orderBy('operation_date', 'desc')->paginate(20);
        $contractors = Contractor::where('is_active', true)->get();

        return view('contractor-operations.index', compact('operations', 'contractors'));
    }

    public function show(ContractorOperation $contractorOperation)
    {
        $contractorOperation->load(['contractor', 'user', 'inventory']);
        return view('contractor-operations.show', compact('contractorOperation'));
    }

    public function create()
    {
        $contractors = Contractor::where('is_active', true)->get();
        $inventory = RoomInventory::with('branch')->get();
        return view('contractor-operations.create', compact('contractors', 'inventory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'inventory_id' => 'nullable|exists:room_inventory,id',
            'type' => 'required|in:send_for_repair,receive_from_repair,purchase,service',
            'contract_number' => 'nullable|string|max:255',
            'operation_date' => 'required|date',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'required|string|max:1000',
        ]);

        ContractorOperation::create([
            'user_id' => Auth::id(),
            ...$request->all()
        ]);

        return redirect()->route('contractor-operations.index')
            ->with('success', 'Операция создана');
    }

    public function edit(ContractorOperation $contractorOperation)
    {
        $contractors = Contractor::where('is_active', true)->get();
        $inventory = RoomInventory::with('branch')->get();
        return view('contractor-operations.edit', compact('contractorOperation', 'contractors', 'inventory'));
    }

    public function update(Request $request, ContractorOperation $contractorOperation)
    {
        $request->validate([
            'contractor_id' => 'required|exists:contractors,id',
            'inventory_id' => 'nullable|exists:room_inventory,id',
            'type' => 'required|in:send_for_repair,receive_from_repair,purchase,service',
            'contract_number' => 'nullable|string|max:255',
            'operation_date' => 'required|date',
            'cost' => 'nullable|numeric|min:0',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $contractorOperation->update($request->all());

        return redirect()->route('contractor-operations.show', $contractorOperation)
            ->with('success', 'Операция обновлена');
    }

    public function destroy(ContractorOperation $contractorOperation)
    {
        $contractorOperation->delete();
        
        return redirect()->route('contractor-operations.index')
            ->with('success', 'Операция удалена');
    }
}