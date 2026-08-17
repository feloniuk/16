<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\WarehouseMovement;
use App\Models\WriteoffRequest;
use App\Models\WriteoffRequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WriteoffRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = WriteoffRequest::with(['user', 'items'])->withCount('items')->notArchived();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('writeoff_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('writeoff_date', '<=', $request->date_to);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);
        $requests->appends($request->query());

        return view('writeoff-requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        // Видачі за обраний період (за замовчуванням — поточний місяць)
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $movements = WarehouseMovement::with('inventoryItem')
            ->whereIn('type', ['issue', 'writeoff'])
            ->whereBetween('operation_date', [$dateFrom, $dateTo])
            ->orderBy('operation_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Групуємо видачі по inventory_id для зручності вибору
        $groupedMovements = $movements->groupBy('inventory_id')->map(function ($items) {
            $first = $items->first();

            return [
                'inventory_id' => $first->inventory_id,
                'item_name' => $first->inventoryItem->full_name ?? $first->inventoryItem->equipment_type ?? '—',
                'unit' => $first->inventoryItem->unit ?? 'шт',
                'total_quantity' => $items->sum(fn ($m) => abs($m->quantity)),
                'movements' => $items,
            ];
        })->values();

        return view('writeoff-requests.create', compact('groupedMovements', 'dateFrom', 'dateTo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'writeoff_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.inventory_id' => 'nullable|integer|exists:room_inventory,id',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $writeoffRequest = WriteoffRequest::create([
                'user_id' => Auth::id(),
                'status' => 'draft',
                'description' => $request->description,
                'notes' => $request->notes,
                'writeoff_date' => $request->writeoff_date,
            ]);

            foreach ($request->items as $itemData) {
                $writeoffRequest->items()->create([
                    'inventory_id' => $itemData['inventory_id'] ?: null,
                    'item_name' => $itemData['item_name'],
                    'unit' => $itemData['unit'],
                    'quantity' => $itemData['quantity'],
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('writeoff-requests.index')->with('success', 'Заявку на списання створено');
    }

    public function show(WriteoffRequest $writeoffRequest)
    {
        $writeoffRequest->load(['items.inventoryItem', 'user']);

        return view('writeoff-requests.show', compact('writeoffRequest'));
    }

    public function edit(WriteoffRequest $writeoffRequest)
    {
        if (! in_array($writeoffRequest->status, ['draft', 'submitted'])) {
            return redirect()->route('writeoff-requests.show', $writeoffRequest)
                ->withErrors(['Редагування доступне лише для чернеток та поданих заявок']);
        }

        $writeoffRequest->load('items');

        $dateFrom = now()->startOfMonth()->toDateString();
        $dateTo = now()->toDateString();

        $groupedMovements = $this->groupedMovements($dateFrom, $dateTo);

        return view('writeoff-requests.edit', compact('writeoffRequest', 'groupedMovements', 'dateFrom', 'dateTo'));
    }

    public function movements(Request $request, WriteoffRequest $writeoffRequest): JsonResponse
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        return response()->json([
            'movements' => $this->groupedMovements($dateFrom, $dateTo)->values(),
        ]);
    }

    private function groupedMovements(string $dateFrom, string $dateTo)
    {
        $movements = WarehouseMovement::with('inventoryItem')
            ->whereIn('type', ['issue', 'writeoff'])
            ->whereDate('operation_date', '>=', $dateFrom)
            ->whereDate('operation_date', '<=', $dateTo)
            ->orderBy('operation_date', 'desc')
            ->get();

        return $movements->groupBy('inventory_id')->map(function ($items) {
            $first = $items->first();

            return [
                'inventory_id' => $first->inventory_id,
                'item_name' => $first->inventoryItem->full_name ?? $first->inventoryItem->equipment_type ?? '—',
                'unit' => $first->inventoryItem->unit ?? 'шт',
                'total_quantity' => $items->sum(fn ($m) => abs($m->quantity)),
            ];
        })->values();
    }

    public function update(Request $request, WriteoffRequest $writeoffRequest): RedirectResponse
    {
        if (! in_array($writeoffRequest->status, ['draft', 'submitted'])) {
            return redirect()->route('writeoff-requests.show', $writeoffRequest)
                ->withErrors(['Редагування доступне лише для чернеток та поданих заявок']);
        }

        $request->validate([
            'writeoff_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer',
            'items.*.item_name' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:20',
            'items.*.inventory_id' => 'nullable|integer|exists:room_inventory,id',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $writeoffRequest) {
            $writeoffRequest->update([
                'description' => $request->description,
                'notes' => $request->notes,
                'writeoff_date' => $request->writeoff_date,
            ]);

            $keptIds = collect($request->items)
                ->filter(fn ($item) => ! empty($item['id']))
                ->pluck('id')
                ->toArray();

            $writeoffRequest->items()->whereNotIn('id', $keptIds)->delete();

            foreach ($request->items as $itemData) {
                $data = [
                    'inventory_id' => $itemData['inventory_id'] ?: null,
                    'item_name' => $itemData['item_name'],
                    'unit' => $itemData['unit'],
                    'quantity' => $itemData['quantity'],
                    'notes' => $itemData['notes'] ?? null,
                ];

                if (! empty($itemData['id'])) {
                    WriteoffRequestItem::where('id', $itemData['id'])->update($data);
                } else {
                    $writeoffRequest->items()->create($data);
                }
            }
        });

        return redirect()->route('writeoff-requests.show', $writeoffRequest)->with('success', 'Заявку оновлено');
    }

    public function submit(WriteoffRequest $writeoffRequest): RedirectResponse
    {
        if ($writeoffRequest->status !== 'draft') {
            return redirect()->back()->withErrors(['Подати можна лише чернетку']);
        }

        $writeoffRequest->update(['status' => 'submitted']);

        return redirect()->route('writeoff-requests.show', $writeoffRequest)->with('success', 'Заявку подано на затвердження');
    }

    public function approve(WriteoffRequest $writeoffRequest): RedirectResponse
    {
        if ($writeoffRequest->status !== 'submitted') {
            return redirect()->back()->withErrors(['Підтвердити можна лише подану заявку']);
        }

        if (! in_array(Auth::user()->role, ['admin', 'director'])) {
            return redirect()->back()->withErrors(['Тільки адмін або директор можуть затвердити заявку']);
        }

        $writeoffRequest->update(['status' => 'approved']);

        return redirect()->route('writeoff-requests.show', $writeoffRequest)->with('success', 'Заявку затверджено');
    }

    public function complete(WriteoffRequest $writeoffRequest): RedirectResponse
    {
        if ($writeoffRequest->status !== 'approved') {
            return redirect()->back()->withErrors(['Завершити можна лише затверджену заявку']);
        }

        if (! in_array(Auth::user()->role, ['admin', 'warehouse_keeper'])) {
            return redirect()->back()->withErrors(['Тільки адмін або складовщик можуть завершити заявку']);
        }

        $writeoffRequest->update(['status' => 'completed']);

        return redirect()->route('writeoff-requests.show', $writeoffRequest)->with('success', 'Заявку завершено');
    }

    public function reject(WriteoffRequest $writeoffRequest): RedirectResponse
    {
        if (! in_array($writeoffRequest->status, ['submitted', 'approved'])) {
            return redirect()->back()->withErrors(['Відхилити можна лише подану або затверджену заявку']);
        }

        if (! in_array(Auth::user()->role, ['admin', 'director'])) {
            return redirect()->back()->withErrors(['Тільки адмін або директор можуть відхилити заявку']);
        }

        $writeoffRequest->update(['status' => 'draft']);

        return redirect()->route('writeoff-requests.show', $writeoffRequest)->with('success', 'Заявку повернено до чернетки');
    }

    public function print(WriteoffRequest $writeoffRequest)
    {
        $writeoffRequest->load(['items.inventoryItem', 'user']);

        $deputyDirector = AppSetting::get('print_deputy_director', 'Олександр ХРОМЧЕНКО');
        $director = AppSetting::get('print_director', 'Ганна ПАВЛЕГА');

        return view('writeoff-requests.print', compact('writeoffRequest', 'deputyDirector', 'director'));
    }

    public function archive(WriteoffRequest $writeoffRequest): RedirectResponse
    {
        $writeoffRequest->update(['archived_at' => now()]);

        return redirect()->route('writeoff-requests.index')->with('success', 'Заявку архівовано');
    }

    public function destroy(WriteoffRequest $writeoffRequest): RedirectResponse
    {
        if ($writeoffRequest->status !== 'draft') {
            return redirect()->back()->withErrors(['Видалити можна лише чернетку']);
        }

        $writeoffRequest->delete();

        return redirect()->route('writeoff-requests.index')->with('success', 'Заявку видалено');
    }
}
