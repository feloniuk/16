<?php

namespace App\Services;

use App\Models\Cabinet;
use App\Models\CabinetShift;
use App\Models\InventoryAudit;
use App\Models\InventoryAuditItem;
use App\Models\InventoryAuditRoom;
use App\Models\InventoryTransfer;
use App\Models\MedicalStaff;
use App\Models\RoomInventory;
use App\Models\User;
use App\Models\WarehouseMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Обхід кабінетів: чернетка фактичної наявності, звірка з room_inventory
 * та застосування підтверджених змін. До виклику apply() записи обладнання
 * і складу не змінюються.
 */
class InventoryAuditService
{
    public const WAREHOUSE_BRANCH_ID = 6;

    public const TYPE_OK = 'ok';

    public const TYPE_CHANGED = 'changed';

    public const TYPE_MOVED = 'moved';

    public const TYPE_NEW = 'new';

    public const ACTION_CONFIRM = 'confirm';

    public const ACTION_UPDATE = 'update';

    public const ACTION_MOVE = 'move';

    public const ACTION_CREATE = 'create';

    public const ACTION_SKIP = 'skip';

    public const ACTION_KEEP = 'keep';

    public const ACTION_REMOVE = 'remove';

    public const ACTION_MATCH_PREFIX = 'match:';

    private const COMPARED_FIELDS = ['brand', 'model', 'serial_number', 'inventory_number'];

    private const DEFAULT_POSITIONS = [
        MedicalStaff::TYPE_DOCTOR => 'лікар',
        MedicalStaff::TYPE_NURSE => 'сестра медична',
    ];

    /**
     * Зберігає один кабінет обходу: довідники кабінетів і медперсоналу
     * оновлюються одразу, знайдене обладнання лягає лише в чернетку.
     *
     * @param array{
     *     branch_id: int|string,
     *     room_number: string,
     *     notes?: string|null,
     *     staff?: array<int|string, array{doctor?: string|null, nurse?: string|null}>|null,
     *     items?: array<int|string, array<string, mixed>>|null
     * } $data
     */
    public function saveRoom(InventoryAudit $audit, array $data): InventoryAuditRoom
    {
        return DB::transaction(function () use ($audit, $data): InventoryAuditRoom {
            $branchId = (int) $data['branch_id'];
            $roomNumber = $this->squish((string) $data['room_number']);

            $cabinet = $branchId === self::WAREHOUSE_BRANCH_ID
                ? null
                : $this->syncCabinet($branchId, $roomNumber, $data['staff'] ?? null);

            $room = $audit->rooms()->updateOrCreate(
                ['branch_id' => $branchId, 'room_number' => $roomNumber],
                ['cabinet_id' => $cabinet?->id, 'notes' => $data['notes'] ?? null],
            );

            $room->items()->delete();

            $recordsInRoom = RoomInventory::query()
                ->where('branch_id', $branchId)
                ->where('room_number', $roomNumber)
                ->get()
                ->keyBy('id');

            $usedRecordIds = [];

            foreach ($data['items'] ?? [] as $itemData) {
                $recordId = (int) ($itemData['room_inventory_id'] ?? 0);
                $record = isset($usedRecordIds[$recordId]) ? null : $recordsInRoom->get($recordId);
                $name = $this->squish((string) ($itemData['equipment_type'] ?? '')) ?: $record?->equipment_type;

                if (! $name) {
                    continue;
                }

                if ($record) {
                    $usedRecordIds[$record->id] = true;
                }

                $room->items()->create([
                    'room_inventory_id' => $record?->id,
                    'equipment_type' => $name,
                    'brand' => $this->filled($itemData['brand'] ?? null) ?? $record?->brand,
                    'model' => $this->filled($itemData['model'] ?? null) ?? $record?->model,
                    'serial_number' => $this->filled($itemData['serial_number'] ?? null) ?? $record?->serial_number,
                    'inventory_number' => $this->filled($itemData['inventory_number'] ?? null) ?? $record?->inventory_number,
                    'quantity' => max(0, (int) ($itemData['quantity'] ?? 1)),
                    'notes' => $this->filled($itemData['notes'] ?? null),
                ]);
            }

            return $room;
        });
    }

    /**
     * Звіряє чернетку обходу з поточними записами room_inventory.
     *
     * @return array{
     *     rooms: array<int, array{
     *         room: InventoryAuditRoom,
     *         rows: array<int, array{
     *             key: string,
     *             type: string,
     *             item: InventoryAuditItem,
     *             record: RoomInventory|null,
     *             changes: array<string, array{old: mixed, new: mixed}>,
     *             actions: array<int, string>,
     *             default: string
     *         }>,
     *         missing: array<int, array{key: string, record: RoomInventory}>,
     *         moved_away: array<int, array{record: RoomInventory, found_in: InventoryAuditRoom}>
     *     }>,
     *     totals: array<string, int>
     * }
     */
    public function compare(InventoryAudit $audit): array
    {
        $rooms = $audit->rooms()
            ->with(['items', 'branch'])
            ->orderBy('branch_id')
            ->orderBy('room_number')
            ->get();

        $items = $rooms->flatMap(fn (InventoryAuditRoom $room): Collection => $room->items);
        $roomOfItem = [];
        foreach ($rooms as $room) {
            foreach ($room->items as $item) {
                $roomOfItem[$item->id] = $room;
            }
        }

        $recordsByLocation = $this->loadRecordsByLocation($rooms);
        $links = $this->linkItemsToRecords($items, $roomOfItem, $recordsByLocation);

        $claimedBy = [];
        foreach ($links as $itemId => $record) {
            $claimedBy[$record->id] = $roomOfItem[$itemId];
        }

        $totals = [self::TYPE_OK => 0, self::TYPE_CHANGED => 0, self::TYPE_MOVED => 0, self::TYPE_NEW => 0, 'missing' => 0];
        $result = [];

        foreach ($rooms as $room) {
            $missing = [];
            $movedAway = [];

            foreach ($recordsByLocation[$this->locationKey($room->branch_id, $room->room_number)] ?? [] as $record) {
                if (! isset($claimedBy[$record->id])) {
                    $missing[] = ['key' => "record:{$record->id}", 'record' => $record];
                } elseif ($claimedBy[$record->id]->id !== $room->id) {
                    $movedAway[] = ['record' => $record, 'found_in' => $claimedBy[$record->id]];
                }
            }

            $matchActions = array_map(
                fn (array $row): string => self::ACTION_MATCH_PREFIX.$row['record']->id,
                $missing,
            );

            $rows = [];
            foreach ($room->items as $item) {
                $rows[] = $this->buildRow($item, $links[$item->id] ?? null, $room, $matchActions);
            }

            foreach ($rows as $row) {
                $totals[$row['type']]++;
            }
            $totals['missing'] += count($missing);

            $result[] = ['room' => $room, 'rows' => $rows, 'missing' => $missing, 'moved_away' => $movedAway];
        }

        return ['rooms' => $result, 'totals' => $totals];
    }

    /**
     * Застосовує рішення звірки до room_inventory і закриває обхід.
     *
     * @param  array<string, string>  $decisions  ключ "item:ID" або "record:ID" => дія
     * @return array{confirmed: int, updated: int, moved: int, created: int, removed: int, skipped: int}
     */
    public function apply(InventoryAudit $audit, array $decisions, User $user): array
    {
        return DB::transaction(function () use ($audit, $decisions, $user): array {
            $summary = ['confirmed' => 0, 'updated' => 0, 'moved' => 0, 'created' => 0, 'removed' => 0, 'skipped' => 0];
            $comparison = $this->compare($audit);

            foreach ($comparison['rooms'] as $roomComparison) {
                $room = $roomComparison['room'];
                $missingById = collect($roomComparison['missing'])
                    ->keyBy(fn (array $row): int => $row['record']->id);
                $manuallyMatched = [];

                foreach ($roomComparison['rows'] as $row) {
                    $item = $row['item'];
                    $record = $row['record'];
                    $action = $decisions[$row['key']] ?? $row['default'];

                    if (! in_array($action, $row['actions'], true)) {
                        $action = self::ACTION_SKIP;
                    }

                    if (str_starts_with($action, self::ACTION_MATCH_PREFIX)) {
                        $recordId = (int) substr($action, strlen(self::ACTION_MATCH_PREFIX));

                        if (isset($manuallyMatched[$recordId])) {
                            $action = self::ACTION_SKIP;
                        } else {
                            $manuallyMatched[$recordId] = true;
                            $record = $missingById[$recordId]['record'];
                            $action = self::ACTION_UPDATE;
                        }
                    }

                    if ($action === self::ACTION_CREATE) {
                        $record = $this->applyCreate($item, $room, $audit, $user);
                    }

                    $resolution = match ($action) {
                        self::ACTION_CONFIRM => InventoryAuditItem::RESOLUTION_CONFIRMED,
                        self::ACTION_UPDATE => $this->applyUpdate($record, $item, $audit, $user),
                        self::ACTION_MOVE => $this->applyMove($record, $item, $room, $audit, $user),
                        self::ACTION_CREATE => InventoryAuditItem::RESOLUTION_CREATED,
                        default => InventoryAuditItem::RESOLUTION_SKIPPED,
                    };

                    $item->update([
                        'room_inventory_id' => $record?->id,
                        'resolution' => $resolution,
                    ]);

                    $summary[$resolution]++;
                }

                foreach ($roomComparison['missing'] as $row) {
                    $record = $row['record'];

                    if (isset($manuallyMatched[$record->id])) {
                        continue;
                    }

                    if (($decisions[$row['key']] ?? self::ACTION_KEEP) === self::ACTION_REMOVE) {
                        $this->applyRemove($record, $audit, $user);
                        $summary['removed']++;
                    }
                }
            }

            $audit->update([
                'status' => InventoryAudit::STATUS_APPLIED,
                'applied_at' => now(),
            ]);

            return $summary;
        });
    }

    /**
     * @param  array<int|string, array{doctor?: string|null, nurse?: string|null}>|null  $staff
     */
    private function syncCabinet(int $branchId, string $roomNumber, ?array $staff): Cabinet
    {
        $cabinet = Cabinet::query()->firstOrCreate(
            ['branch_id' => $branchId, 'number' => $roomNumber],
            ['is_active' => true],
        );

        foreach ([CabinetShift::SHIFT_FIRST, CabinetShift::SHIFT_SECOND] as $shiftNumber) {
            $shift = $cabinet->shifts()->firstOrCreate(['shift' => $shiftNumber]);

            if ($staff === null) {
                continue;
            }

            $shift->update([
                'doctor_id' => $this->resolveStaff($staff[$shiftNumber]['doctor'] ?? null, MedicalStaff::TYPE_DOCTOR, $cabinet)?->id,
                'nurse_id' => $this->resolveStaff($staff[$shiftNumber]['nurse'] ?? null, MedicalStaff::TYPE_NURSE, $cabinet)?->id,
            ]);
        }

        return $cabinet;
    }

    /**
     * Знаходить співробітника за ПІБ (повний збіг або однозначний початок ПІБ),
     * інакше створює нового в довіднику медперсоналу.
     */
    private function resolveStaff(?string $name, string $type, Cabinet $cabinet): ?MedicalStaff
    {
        $name = $this->squish((string) $name);

        if ($name === '') {
            return null;
        }

        $needle = $this->normalize($name);
        $staff = MedicalStaff::query()->where('type', $type)->get();

        $exact = $staff->first(fn (MedicalStaff $person): bool => $this->normalize($person->full_name) === $needle);
        if ($exact) {
            return $exact;
        }

        $byPrefix = $staff->filter(
            fn (MedicalStaff $person): bool => str_starts_with($this->normalize($person->full_name), $needle.' ')
        );
        if ($byPrefix->count() === 1) {
            return $byPrefix->first();
        }

        $parts = explode(' ', $name);

        return MedicalStaff::query()->create([
            'type' => $type,
            'last_name' => $parts[0],
            'first_name' => $parts[1] ?? '',
            'middle_name' => count($parts) > 2 ? implode(' ', array_slice($parts, 2)) : null,
            'position' => self::DEFAULT_POSITIONS[$type],
            'ambulatoriya_id' => $cabinet->ambulatoriya_id,
            'is_active' => true,
        ]);
    }

    /**
     * @param  Collection<int, InventoryAuditRoom>  $rooms
     * @return array<string, array<int, RoomInventory>>
     */
    private function loadRecordsByLocation(Collection $rooms): array
    {
        if ($rooms->isEmpty()) {
            return [];
        }

        $records = RoomInventory::query()
            ->with('branch')
            ->where(function ($query) use ($rooms): void {
                foreach ($rooms as $room) {
                    $query->orWhere(fn ($location) => $location
                        ->where('branch_id', $room->branch_id)
                        ->where('room_number', $room->room_number));
                }
            })
            ->orderBy('equipment_type')
            ->orderBy('id')
            ->get();

        $byLocation = [];
        foreach ($records as $record) {
            $byLocation[$this->locationKey($record->branch_id, $record->room_number)][] = $record;
        }

        return $byLocation;
    }

    /**
     * Зіставлення у чотири проходи: явна позначка у формі → інвентарний номер →
     * серійний номер → назва в межах того самого кабінету. Один запис системи
     * може бути зайнятий лише однією позицією обходу.
     *
     * @param  Collection<int, InventoryAuditItem>  $items
     * @param  array<int, InventoryAuditRoom>  $roomOfItem
     * @param  array<string, array<int, RoomInventory>>  $recordsByLocation
     * @return array<int, RoomInventory> item id => запис системи
     */
    private function linkItemsToRecords(Collection $items, array $roomOfItem, array $recordsByLocation): array
    {
        $links = [];
        $claimed = [];

        $explicit = RoomInventory::query()
            ->with('branch')
            ->whereIn('id', $items->pluck('room_inventory_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $record = $item->room_inventory_id ? $explicit->get($item->room_inventory_id) : null;

            if ($record && ! isset($claimed[$record->id])) {
                $links[$item->id] = $record;
                $claimed[$record->id] = true;
            }
        }

        foreach (['inventory_number', 'serial_number'] as $field) {
            $pending = $items->filter(
                fn (InventoryAuditItem $item): bool => ! isset($links[$item->id]) && $this->normalize($item->{$field}) !== ''
            );

            if ($pending->isEmpty()) {
                continue;
            }

            $candidates = RoomInventory::query()
                ->with('branch')
                ->whereIn($field, $pending->pluck($field)->map(fn (string $value): string => trim($value))->unique())
                ->orderBy('id')
                ->get()
                ->groupBy(fn (RoomInventory $record): string => $this->normalize($record->{$field}));

            foreach ($pending as $item) {
                $room = $roomOfItem[$item->id];
                $free = ($candidates[$this->normalize($item->{$field})] ?? collect())
                    ->reject(fn (RoomInventory $record): bool => isset($claimed[$record->id]));

                $record = $free->first(fn (RoomInventory $record): bool => $this->isAt($record, $room)) ?? $free->first();

                if ($record) {
                    $links[$item->id] = $record;
                    $claimed[$record->id] = true;
                }
            }
        }

        foreach ($items as $item) {
            if (isset($links[$item->id])) {
                continue;
            }

            $room = $roomOfItem[$item->id];

            foreach ($recordsByLocation[$this->locationKey($room->branch_id, $room->room_number)] ?? [] as $record) {
                if (isset($claimed[$record->id])
                    || $this->normalize($record->equipment_type) !== $this->normalize($item->equipment_type)
                    || $this->identifiersConflict($item, $record)) {
                    continue;
                }

                $links[$item->id] = $record;
                $claimed[$record->id] = true;

                break;
            }
        }

        return $links;
    }

    /**
     * @param  array<int, string>  $matchActions
     * @return array{key: string, type: string, item: InventoryAuditItem, record: RoomInventory|null, changes: array<string, array{old: mixed, new: mixed}>, actions: array<int, string>, default: string}
     */
    private function buildRow(InventoryAuditItem $item, ?RoomInventory $record, InventoryAuditRoom $room, array $matchActions): array
    {
        $row = ['key' => "item:{$item->id}", 'item' => $item, 'record' => $record, 'changes' => []];

        if (! $record) {
            return $row + [
                'type' => self::TYPE_NEW,
                'actions' => [self::ACTION_CREATE, self::ACTION_SKIP, ...$matchActions],
                'default' => self::ACTION_CREATE,
            ];
        }

        $row['changes'] = $this->diff($item, $record);

        if (! $this->isAt($record, $room)) {
            return $row + [
                'type' => self::TYPE_MOVED,
                'actions' => [self::ACTION_MOVE, self::ACTION_SKIP],
                'default' => self::ACTION_MOVE,
            ];
        }

        if ($row['changes'] === []) {
            return $row + [
                'type' => self::TYPE_OK,
                'actions' => [self::ACTION_CONFIRM],
                'default' => self::ACTION_CONFIRM,
            ];
        }

        return $row + [
            'type' => self::TYPE_CHANGED,
            'actions' => [self::ACTION_UPDATE, self::ACTION_SKIP],
            'default' => self::ACTION_UPDATE,
        ];
    }

    /**
     * @return array<string, array{old: mixed, new: mixed}>
     */
    private function diff(InventoryAuditItem $item, RoomInventory $record): array
    {
        $changes = [];

        if ((int) $record->quantity !== (int) $item->quantity) {
            $changes['quantity'] = ['old' => (int) $record->quantity, 'new' => (int) $item->quantity];
        }

        foreach (self::COMPARED_FIELDS as $field) {
            if ($this->normalize($item->{$field}) !== '' && $this->normalize($item->{$field}) !== $this->normalize($record->{$field})) {
                $changes[$field] = ['old' => $record->{$field}, 'new' => $item->{$field}];
            }
        }

        return $changes;
    }

    private function applyUpdate(RoomInventory $record, InventoryAuditItem $item, InventoryAudit $audit, User $user): string
    {
        $oldQuantity = (int) $record->quantity;

        $record->update($this->foundAttributes($item));

        if ($oldQuantity !== (int) $item->quantity) {
            $this->logMovement($record, 'inventory', (int) $item->quantity - $oldQuantity, $audit, $user);
        }

        return InventoryAuditItem::RESOLUTION_UPDATED;
    }

    private function applyMove(RoomInventory $record, InventoryAuditItem $item, InventoryAuditRoom $room, InventoryAudit $audit, User $user): string
    {
        $fromBranchId = $record->branch_id;
        $fromRoomNumber = $record->room_number;

        $record->update($this->foundAttributes($item) + [
            'branch_id' => $room->branch_id,
            'room_number' => $room->room_number,
        ]);

        InventoryTransfer::query()->create([
            'inventory_id' => $record->id,
            'from_branch_id' => $fromBranchId,
            'from_room_number' => $fromRoomNumber,
            'to_branch_id' => $room->branch_id,
            'to_room_number' => $room->room_number,
            'quantity' => (int) $item->quantity,
            'user_id' => $user->id,
            'transfer_date' => now()->toDateString(),
            'notes' => $this->auditLabel($audit),
        ]);

        $this->logMovement(
            $record,
            'transfer',
            (int) $item->quantity,
            $audit,
            $user,
            "Переміщення: {$fromRoomNumber} → {$room->room_number}",
        );

        return InventoryAuditItem::RESOLUTION_MOVED;
    }

    private function applyCreate(InventoryAuditItem $item, InventoryAuditRoom $room, InventoryAudit $audit, User $user): RoomInventory
    {
        $record = RoomInventory::query()->create([
            'admin_telegram_id' => $user->telegram_id ?? 0,
            'branch_id' => $room->branch_id,
            'room_number' => $room->room_number,
            'equipment_type' => $item->equipment_type,
            'brand' => $item->brand,
            'model' => $item->model,
            'serial_number' => $item->serial_number,
            'inventory_number' => $item->inventory_number,
            'quantity' => (int) $item->quantity,
            'unit' => 'шт',
            'min_quantity' => 0,
            'notes' => $item->notes,
        ]);

        if ((int) $room->branch_id === self::WAREHOUSE_BRANCH_ID && (int) $item->quantity !== 0) {
            $this->logMovement($record, 'inventory', (int) $item->quantity, $audit, $user);
        }

        return $record;
    }

    /**
     * Не знайдений запис не видаляється: залишок обнуляється, а сам запис
     * лишається для подальшого списання.
     */
    private function applyRemove(RoomInventory $record, InventoryAudit $audit, User $user): void
    {
        $oldQuantity = (int) $record->quantity;
        $note = 'Не знайдено: '.$this->auditLabel($audit);

        $record->update([
            'quantity' => 0,
            'notes' => $record->notes ? $record->notes."\n".$note : $note,
        ]);

        if ($oldQuantity !== 0) {
            $this->logMovement($record, 'inventory', -$oldQuantity, $audit, $user, 'Не знайдено');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function foundAttributes(InventoryAuditItem $item): array
    {
        $attributes = ['quantity' => (int) $item->quantity];

        foreach (self::COMPARED_FIELDS as $field) {
            if ($this->normalize($item->{$field}) !== '') {
                $attributes[$field] = $item->{$field};
            }
        }

        return $attributes;
    }

    private function logMovement(RoomInventory $record, string $type, int $quantity, InventoryAudit $audit, User $user, ?string $detail = null): void
    {
        WarehouseMovement::query()->create([
            'user_id' => $user->id,
            'inventory_id' => $record->id,
            'type' => $type,
            'quantity' => $quantity,
            'balance_after' => (int) $record->quantity,
            'note' => $this->auditLabel($audit).($detail ? " — {$detail}" : ''),
            'operation_date' => now()->toDateString(),
        ]);
    }

    private function auditLabel(InventoryAudit $audit): string
    {
        return "Обхід кабінетів #{$audit->id}";
    }

    private function identifiersConflict(InventoryAuditItem $item, RoomInventory $record): bool
    {
        foreach (['inventory_number', 'serial_number'] as $field) {
            $found = $this->normalize($item->{$field});
            $known = $this->normalize($record->{$field});

            if ($found !== '' && $known !== '' && $found !== $known) {
                return true;
            }
        }

        return false;
    }

    private function isAt(RoomInventory $record, InventoryAuditRoom $room): bool
    {
        return $this->locationKey($record->branch_id, $record->room_number)
            === $this->locationKey($room->branch_id, $room->room_number);
    }

    private function locationKey(int|string $branchId, ?string $roomNumber): string
    {
        return $branchId.'|'.$this->normalize($roomNumber);
    }

    private function normalize(?string $value): string
    {
        return mb_strtolower(str_replace(['’', 'ʼ', '`'], "'", $this->squish((string) $value)));
    }

    private function squish(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    private function filled(mixed $value): ?string
    {
        $value = $this->squish((string) $value);

        return $value === '' ? null : $value;
    }
}
