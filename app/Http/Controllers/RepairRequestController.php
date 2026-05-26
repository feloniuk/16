<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RepairRequest;
use App\Models\RoomInventory;
use App\Models\WarehouseMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepairRequestController extends Controller
{
    private const WAREHOUSE_BRANCH_ID = 6;

    public function index(Request $request)
    {
        $query = RepairRequest::with('branch');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $repairs = $query->orderBy('created_at', 'desc')->paginate(20);
        $repairs->appends($request->query());

        $branches = Branch::where('is_active', true)->get();

        return view('repairs.index', compact('repairs', 'branches'));
    }

    public function show(RepairRequest $repair)
    {
        $repair->load('branch');

        return view('repairs.show', compact('repair'));
    }

    public function update(Request $request, RepairRequest $repair)
    {
        $request->validate([
            'status' => 'required|in:нова,в_роботі,виконана',
        ]);

        $repair->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Статус заявки оновлено');
    }

    public function issueFromWarehouse(Request $request, RepairRequest $repair): RedirectResponse
    {
        $request->validate([
            'inventory_id' => 'required|integer|exists:room_inventory,id',
            'quantity' => 'required|integer|min:1',
            'operation_date' => 'required|date',
            'note' => 'nullable|string|max:500',
        ]);

        $item = RoomInventory::findOrFail($request->inventory_id);

        if ($item->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => "Недостатньо товару на складі. Залишок: {$item->quantity} {$item->unit}"]);
        }

        DB::transaction(function () use ($request, $repair, $item) {
            $newBalance = $item->quantity - $request->quantity;
            $item->update(['quantity' => $newBalance]);

            $branchName = $repair->branch->name ?? '—';
            $baseNote = "Ремонт #{$repair->id}. {$branchName}, каб. {$repair->room_number}";
            $note = $request->filled('note') ? "{$request->note}. {$baseNote}" : $baseNote;

            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'issue',
                'quantity' => -$request->quantity,
                'balance_after' => $newBalance,
                'note' => $note,
                'operation_date' => $request->operation_date,
            ]);
        });

        return back()->with('success', 'Товар видано зі складу');
    }
}
