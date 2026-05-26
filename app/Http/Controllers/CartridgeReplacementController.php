<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\RoomInventory;
use App\Models\WarehouseMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartridgeReplacementController extends Controller
{
    private const WAREHOUSE_BRANCH_ID = 6;

    public function index(Request $request)
    {
        $query = CartridgeReplacement::with('branch');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cartridge_type', 'like', "%{$search}%")
                    ->orWhere('printer_info', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('replacement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('replacement_date', '<=', $request->date_to);
        }

        $cartridges = $query->orderBy('replacement_date', 'desc')->paginate(20);
        $cartridges->appends($request->query());

        $branches = Branch::where('is_active', true)->get();

        return view('cartridges.index', compact('cartridges', 'branches'));
    }

    public function show(CartridgeReplacement $cartridge)
    {
        $cartridge->load('branch', 'printer');

        return view('cartridges.show', compact('cartridge'));
    }

    public function issueFromWarehouse(Request $request, CartridgeReplacement $cartridge): RedirectResponse
    {
        $request->validate([
            'inventory_id' => 'required|integer|exists:room_inventory,id',
            'quantity' => 'required|integer|min:1',
            'operation_date' => 'required|date',
        ]);

        $item = RoomInventory::findOrFail($request->inventory_id);

        if ($item->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => "Недостатньо товару на складі. Залишок: {$item->quantity} {$item->unit}"]);
        }

        DB::transaction(function () use ($request, $cartridge, $item) {
            $newBalance = $item->quantity - $request->quantity;
            $item->update(['quantity' => $newBalance]);

            $branchName = $cartridge->branch->name ?? '—';
            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'issue',
                'quantity' => -$request->quantity,
                'balance_after' => $newBalance,
                'note' => "Картридж [{$cartridge->cartridge_type}]. Заявка #{$cartridge->id}. {$branchName}, каб. {$cartridge->room_number}",
                'operation_date' => $request->operation_date,
            ]);
        });

        return back()->with('success', 'Товар видано зі складу');
    }
}
