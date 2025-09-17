<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\Branch;
use Illuminate\Http\Request;

class InventoryLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,warehouse_manager']);
    }

    public function index(Request $request)
    {
        $query = InventoryLog::with(['user', 'inventory.branch']);

        // Фильтрация
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('inventory', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('inventory', function($inv) use ($search) {
                      $inv->where('inventory_number', 'like', "%{$search}%")
                          ->orWhere('equipment_type', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $branches = Branch::where('is_active', true)->get();
        $users = \App\Models\User::where('is_active', true)->get();
        
        return view('inventory-logs.index', compact('logs', 'branches', 'users'));
    }

    public function show(InventoryLog $inventoryLog)
    {
        $inventoryLog->load(['user', 'inventory.branch']);
        return view('inventory-logs.show', compact('inventoryLog'));
    }
}