<?php
// app/Http/Controllers/PurchaseRequestController.php
namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\RoomInventory; // ЗМІНЕНО: замість WarehouseItem
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    const WAREHOUSE_BRANCH_ID = 6;

    public function index(Request $request)
    {
        $query = PurchaseRequest::with('user')->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('requested_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('requested_date', '<=', $request->date_to);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('purchase-requests.index', compact('requests'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['items.inventoryItem', 'user']);
        return view('purchase-requests.show', compact('purchaseRequest'));
    }

    public function create()
    {
        // ЗМІНЕНО: товари складу з room_inventory
        $warehouseItems = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->orderBy('equipment_type')
            ->get();
            
        $lowStockItems = RoomInventory::lowStock()->get();
        
        return view('purchase-requests.create', compact('warehouseItems', 'lowStockItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string',
            'requested_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function() use ($request) {
            $purchaseRequest = PurchaseRequest::create([
                'user_id' => Auth::id(),
                'description' => $request->description,
                'requested_date' => $request->requested_date,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $purchaseRequest->items()->create($itemData);
            }

            $purchaseRequest->recalculateTotal();
        });

        return redirect()->route('purchase-requests.index')->with('success', 'Заявку створено');
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        if (!in_array($purchaseRequest->status, ['draft', 'submitted'])) {
            return redirect()->back()->withErrors(['Неможливо редагувати заявку в поточному статусі']);
        }

        $purchaseRequest->load('items');
        $warehouseItems = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->orderBy('equipment_type')
            ->get();
        
        return view('purchase-requests.edit', compact('purchaseRequest', 'warehouseItems'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        if (!in_array($purchaseRequest->status, ['draft', 'submitted'])) {
            return redirect()->back()->withErrors(['Неможливо редагувати заявку в поточному статусі']);
        }

        $request->validate([
            'description' => 'nullable|string',
            'requested_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function() use ($request, $purchaseRequest) {
            $purchaseRequest->update([
                'description' => $request->description,
                'requested_date' => $request->requested_date,
                'notes' => $request->notes,
            ]);

            $purchaseRequest->items()->delete();
            
            foreach ($request->items as $itemData) {
                $purchaseRequest->items()->create($itemData);
            }

            $purchaseRequest->recalculateTotal();
        });

        return redirect()->route('purchase-requests.show', $purchaseRequest)->with('success', 'Заявку оновлено');
    }

    public function submit(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'draft') {
            return redirect()->back()->withErrors(['Неможливо подати заявку в поточному статусі']);
        }

        $purchaseRequest->update(['status' => 'submitted']);

        return redirect()->route('purchase-requests.show', $purchaseRequest)->with('success', 'Заявку подано на розгляд');
    }

    public function print(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['items.inventoryItem', 'user']);
        return view('purchase-requests.print', compact('purchaseRequest'));
    }
}