<?php
namespace App\Http\Controllers;

use App\Models\WarehouseItem;
use App\Models\WarehouseMovement;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{

    public function index(Request $request)
    {
        $query = WarehouseItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->lowStock();
        }

        $items = $query->orderBy('name')->paginate(20);
        $categories = WarehouseItem::distinct()->pluck('category')->filter();
        $lowStockCount = WarehouseItem::lowStock()->count();

        return view('warehouse.index', compact('items', 'categories', 'lowStockCount'));
    }

    public function show(WarehouseItem $item)
    {
        $item->load(['movements' => function($query) {
            $query->with(['user', 'issuedToUser'])->orderBy('created_at', 'desc')->limit(20);
        }]);

        return view('warehouse.show', compact('item'));
    }

    public function create()
    {
        $categories = WarehouseItem::distinct()->pluck('category')->filter();
        return view('warehouse.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:warehouse_items,code',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ]);

        $item = WarehouseItem::create($request->all());

        // Создаем движение для начального остатка
        if ($item->quantity > 0) {
            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'warehouse_item_id' => $item->id,
                'type' => 'receipt',
                'quantity' => $item->quantity,
                'balance_after' => $item->quantity,
                'note' => 'Початковий залишок',
                'operation_date' => now()->toDateString(),
            ]);
        }

        return redirect()->route('warehouse.index')->with('success', 'Товар додано успішно');
    }

    public function edit(WarehouseItem $item)
    {
        $categories = WarehouseItem::distinct()->pluck('category')->filter();
        return view('warehouse.edit', compact('item', 'categories'));
    }

    public function update(Request $request, WarehouseItem $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:warehouse_items,code,' . $item->id,
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $item->update($request->all());

        return redirect()->route('warehouse.show', $item)->with('success', 'Товар оновлено');
    }

    public function receipt(Request $request, WarehouseItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:500',
            'document_number' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $item) {
            $newBalance = $item->quantity + $request->quantity;
            
            $item->update(['quantity' => $newBalance]);

            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'warehouse_item_id' => $item->id,
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

    public function issue(Request $request, WarehouseItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->quantity,
            'note' => 'nullable|string|max:500',
            'issued_to' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($request, $item) {
            $newBalance = $item->quantity - $request->quantity;
            
            $item->update(['quantity' => $newBalance]);

            WarehouseMovement::create([
                'user_id' => Auth::id(),
                'warehouse_item_id' => $item->id,
                'type' => 'issue',
                'quantity' => -$request->quantity, // отрицательное количество для выдачи
                'balance_after' => $newBalance,
                'note' => $request->note . ($request->issued_to ? " (Видано: {$request->issued_to})" : ''),
                'operation_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('warehouse.show', $item)->with('success', 'Видачу зафіксовано');
    }

    public function movements(Request $request)
    {
        $query = WarehouseMovement::with(['user', 'warehouseItem', 'issuedToUser']);

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

        return view('warehouse.movements', compact('movements'));
    }
}