<?php

// app/Http/Controllers/WarehouseController.php

namespace App\Http\Controllers;

use App\Models\RoomInventory;
use App\Models\WarehouseMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    // ID філії "Склад"
    const WAREHOUSE_BRANCH_ID = 6;

    public function index(Request $request)
    {
        // Згрупований вигляд по найменуванню (за замовчуванням)
        $query = RoomInventory::select(
            'equipment_type',
            DB::raw('GROUP_CONCAT(DISTINCT id) as item_ids'),
            DB::raw('COUNT(*) as items_count'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('MAX(category) as category'),
            DB::raw('MAX(unit) as unit'),
            DB::raw('AVG(price) as avg_price'),
            DB::raw('MIN(min_quantity) as min_quantity')
        )
            ->where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->groupBy('equipment_type');

        if ($request->filled('category')) {
            $query->having('category', $request->category);
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

        $items = $query->orderBy('equipment_type')->paginate(20);
        $items->appends($request->query());

        // Категорії товарів складу
        $categories = config('warehouse-categories');

        // Кількість найменувань з низьким залишком
        $lowStockCount = RoomInventory::select('equipment_type')
            ->where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->groupBy('equipment_type')
            ->havingRaw('SUM(quantity) <= MIN(min_quantity)')
            ->get()
            ->count();

        return view('warehouse.index', compact('items', 'categories', 'lowStockCount'));
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

        return view('warehouse.show', compact('item'));
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
            'quantity' => 'required|integer|min:1|max:'.$item->quantity,
            'note' => 'nullable|string|max:500',
            'issued_to' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $item) {
            $newBalance = $item->quantity - $request->quantity;

            $item->update(['quantity' => $newBalance]);

            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'inventory_id' => $item->id,
                'type' => 'issue',
                'quantity' => -$request->quantity,
                'balance_after' => $newBalance,
                'note' => $request->note.($request->issued_to ? " (Видано: {$request->issued_to})" : ''),
                'operation_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('warehouse.show', $item)->with('success', 'Видачу зафіксовано');
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

        // Отримуємо загальну кількість товару
        $totalAvailable = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
            ->where('equipment_type', $equipmentType)
            ->sum('quantity');

        if ($totalAvailable < $quantityToIssue) {
            return back()->with('error', "Недостатньо товару на складі. Доступно: {$totalAvailable}");
        }

        DB::transaction(function () use ($request, $equipmentType, $quantityToIssue) {
            $remaining = $quantityToIssue;

            // Отримуємо записи з цим найменуванням, у яких є залишок
            $items = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)
                ->where('equipment_type', $equipmentType)
                ->where('quantity', '>', 0)
                ->orderBy('quantity', 'desc') // Спочатку з більшим залишком
                ->get();

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
                    'quantity' => $issueFromThis,
                    'balance_after' => $newBalance,
                    'note' => $request->note.($request->issued_to ? " | Кому: {$request->issued_to}" : ''),
                    'operation_date' => now()->toDateString(),
                ]);

                $remaining -= $issueFromThis;
            }
        });

        return redirect()->route('warehouse.index')->with('success', "Видано {$quantityToIssue} од. товару \"{$equipmentType}\"");
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

        return redirect()->route('warehouse.index')->with('success', "Надходження {$request->quantity} од. товару \"{$equipmentType}\" зафіксовано");
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
