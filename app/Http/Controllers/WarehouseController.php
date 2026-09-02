<?php

// app/Http/Controllers/WarehouseController.php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RoomInventory;
use App\Models\WarehouseMovement;
use App\Services\WarehouseIssueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    // ID філії "Склад"
    const WAREHOUSE_BRANCH_ID = 6;

    public function index(Request $request)
    {
        // Категорії товарів складу
        $categories = config('warehouse-categories');

        // По умолчанию фільтруємо по першій категорії (Господарчі Товари)
        $activeCategory = $request->get('category', $categories[0] ?? null);

        // Згрупований вигляд по найменуванню (за замовчуванням)
        $query = RoomInventory::select(
            'equipment_type',
            DB::raw('GROUP_CONCAT(DISTINCT id) as item_ids'),
            DB::raw('COUNT(*) as items_count'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('MAX(category) as category'),
            DB::raw('MAX(unit) as unit'),
            DB::raw('AVG(price) as avg_price'),
            DB::raw('MIN(min_quantity) as min_quantity'),
            DB::raw('MAX(is_priority) as is_priority')
        )
            ->where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->groupBy('equipment_type');

        // Фільтруємо по категорії, але не якщо вибрано "Всі"
        if ($activeCategory && $activeCategory !== 'all') {
            $query->having('category', $activeCategory);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('equipment_type', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->havingRaw('SUM(quantity) <= MIN(min_quantity)');
        }

        if ($request->filled('hide_zero_stock')) {
            $query->havingRaw('SUM(quantity) > 0');
        }

        $items = $query->orderByDesc('is_priority')->orderBy('equipment_type')->paginate(20);
        $items->appends($request->query());

        // Кількість найменувань з низьким залишком
        $lowStockQuery = RoomInventory::select('equipment_type')
            ->where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->groupBy('equipment_type');

        if ($activeCategory && $activeCategory !== 'all') {
            $lowStockQuery->where('category', $activeCategory);
        }

        $lowStockCount = $lowStockQuery
            ->havingRaw('SUM(quantity) <= MIN(min_quantity)')
            ->get()
            ->count();

        $branches = Branch::where('is_active', true)->where('id', '!=', self::WAREHOUSE_BRANCH_ID)->orderBy('name')->get();

        return view('warehouse.index', compact('items', 'categories', 'lowStockCount', 'activeCategory', 'branches'));
    }

    public function togglePriority(Request $request)
    {
        $request->validate([
            'equipment_type' => 'required|string',
            'is_priority' => 'required|boolean',
        ]);

        RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->where('equipment_type', $request->equipment_type)
            ->update(['is_priority' => $request->boolean('is_priority')]);

        return response()->json(['success' => true]);
    }

    public function show(RoomInventory $item)
    {
        // Перевіряємо чи це складський товар
        if ($item->branch_id != self::WAREHOUSE_BRANCH_ID) {
            abort(404, 'Це не складський товар');
        }

        $item->load(['movements' => function ($query) {
            $query->with(['user'])->orderBy('created_at', 'desc')->limit(20);
        }]);

        $branches = Branch::where('is_active', true)->where('id', '!=', self::WAREHOUSE_BRANCH_ID)->orderBy('name')->get();

        return view('warehouse.show', compact('item', 'branches'));
    }

    public function create()
    {
        $categories = config('warehouse-categories');

        return view('warehouse.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $categories = config('warehouse-categories');

        $request->validate([
            'equipment_type' => 'required|string|max:255',
            'full_name' => 'nullable|string',
            'inventory_number' => 'required|string|max:255|unique:room_inventory,inventory_number',
            'notes' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|in:'.implode(',', $categories),
        ]);

        $item = RoomInventory::create([
            'branch_id' => self::WAREHOUSE_BRANCH_ID,
            'room_number' => $request->category ?? 'Загальний',
            'equipment_type' => $request->equipment_type,
            'full_name' => $request->full_name,
            'inventory_number' => $request->inventory_number,
            'notes' => $request->notes,
            'unit' => $request->unit,
            'quantity' => $request->quantity,
            'min_quantity' => $request->min_quantity,
            'price' => $request->price,
            'category' => $request->category,
            'admin_telegram_id' => Auth::user()->telegram_id ?? 0,
        ]);

        // Створюємо рух для початкового залишку
        if ($item->quantity > 0) {
            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'receipt',
                'quantity' => $item->quantity,
                'balance_after' => $item->quantity,
                'note' => 'Початковий залишок',
                'operation_date' => now()->toDateString(),
            ]);
        }

        return redirect()->route('warehouse.index')->with('success', 'Товар додано успішно');
    }

    public function edit(RoomInventory $item)
    {
        if ($item->branch_id != self::WAREHOUSE_BRANCH_ID) {
            abort(404, 'Це не складський товар');
        }

        $categories = config('warehouse-categories');

        return view('warehouse.edit', compact('item', 'categories'));
    }

    public function update(Request $request, RoomInventory $item)
    {
        if ($item->branch_id != self::WAREHOUSE_BRANCH_ID) {
            abort(404, 'Це не складський товар');
        }

        $categories = config('warehouse-categories');

        $request->validate([
            'equipment_type' => 'required|string|max:255',
            'full_name' => 'nullable|string',
            'inventory_number' => 'required|string|max:255|unique:room_inventory,inventory_number,'.$item->id,
            'notes' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|in:'.implode(',', $categories),
        ]);

        $item->update([
            'equipment_type' => $request->equipment_type,
            'full_name' => $request->full_name,
            'inventory_number' => $request->inventory_number,
            'notes' => $request->notes,
            'unit' => $request->unit,
            'min_quantity' => $request->min_quantity,
            'price' => $request->price,
            'category' => $request->category,
            'room_number' => $request->category ?? 'Загальний',
        ]);

        return redirect()->route('warehouse.show', $item)->with('success', 'Товар оновлено');
    }

    public function receipt(Request $request, RoomInventory $item)
    {
        if ($item->branch_id != self::WAREHOUSE_BRANCH_ID) {
            abort(404, 'Це не складський товар');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
            'document_number' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $item) {
            $newBalance = $item->quantity + $request->quantity;

            $item->update(['quantity' => $newBalance]);

            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'receipt',
                'quantity' => $request->quantity,
                'balance_after' => $newBalance,
                'note' => $request->note,
                'document_number' => $request->document_number,
                'operation_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('warehouse.show', $item)->with('success', 'Надходження зафіксовано');
    }

    public function issue(Request $request, RoomInventory $item)
    {
        if ($item->branch_id != self::WAREHOUSE_BRANCH_ID) {
            abort(404, 'Це не складський товар');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
            'issued_to' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $item) {
                $locked = RoomInventory::where('id', $item->id)->lockForUpdate()->firstOrFail();

                if ($locked->quantity < $request->quantity) {
                    throw new \RuntimeException("Недостатньо товару на складі. Залишок: {$locked->quantity} {$locked->unit}");
                }

                $newBalance = $locked->quantity - $request->quantity;
                $locked->update(['quantity' => $newBalance]);

                WarehouseMovement::create([
                    'user_id' => Auth::id(),
                    'inventory_id' => $locked->id,
                    'type' => 'issue',
                    'quantity' => -$request->quantity,
                    'balance_after' => $newBalance,
                    'note' => $request->note.($request->issued_to ? " (Видано: {$request->issued_to})" : ''),
                    'operation_date' => now()->toDateString(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        return redirect()->route('warehouse.show', $item)->with('success', 'Видачу зафіксовано');
    }

    public function issueBatch(Request $request, WarehouseIssueService $issueService)
    {
        $request->validate([
            'destination_branch_id' => 'required|exists:branches,id',
            'destination_room_number' => 'required|string|max:50',
            'issued_to' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|integer|exists:room_inventory,id',
            'items.*.quantity' => 'required|integer|min:1',
            'replace_old_item_id' => 'nullable|integer|exists:room_inventory,id',
            'replace_action' => 'nullable|in:repair,transfer,warehouse',
            'replace_to_branch_id' => 'required_if:replace_action,transfer|exists:branches,id',
            'replace_to_room_number' => 'required_if:replace_action,transfer|string|max:50',
            'replace_note' => 'nullable|string|max:500',
        ]);

        $contextNote = "Видача в кабінет {$request->destination_room_number}".
            ($request->issued_to ? " (Видано: {$request->issued_to})" : '');

        $replacement = null;
        if ($request->filled('replace_old_item_id') && $request->filled('replace_action')) {
            $replacement = [
                'old_inventory_id' => (int) $request->replace_old_item_id,
                'action' => $request->replace_action,
                'to_branch_id' => $request->replace_to_branch_id,
                'to_room_number' => $request->replace_to_room_number,
                'note' => $request->replace_note,
            ];
        }

        try {
            $issueService->issueBatch(
                $request->items,
                ['note' => $contextNote],
                $replacement,
                Auth::user()
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['items' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Видачу зафіксовано');
    }

    public function roomEquipment(Request $request): JsonResponse
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:50',
        ]);

        $items = RoomInventory::where('branch_id', $request->branch_id)
            ->where('room_number', $request->room_number)
            ->get(['id', 'equipment_type', 'full_name', 'inventory_number']);

        return response()->json($items);
    }

    public function movements(Request $request)
    {
        $query = WarehouseMovement::with(['user', 'inventoryItem']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('operation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('operation_date', '<=', $request->date_to);
        }

        if ($request->filled('item_name')) {
            $itemName = $request->item_name;
            $query->whereHas('inventoryItem', function ($q) use ($itemName) {
                $q->where('equipment_type', 'like', "%{$itemName}%")
                    ->orWhere('full_name', 'like', "%{$itemName}%");
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        $movements->appends($request->query());

        return view('warehouse.movements', compact('movements'));
    }

    /**
     * Видача товару по найменуванню (списує з доступних записів)
     */
    public function issueByName(Request $request)
    {
        $request->validate([
            'equipment_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
            'issued_to' => 'nullable|string|max:255',
        ]);

        $equipmentType = $request->equipment_type;
        $quantityToIssue = $request->quantity;

        try {
            DB::transaction(function () use ($request, $equipmentType, $quantityToIssue) {
                // Блокуємо всі записи цього найменування, щоб уникнути гонки при одночасній видачі
                $items = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
                    ->where('equipment_type', $equipmentType)
                    ->where('quantity', '>', 0)
                    ->orderBy('quantity', 'desc') // Спочатку з більшим залишком
                    ->lockForUpdate()
                    ->get();

                $totalAvailable = $items->sum('quantity');

                if ($totalAvailable < $quantityToIssue) {
                    throw new \RuntimeException("Недостатньо товару на складі. Доступно: {$totalAvailable}");
                }

                $remaining = $quantityToIssue;

                foreach ($items as $item) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $issueFromThis = min($remaining, $item->quantity);
                    $newBalance = $item->quantity - $issueFromThis;

                    $item->update(['quantity' => $newBalance]);

                    WarehouseMovement::create([
                        'user_id' => Auth::id(),
                        'inventory_id' => $item->id,
                        'type' => 'issue',
                        'quantity' => -$issueFromThis,
                        'balance_after' => $newBalance,
                        'note' => $request->note.($request->issued_to ? " | Кому: {$request->issued_to}" : ''),
                        'operation_date' => now()->toDateString(),
                    ]);

                    $remaining -= $issueFromThis;
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Видано {$quantityToIssue} од. товару \"{$equipmentType}\"");
    }

    /**
     * Надходження товару по найменуванню (додає до першого запису або створює новий)
     */
    public function receiptByName(Request $request)
    {
        $request->validate([
            'equipment_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
            'document_number' => 'nullable|string|max:255',
        ]);

        $equipmentType = $request->equipment_type;

        DB::transaction(function () use ($request, $equipmentType) {
            // Знаходимо перший запис з цим найменуванням
            $item = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
                ->where('equipment_type', $equipmentType)
                ->first();

            if ($item) {
                // Додаємо до існуючого запису
                $newBalance = $item->quantity + $request->quantity;
                $item->update(['quantity' => $newBalance]);

                WarehouseMovement::create([
                    'user_id' => Auth::id(),
                    'inventory_id' => $item->id,
                    'type' => 'receipt',
                    'quantity' => $request->quantity,
                    'balance_after' => $newBalance,
                    'note' => $request->note,
                    'document_number' => $request->document_number,
                    'operation_date' => now()->toDateString(),
                ]);
            } else {
                // Створюємо новий запис
                $newItem = RoomInventory::create([
                    'branch_id' => self::WAREHOUSE_BRANCH_ID,
                    'room_number' => 'Загальний',
                    'equipment_type' => $equipmentType,
                    'inventory_number' => 'WH-'.now()->format('YmdHis'),
                    'quantity' => $request->quantity,
                    'unit' => 'шт',
                    'admin_telegram_id' => Auth::user()->telegram_id ?? 0,
                ]);

                WarehouseMovement::create([
                    'user_id' => Auth::id(),
                    'inventory_id' => $newItem->id,
                    'type' => 'receipt',
                    'quantity' => $request->quantity,
                    'balance_after' => $request->quantity,
                    'note' => $request->note ?? 'Нове надходження',
                    'document_number' => $request->document_number,
                    'operation_date' => now()->toDateString(),
                ]);
            }
        });

        return back()->with('success', "Надходження {$request->quantity} од. товару \"{$equipmentType}\" зафіксовано");
    }

    /**
     * Показати всі записи по конкретному найменуванню
     */
    public function showByName(Request $request)
    {
        $equipmentType = $request->query('name');

        if (! $equipmentType) {
            return redirect()->route('warehouse.index');
        }

        $items = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->where('equipment_type', $equipmentType)
            ->orderBy('quantity', 'desc')
            ->get();

        $totalQuantity = $items->sum('quantity');

        return view('warehouse.show-by-name', compact('items', 'equipmentType', 'totalQuantity'));
    }
}
