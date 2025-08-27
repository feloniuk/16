<?php

namespace App\Http\Controllers;

use App\Models\InventoryAudit;
use App\Models\InventoryAuditItem;
use App\Models\Branch;
use App\Models\RoomInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,warehouse_manager']);
    }

    public function index(Request $request)
    {
        $query = InventoryAudit::with(['user', 'branch']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('date_from')) {
            $query->where('audit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('audit_date', '<=', $request->date_to);
        }

        $audits = $query->orderBy('audit_date', 'desc')->paginate(20);
        $branches = Branch::where('is_active', true)->get();

        return view('inventory-audits.index', compact('audits', 'branches'));
    }

    public function show(InventoryAudit $inventoryAudit)
    {
        $inventoryAudit->load(['items.inventory', 'user', 'branch']);
        return view('inventory-audits.show', compact('inventoryAudit'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        return view('inventory-audits.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'audit_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $auditNumber = 'AUD-' . date('Y') . '-' . str_pad(InventoryAudit::count() + 1, 6, '0', STR_PAD_LEFT);
        
        $totalItems = RoomInventory::where('branch_id', $request->branch_id)->count();

        $audit = InventoryAudit::create([
            'user_id' => Auth::id(),
            'branch_id' => $request->branch_id,
            'audit_number' => $auditNumber,
            'audit_date' => $request->audit_date,
            'total_items' => $totalItems,
            'notes' => $request->notes,
        ]);

        // Создаем элементы аудита для всего инвентаря филиала
        $inventoryItems = RoomInventory::where('branch_id', $request->branch_id)->get();
        
        foreach ($inventoryItems as $item) {
            InventoryAuditItem::create([
                'audit_id' => $audit->id,
                'inventory_id' => $item->id,
                'inventory_number' => $item->inventory_number,
                'equipment_type' => $item->equipment_type,
                'location' => $item->branch->name . ':' . $item->room_number,
                'status' => 'found', // По умолчанию считаем найденным
            ]);
        }

        return redirect()->route('inventory-audits.show', $audit)
            ->with('success', 'Инвентаризация создана');
    }

    public function edit(InventoryAudit $inventoryAudit)
    {
        if ($inventoryAudit->status === 'completed') {
            return redirect()->back()->withErrors(['Нельзя редактировать завершенную инвентаризацию']);
        }

        $branches = Branch::where('is_active', true)->get();
        return view('inventory-audits.edit', compact('inventoryAudit', 'branches'));
    }

    public function update(Request $request, InventoryAudit $inventoryAudit)
    {
        if ($inventoryAudit->status === 'completed') {
            return redirect()->back()->withErrors(['Нельзя редактировать завершенную инвентаризацию']);
        }

        $request->validate([
            'audit_date' => 'required|date',
            'status' => 'required|in:planned,in_progress,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        $inventoryAudit->update($request->only(['audit_date', 'status', 'notes']));

        // Если завершаем инвентаризацию, пересчитываем статистику
        if ($request->status === 'completed') {
            $this->recalculateAuditStats($inventoryAudit);
        }

        return redirect()->route('inventory-audits.show', $inventoryAudit)
            ->with('success', 'Инвентаризация обновлена');
    }

    public function updateItem(Request $request, InventoryAudit $inventoryAudit, InventoryAuditItem $item)
    {
        $request->validate([
            'status' => 'required|in:found,missing,extra,damaged',
            'notes' => 'nullable|string|max:500',
        ]);

        $item->update($request->only(['status', 'notes']));

        // Обновляем счетчики
        $this->recalculateAuditStats($inventoryAudit);

        return response()->json(['success' => true]);
    }

    private function recalculateAuditStats(InventoryAudit $audit)
    {
        $stats = $audit->items()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $audit->update([
            'checked_items' => $audit->items()->count(),
            'missing_items' => $stats['missing'] ?? 0,
            'extra_items' => $stats['extra'] ?? 0,
        ]);
    }

    public function export(InventoryAudit $inventoryAudit)
    {
        // Экспорт результатов инвентаризации в Excel
        $filename = "inventory_audit_{$inventoryAudit->audit_number}_" . date('Y-m-d') . '.xlsx';
        
        return \Excel::download(new \App\Exports\InventoryAuditExport($inventoryAudit), $filename);
    }
}