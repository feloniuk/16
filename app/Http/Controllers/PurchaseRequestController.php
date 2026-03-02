<?php

// app/Http/Controllers/PurchaseRequestController.php

namespace App\Http\Controllers;

use App\Http\Requests\ReceivePurchaseRequestRequest;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\RoomInventory; // ЗМІНЕНО: замість WarehouseItem
use App\Models\WarehouseMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    const WAREHOUSE_BRANCH_ID = 6;

    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['user', 'items'])->withCount('items')->notArchived();

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
        $requests->appends($request->query());

        return view('purchase-requests.index', compact('requests'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['items.inventoryItem', 'user']);

        return view('purchase-requests.show', compact('purchaseRequest'));
    }

    public function create()
    {
        // Товари складу з room_inventory, згруповані по найменуванню
        $warehouseItems = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->select(
                'equipment_type',
                DB::raw('GROUP_CONCAT(COALESCE(full_name, "") ORDER BY id DESC LIMIT 1) as full_name'),
                DB::raw('MAX(id) as id'),
                DB::raw('MAX(unit) as unit'),
                DB::raw('MAX(price) as price'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(min_quantity) as min_quantity'),
                DB::raw('MAX(inventory_number) as inventory_number')
            )
            ->groupBy('equipment_type')
            ->orderBy('equipment_type')
            ->get();

        // Товари з низким запасом
        $lowStockItems = RoomInventory::select(
            'equipment_type',
            DB::raw('GROUP_CONCAT(COALESCE(full_name, "") ORDER BY id DESC LIMIT 1) as full_name'),
            DB::raw('MAX(id) as id'),
            DB::raw('MAX(unit) as unit'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('MAX(min_quantity) as min_quantity'),
            DB::raw('MAX(price) as price'),
            DB::raw('MAX(inventory_number) as inventory_number')
        )
            ->where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->groupBy('equipment_type')
            ->havingRaw('SUM(quantity) <= MAX(min_quantity)')
            ->orderBy('equipment_type')
            ->get();

        return view('purchase-requests.create', compact('warehouseItems', 'lowStockItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string',
            'requested_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
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
        $purchaseRequest->load('items');

        // Товари складу з room_inventory, згруповані по найменуванню
        $warehouseItems = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->select(
                'equipment_type',
                DB::raw('GROUP_CONCAT(COALESCE(full_name, "") ORDER BY id DESC LIMIT 1) as full_name'),
                DB::raw('MAX(id) as id'),
                DB::raw('MAX(unit) as unit'),
                DB::raw('MAX(price) as price'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(min_quantity) as min_quantity'),
                DB::raw('MAX(inventory_number) as inventory_number')
            )
            ->groupBy('equipment_type')
            ->orderBy('equipment_type')
            ->get();

        return view('purchase-requests.edit', compact('purchaseRequest', 'warehouseItems'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'description' => 'nullable|string',
            'requested_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseRequest) {
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

    public function approve(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'submitted') {
            return redirect()->back()->withErrors(['Заявка повинна мати статус "Подана" для підтвердження']);
        }

        if (! in_array(Auth::user()->role, ['admin', 'director'])) {
            return redirect()->back()->withErrors(['Тільки адмін або директор можуть підтвердити заявку']);
        }

        $purchaseRequest->update(['status' => 'approved']);

        return redirect()->route('purchase-requests.show', $purchaseRequest)->with('success', 'Заявку затверджено');
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'submitted') {
            return redirect()->back()->withErrors(['Заявка повинна мати статус "Подана" для відхилення']);
        }

        if (! in_array(Auth::user()->role, ['admin', 'director'])) {
            return redirect()->back()->withErrors(['Тільки адмін або директор можуть відхилити заявку']);
        }

        $purchaseRequest->update(['status' => 'rejected']);

        return redirect()->route('purchase-requests.show', $purchaseRequest)->with('success', 'Заявку відхилено');
    }

    public function print(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['items.inventoryItem', 'user']);

        return view('purchase-requests.print', compact('purchaseRequest'));
    }

    public function archive(PurchaseRequest $purchaseRequest): \Illuminate\Http\RedirectResponse
    {
        $purchaseRequest->update(['archived_at' => now()]);

        return redirect()->route('purchase-requests.index')->with('success', 'Заявку архівовано');
    }

    public function split(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'selected_indices' => 'required|array|min:1',
            'new_description' => 'nullable|string|max:255',
            'new_requested_date' => 'required|date|after_or_equal:today',
            'selected_items' => 'required|array|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, $purchaseRequest) {
                // Одержуємо індекси вибраних товарів
                $selectedIndices = $request->selected_indices;
                $selectedItems = $request->selected_items;

                // Створюємо нову заявку
                $newRequest = PurchaseRequest::create([
                    'user_id' => Auth::id(),
                    'description' => $request->new_description ?? 'Розділена заявка від '.$purchaseRequest->request_number,
                    'requested_date' => $request->new_requested_date,
                    'notes' => 'Розділена з заявки: '.$purchaseRequest->request_number,
                ]);

                // Копіюємо вибрані товари до нової заявки
                foreach ($selectedItems as $itemData) {
                    $newRequest->items()->create([
                        'item_name' => $itemData['item_name'],
                        'item_code' => $itemData['item_code'],
                        'quantity' => $itemData['quantity'],
                        'unit' => $itemData['unit'],
                        'estimated_price' => $itemData['estimated_price'] ?? null,
                    ]);
                }

                // Видаляємо вибрані товари з поточної заявки за індексами
                $itemsToDelete = $purchaseRequest->items()
                    ->orderBy('id')
                    ->take(PHP_INT_MAX)
                    ->get()
                    ->filter(function ($item, $index) use ($selectedIndices) {
                        return in_array($index, $selectedIndices);
                    });

                foreach ($itemsToDelete as $item) {
                    $item->delete();
                }

                // Перераховуємо суми обох заявок
                $newRequest->recalculateTotal();
                $purchaseRequest->recalculateTotal();

                // Якщо в поточній заявці не залишилося товарів, видаляємо її
                if ($purchaseRequest->items()->count() === 0) {
                    $purchaseRequest->delete();
                }
            });

            $newRequest = PurchaseRequest::latest()->first();

            return response()->json([
                'success' => true,
                'message' => 'Заявку успішно розділено',
                'new_request_number' => $newRequest->request_number,
                'new_request_id' => $newRequest->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Помилка при розділенні: '.$e->getMessage(),
            ], 500);
        }
    }

    public function receive(ReceivePurchaseRequestRequest $request, PurchaseRequest $purchaseRequest)
    {
        if (! in_array($purchaseRequest->status, ['submitted', 'approved', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Неможливо оприходувати товари з заявки в статусі '.$purchaseRequest->status,
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $purchaseRequest) {
                foreach ($request->items as $itemData) {
                    $purchaseRequestItem = PurchaseRequestItem::findOrFail($itemData['purchase_request_item_id']);

                    // Перевірити що товар належить цій заявці
                    if ($purchaseRequestItem->purchase_request_id !== $purchaseRequest->id) {
                        throw new \Exception('Товар не належить до цієї заявки');
                    }

                    $actualQuantity = $itemData['actual_quantity'];
                    $action = $itemData['action'];

                    // Визначити запис RoomInventory
                    $inventory = null;

                    if ($action === 'update_existing') {
                        // Спробуємо взяти через warehouse_item_id або пошукаємо по item_name
                        if ($purchaseRequestItem->warehouse_item_id) {
                            $inventory = RoomInventory::find($purchaseRequestItem->warehouse_item_id);
                        }

                        if (! $inventory) {
                            $inventory = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
                                ->where('equipment_type', $purchaseRequestItem->item_name)
                                ->first();
                        }

                        if (! $inventory) {
                            throw new \Exception("Товар '{$purchaseRequestItem->item_name}' не знайдено на складі");
                        }
                    } elseif ($action === 'create_new') {
                        // Створити новий запис в room_inventory
                        $inventory = RoomInventory::create([
                            'branch_id' => self::WAREHOUSE_BRANCH_ID,
                            'room_number' => 'Загальний',
                            'equipment_type' => $purchaseRequestItem->item_name,
                            'inventory_number' => 'WH-'.now()->format('YmdHis'),
                            'quantity' => 0,
                            'unit' => $purchaseRequestItem->unit,
                            'price' => $purchaseRequestItem->estimated_price ?? 0,
                            'admin_telegram_id' => Auth::user()->telegram_id ?? 0,
                        ]);
                    } elseif ($action === 'link_to_existing') {
                        // Взяти по existing_inventory_id
                        $inventory = RoomInventory::findOrFail($itemData['existing_inventory_id']);
                        // Оновити warehouse_item_id на PurchaseRequestItem
                        $purchaseRequestItem->update(['warehouse_item_id' => $inventory->id]);
                    }

                    // Збільшити quantity
                    $newBalance = $inventory->quantity + $actualQuantity;
                    $inventory->update(['quantity' => $newBalance]);

                    // Створити WarehouseMovement
                    WarehouseMovement::create([
                        'user_id' => Auth::id(),
                        'inventory_id' => $inventory->id,
                        'type' => 'receipt',
                        'quantity' => $actualQuantity,
                        'balance_after' => $newBalance,
                        'note' => "Оприходовано з заявки {$purchaseRequest->request_number}",
                        'document_number' => $purchaseRequest->request_number,
                        'operation_date' => now()->toDateString(),
                    ]);

                    // Оновити warehouse_item_id на PurchaseRequestItem (якщо не оновлено раніше)
                    if (! $purchaseRequestItem->warehouse_item_id) {
                        $purchaseRequestItem->update(['warehouse_item_id' => $inventory->id]);
                    }
                }

                // Оновити статус заявки на completed
                $purchaseRequest->update(['status' => 'completed']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Товари успішно оприходовані на склад',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Помилка при оприходуванні: '.$e->getMessage(),
            ], 500);
        }
    }
}
