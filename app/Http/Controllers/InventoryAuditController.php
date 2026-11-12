<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyInventoryAuditRequest;
use App\Http\Requests\SaveInventoryAuditRoomRequest;
use App\Models\Branch;
use App\Models\Cabinet;
use App\Models\InventoryAudit;
use App\Models\InventoryAuditRoom;
use App\Models\MedicalStaff;
use App\Models\RoomInventory;
use App\Services\InventoryAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventoryAuditController extends Controller
{
    public function __construct(private InventoryAuditService $audits) {}

    public function index(): View
    {
        $audits = InventoryAudit::query()
            ->with('user')
            ->withCount(['rooms', 'items'])
            ->latest()
            ->paginate(20);

        return view('inventory-audits.index', compact('audits'));
    }

    public function store(): RedirectResponse
    {
        $audit = InventoryAudit::create([
            'user_id' => Auth::id(),
            'title' => 'Обхід від '.now()->format('d.m.Y'),
            'status' => InventoryAudit::STATUS_IN_PROGRESS,
        ]);

        return redirect()->route('inventory-audits.show', $audit);
    }

    public function show(InventoryAudit $inventoryAudit): View|RedirectResponse
    {
        if ($inventoryAudit->isApplied()) {
            return redirect()->route('inventory-audits.review', $inventoryAudit);
        }

        $inventoryAudit->load(['rooms' => fn ($query) => $query->with('branch')->withCount('items')->latest('updated_at')]);

        $branches = Branch::query()->where('is_active', true)->orderBy('name')->get();

        $roomsByBranch = [];
        foreach (Cabinet::query()->get(['branch_id', 'number']) as $cabinet) {
            $roomsByBranch[$cabinet->branch_id][$cabinet->number] = true;
        }
        $knownRooms = RoomInventory::query()
            ->whereNotNull('room_number')
            ->distinct()
            ->get(['branch_id', 'room_number']);
        foreach ($knownRooms as $location) {
            $roomsByBranch[$location->branch_id][$location->room_number] = true;
        }
        $roomsByBranch = array_map(fn (array $rooms): array => array_map('strval', array_keys($rooms)), $roomsByBranch);

        $equipmentTypes = RoomInventory::query()
            ->equipment()
            ->distinct()
            ->orderBy('equipment_type')
            ->limit(1000)
            ->pluck('equipment_type');

        $staff = MedicalStaff::query()->active()->orderBy('last_name')->get();
        $doctorNames = $staff->where('type', MedicalStaff::TYPE_DOCTOR)->map->full_name->values();
        $nurseNames = $staff->where('type', MedicalStaff::TYPE_NURSE)->map->full_name->values();

        return view('inventory-audits.show', [
            'audit' => $inventoryAudit,
            'branches' => $branches,
            'roomsByBranch' => $roomsByBranch,
            'equipmentTypes' => $equipmentTypes,
            'doctorNames' => $doctorNames,
            'nurseNames' => $nurseNames,
            'warehouseBranchId' => InventoryAuditService::WAREHOUSE_BRANCH_ID,
        ]);
    }

    /**
     * Дані кабінету для форми обходу: що числиться в системі, що вже
     * збережено в чернетці та хто закріплений за кабінетом.
     */
    public function roomData(Request $request, InventoryAudit $inventoryAudit): JsonResponse
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:50',
        ]);

        $branchId = (int) $request->input('branch_id');
        $roomNumber = trim((string) $request->input('room_number'));

        $records = RoomInventory::query()
            ->where('branch_id', $branchId)
            ->where('room_number', $roomNumber)
            ->orderBy('equipment_type')
            ->orderBy('id')
            ->get(['id', 'equipment_type', 'brand', 'model', 'serial_number', 'inventory_number', 'quantity']);

        $saved = $inventoryAudit->rooms()
            ->with('items')
            ->where('branch_id', $branchId)
            ->where('room_number', $roomNumber)
            ->first();

        $cabinet = Cabinet::query()
            ->with(['shifts.doctor', 'shifts.nurse'])
            ->where('branch_id', $branchId)
            ->where('number', $roomNumber)
            ->first();

        $staff = [];
        foreach ($cabinet?->shifts ?? [] as $shift) {
            $staff[$shift->shift] = [
                'doctor' => $shift->doctor?->full_name ?? '',
                'nurse' => $shift->nurse?->full_name ?? '',
            ];
        }

        return response()->json([
            'records' => $records,
            'saved' => $saved ? [
                'notes' => $saved->notes,
                'items' => $saved->items->map->only([
                    'room_inventory_id', 'equipment_type', 'brand', 'model', 'serial_number', 'inventory_number', 'quantity', 'notes',
                ])->values(),
            ] : null,
            'staff' => $staff,
        ]);
    }

    public function saveRoom(SaveInventoryAuditRoomRequest $request, InventoryAudit $inventoryAudit): RedirectResponse
    {
        if ($inventoryAudit->isApplied()) {
            return redirect()->route('inventory-audits.review', $inventoryAudit)
                ->withErrors(['Обхід уже застосовано — редагування неможливе']);
        }

        $room = $this->audits->saveRoom($inventoryAudit, $request->validated());

        return redirect()
            ->route('inventory-audits.show', ['inventoryAudit' => $inventoryAudit, 'branch_id' => $room->branch_id])
            ->with('success', "Кабінет {$room->room_number} збережено ({$room->items()->count()} поз.)");
    }

    public function destroyRoom(InventoryAudit $inventoryAudit, InventoryAuditRoom $room): RedirectResponse
    {
        abort_unless($room->inventory_audit_id === $inventoryAudit->id, 404);

        if ($inventoryAudit->isApplied()) {
            return redirect()->route('inventory-audits.review', $inventoryAudit)
                ->withErrors(['Обхід уже застосовано — редагування неможливе']);
        }

        $room->delete();

        return redirect()->route('inventory-audits.show', $inventoryAudit)
            ->with('success', "Кабінет {$room->room_number} прибрано з обходу");
    }

    public function review(InventoryAudit $inventoryAudit): View
    {
        if ($inventoryAudit->isApplied()) {
            $inventoryAudit->load(['user', 'rooms' => fn ($query) => $query
                ->with(['branch', 'items.roomInventory'])
                ->orderBy('branch_id')
                ->orderBy('room_number')]);

            return view('inventory-audits.result', ['audit' => $inventoryAudit]);
        }

        return view('inventory-audits.review', [
            'audit' => $inventoryAudit,
            'comparison' => $this->audits->compare($inventoryAudit),
        ]);
    }

    public function apply(ApplyInventoryAuditRequest $request, InventoryAudit $inventoryAudit): RedirectResponse
    {
        if ($inventoryAudit->isApplied()) {
            return redirect()->route('inventory-audits.review', $inventoryAudit)
                ->withErrors(['Обхід уже застосовано']);
        }

        if (! $inventoryAudit->rooms()->exists()) {
            return redirect()->route('inventory-audits.show', $inventoryAudit)
                ->withErrors(['В обході ще немає жодного кабінету']);
        }

        $summary = $this->audits->apply($inventoryAudit, $request->decisions(), $request->user());

        return redirect()->route('inventory-audits.review', $inventoryAudit)->with(
            'success',
            "Обхід застосовано: підтверджено {$summary['confirmed']}, оновлено {$summary['updated']}, ".
            "переміщено {$summary['moved']}, створено {$summary['created']}, ".
            "обнулено {$summary['removed']}, пропущено {$summary['skipped']}"
        );
    }

    public function destroy(InventoryAudit $inventoryAudit): RedirectResponse
    {
        if ($inventoryAudit->isApplied()) {
            return redirect()->route('inventory-audits.index')
                ->withErrors(['Застосований обхід видалити неможливо']);
        }

        $inventoryAudit->delete();

        return redirect()->route('inventory-audits.index')->with('success', 'Чернетку обходу видалено');
    }
}
