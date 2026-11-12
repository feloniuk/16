<?php

namespace Tests\Feature;

use App\Models\Branch;
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
use App\Services\InventoryAuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAuditTest extends TestCase
{
    use RefreshDatabase;

    private const WAREHOUSE_BRANCH_ID = 6;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /**
     * Одна з міграцій сама додає рядок у room_inventory, тому тести рахують лише власні записи.
     */
    private function equipmentCount(): int
    {
        return RoomInventory::where('branch_id', '!=', self::WAREHOUSE_BRANCH_ID)->count();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function equipment(Branch $branch, string $room, string $type, array $attributes = []): RoomInventory
    {
        return RoomInventory::create($attributes + [
            'admin_telegram_id' => 0,
            'branch_id' => $branch->id,
            'room_number' => $room,
            'equipment_type' => $type,
            'quantity' => 1,
            'unit' => 'шт',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function found(InventoryAuditRoom $room, string $type, array $attributes = []): InventoryAuditItem
    {
        return InventoryAuditItem::factory()->for($room, 'room')->create($attributes + ['equipment_type' => $type]);
    }

    private function auditRoom(InventoryAudit $audit, Branch $branch, string $number): InventoryAuditRoom
    {
        return InventoryAuditRoom::factory()->for($audit, 'audit')->create([
            'branch_id' => $branch->id,
            'room_number' => $number,
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function rowsByType(InventoryAudit $audit): array
    {
        $rows = [];
        foreach (app(InventoryAuditService::class)->compare($audit)['rooms'] as $roomComparison) {
            foreach ($roomComparison['rows'] as $row) {
                $rows[$row['item']->equipment_type] = $row;
            }
        }

        return $rows;
    }

    public function test_admin_can_start_an_audit_and_open_the_walk_form(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('inventory-audits.store'))->assertRedirect();

        $audit = InventoryAudit::firstOrFail();
        $this->assertSame(InventoryAudit::STATUS_IN_PROGRESS, $audit->status);
        $this->assertSame($admin->id, $audit->user_id);

        $this->actingAs($admin)->get(route('inventory-audits.show', $audit))->assertOk();
        $this->actingAs($admin)->get(route('inventory-audits.index'))->assertOk();
    }

    public function test_warehouse_keeper_can_access_but_director_cannot(): void
    {
        $keeper = User::factory()->create(['role' => 'warehouse_keeper', 'is_active' => true]);
        $director = User::factory()->create(['role' => 'director', 'is_active' => true]);

        $this->actingAs($keeper)->get(route('inventory-audits.index'))->assertOk();
        $this->actingAs($director)->get(route('inventory-audits.index'))->assertForbidden();
        $this->actingAs($director)->post(route('inventory-audits.store'))->assertForbidden();
    }

    public function test_saving_a_room_fills_cabinets_and_staff_but_does_not_touch_inventory(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $existingDoctor = MedicalStaff::factory()->doctor()->create([
            'last_name' => 'Шевченко', 'first_name' => 'Тарас', 'middle_name' => 'Григорович',
        ]);
        $monitor = $this->equipment($branch, '101', 'Монітор', ['inventory_number' => 'INV-1']);
        $audit = InventoryAudit::factory()->create();

        $response = $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), [
            'branch_id' => $branch->id,
            'room_number' => ' 101 ',
            'notes' => 'Другий поверх',
            'staff' => [
                1 => ['doctor' => '  шевченко   тарас григорович ', 'nurse' => 'Коваль Олена Іванівна'],
                2 => ['doctor' => '', 'nurse' => ''],
            ],
            'items' => [
                'r'.$monitor->id => ['room_inventory_id' => $monitor->id, 'quantity' => 1],
                'n1' => ['equipment_type' => 'Принтер', 'brand' => 'HP', 'inventory_number' => 'INV-77', 'quantity' => 1],
                'n2' => ['equipment_type' => '', 'brand' => 'порожній рядок'],
            ],
        ]);

        $response->assertRedirect(route('inventory-audits.show', ['inventoryAudit' => $audit, 'branch_id' => $branch->id]));
        $response->assertSessionHasNoErrors();

        $cabinet = Cabinet::where('branch_id', $branch->id)->where('number', '101')->firstOrFail();
        $firstShift = $cabinet->shifts()->where('shift', CabinetShift::SHIFT_FIRST)->firstOrFail();
        $secondShift = $cabinet->shifts()->where('shift', CabinetShift::SHIFT_SECOND)->firstOrFail();

        $this->assertSame($existingDoctor->id, $firstShift->doctor_id);
        $this->assertSame(1, MedicalStaff::doctors()->count());

        $nurse = MedicalStaff::nurses()->firstOrFail();
        $this->assertSame('Коваль Олена Іванівна', $nurse->full_name);
        $this->assertSame($nurse->id, $firstShift->nurse_id);
        $this->assertNull($secondShift->doctor_id);

        $room = $audit->rooms()->firstOrFail();
        $this->assertSame('101', $room->room_number);
        $this->assertSame($cabinet->id, $room->cabinet_id);
        $this->assertCount(2, $room->items);
        $this->assertSame('Монітор', $room->items->firstWhere('room_inventory_id', $monitor->id)->equipment_type);

        $this->assertSame(1, $this->equipmentCount());
    }

    public function test_surname_alone_matches_a_unique_existing_staff_member(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $doctor = MedicalStaff::factory()->doctor()->create(['last_name' => 'Франко', 'first_name' => 'Іван', 'middle_name' => 'Якович']);
        $audit = InventoryAudit::factory()->create();

        $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), [
            'branch_id' => $branch->id,
            'room_number' => '5',
            'staff' => [1 => ['doctor' => 'Франко']],
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, MedicalStaff::count());
        $this->assertSame($doctor->id, CabinetShift::where('shift', CabinetShift::SHIFT_FIRST)->firstOrFail()->doctor_id);
    }

    public function test_saving_the_same_room_again_replaces_its_draft_and_staff(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $audit = InventoryAudit::factory()->create();
        $payload = fn (array $items, string $doctor): array => [
            'branch_id' => $branch->id,
            'room_number' => '12',
            'staff' => [1 => ['doctor' => $doctor]],
            'items' => $items,
        ];

        $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), $payload([
            ['equipment_type' => 'Сканер'], ['equipment_type' => 'Миша'],
        ], 'Лисенко Микола Віталійович'));
        $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), $payload([
            ['equipment_type' => 'Ноутбук'],
        ], ''));

        $this->assertSame(1, $audit->rooms()->count());
        $this->assertSame(['Ноутбук'], $audit->items()->pluck('equipment_type')->all());
        $this->assertSame(1, Cabinet::count());
        $this->assertNull(CabinetShift::where('shift', CabinetShift::SHIFT_FIRST)->firstOrFail()->doctor_id);
    }

    public function test_warehouse_branch_is_audited_without_creating_a_cabinet(): void
    {
        $admin = $this->admin();
        $warehouse = Branch::factory()->create(['id' => self::WAREHOUSE_BRANCH_ID]);
        $audit = InventoryAudit::factory()->create();

        $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), [
            'branch_id' => $warehouse->id,
            'room_number' => 'Склад',
            'staff' => [1 => ['doctor' => 'Не Має Значення']],
            'items' => [['equipment_type' => 'Папір А4', 'quantity' => 10]],
        ])->assertSessionHasNoErrors();

        $this->assertSame(0, Cabinet::count());
        $this->assertSame(0, MedicalStaff::count());
        $this->assertSame(1, $audit->items()->count());
    }

    public function test_room_data_returns_system_records_saved_draft_and_staff(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $monitor = $this->equipment($branch, '7', 'Монітор');
        $this->equipment($branch, '8', 'Принтер');
        $cabinet = Cabinet::factory()->for($branch)->create(['number' => '7']);
        $doctor = MedicalStaff::factory()->doctor()->create();
        CabinetShift::factory()->for($cabinet)->create(['shift' => CabinetShift::SHIFT_FIRST, 'doctor_id' => $doctor->id]);
        $audit = InventoryAudit::factory()->create();
        $room = $this->auditRoom($audit, $branch, '7');
        $this->found($room, 'Монітор', ['room_inventory_id' => $monitor->id]);

        $response = $this->actingAs($admin)->getJson(route('inventory-audits.room-data', [
            'inventoryAudit' => $audit, 'branch_id' => $branch->id, 'room_number' => '7',
        ]));

        $response->assertOk()
            ->assertJsonCount(1, 'records')
            ->assertJsonPath('records.0.id', $monitor->id)
            ->assertJsonPath('saved.items.0.room_inventory_id', $monitor->id)
            ->assertJsonPath('staff.1.doctor', $doctor->full_name);
    }

    public function test_comparison_classifies_found_items_against_system_records(): void
    {
        $branch = Branch::factory()->create();
        $ticked = $this->equipment($branch, '1', 'Монітор');
        $byNumber = $this->equipment($branch, '1', 'Системний блок', ['inventory_number' => 'PC-1', 'quantity' => 1]);
        $elsewhere = $this->equipment($branch, '9', 'Принтер', ['serial_number' => 'SN-9']);
        $byName = $this->equipment($branch, '1', 'Клавіатура');
        $absent = $this->equipment($branch, '1', 'Сканер', ['inventory_number' => 'SC-1']);

        $audit = InventoryAudit::factory()->create();
        $room = $this->auditRoom($audit, $branch, '1');
        $this->found($room, 'Монітор', ['room_inventory_id' => $ticked->id]);
        $this->found($room, 'Системний блок', ['inventory_number' => 'pc-1', 'quantity' => 2, 'brand' => 'Dell']);
        $this->found($room, 'Принтер', ['serial_number' => 'SN-9']);
        $this->found($room, 'клавіатура');
        $this->found($room, 'Сканер', ['inventory_number' => 'SC-OTHER']);

        $rows = $this->rowsByType($audit);
        $comparison = app(InventoryAuditService::class)->compare($audit);

        $this->assertSame(InventoryAuditService::TYPE_OK, $rows['Монітор']['type']);
        $this->assertSame(InventoryAuditService::TYPE_CHANGED, $rows['Системний блок']['type']);
        $this->assertSame($byNumber->id, $rows['Системний блок']['record']->id);
        $this->assertSame(['quantity', 'brand'], array_keys($rows['Системний блок']['changes']));
        $this->assertSame(InventoryAuditService::TYPE_MOVED, $rows['Принтер']['type']);
        $this->assertSame($elsewhere->id, $rows['Принтер']['record']->id);
        $this->assertSame(InventoryAuditService::TYPE_OK, $rows['клавіатура']['type']);
        $this->assertSame($byName->id, $rows['клавіатура']['record']->id);
        $this->assertSame(InventoryAuditService::TYPE_NEW, $rows['Сканер']['type']);
        $this->assertContains('match:'.$absent->id, $rows['Сканер']['actions']);

        $this->assertSame([$absent->id], array_map(fn (array $row): int => $row['record']->id, $comparison['rooms'][0]['missing']));
        $this->assertSame(1, $comparison['totals']['missing']);
    }

    public function test_item_found_in_another_audited_room_is_not_reported_missing(): void
    {
        $branch = Branch::factory()->create();
        $laptop = $this->equipment($branch, '1', 'Ноутбук', ['inventory_number' => 'NB-1']);
        $audit = InventoryAudit::factory()->create();
        $this->auditRoom($audit, $branch, '1');
        $this->found($this->auditRoom($audit, $branch, '2'), 'Ноутбук', ['inventory_number' => 'NB-1']);

        $comparison = collect(app(InventoryAuditService::class)->compare($audit)['rooms'])
            ->keyBy(fn (array $row): string => $row['room']->room_number);

        $this->assertSame([], $comparison['1']['missing']);
        $this->assertSame($laptop->id, $comparison['1']['moved_away'][0]['record']->id);
        $this->assertSame(InventoryAuditService::TYPE_MOVED, $comparison['2']['rows'][0]['type']);
    }

    public function test_review_page_renders_without_changing_inventory(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $this->equipment($branch, '1', 'Сканер');
        $audit = InventoryAudit::factory()->create();
        $this->found($this->auditRoom($audit, $branch, '1'), 'Принтер');

        $this->actingAs($admin)->get(route('inventory-audits.review', $audit))
            ->assertOk()
            ->assertSee('Принтер')
            ->assertSee('Не знайдено');

        $this->assertSame(1, $this->equipmentCount());
        $this->assertSame(InventoryAudit::STATUS_IN_PROGRESS, $audit->fresh()->status);
    }

    public function test_apply_with_default_decisions_updates_moves_and_creates_records(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $changed = $this->equipment($branch, '1', 'Системний блок', ['inventory_number' => 'PC-1', 'quantity' => 1]);
        $moved = $this->equipment($branch, '9', 'Принтер', ['inventory_number' => 'PR-1']);
        $missing = $this->equipment($branch, '1', 'Сканер', ['quantity' => 1]);

        $audit = InventoryAudit::factory()->create();
        $room = $this->auditRoom($audit, $branch, '1');
        $this->found($room, 'Системний блок', ['room_inventory_id' => $changed->id, 'quantity' => 3, 'serial_number' => 'SN-NEW']);
        $this->found($room, 'Принтер', ['inventory_number' => 'PR-1']);
        $this->found($room, 'Телефон', ['brand' => 'Panasonic', 'quantity' => 2]);

        $response = $this->actingAs($admin)->post(route('inventory-audits.apply', $audit), ['decisions' => '{}']);

        $response->assertRedirect(route('inventory-audits.review', $audit));
        $response->assertSessionHasNoErrors();

        $changed->refresh();
        $this->assertSame(3, $changed->quantity);
        $this->assertSame('SN-NEW', $changed->serial_number);
        $this->assertSame('Системний блок', $changed->equipment_type);
        $this->assertDatabaseHas('warehouse_movements', ['inventory_id' => $changed->id, 'type' => 'inventory', 'quantity' => 2, 'balance_after' => 3]);

        $moved->refresh();
        $this->assertSame('1', $moved->room_number);
        $transfer = InventoryTransfer::where('inventory_id', $moved->id)->firstOrFail();
        $this->assertSame('9', $transfer->from_room_number);
        $this->assertSame('1', $transfer->to_room_number);
        $this->assertDatabaseHas('warehouse_movements', ['inventory_id' => $moved->id, 'type' => 'transfer']);

        $created = RoomInventory::where('equipment_type', 'Телефон')->firstOrFail();
        $this->assertSame($branch->id, $created->branch_id);
        $this->assertSame('1', $created->room_number);
        $this->assertSame('Panasonic', $created->brand);
        $this->assertSame(2, $created->quantity);

        $this->assertSame(1, $missing->fresh()->quantity);

        $audit->refresh();
        $this->assertTrue($audit->isApplied());
        $this->assertNotNull($audit->applied_at);
        $this->assertEqualsCanonicalizing(
            [InventoryAuditItem::RESOLUTION_UPDATED, InventoryAuditItem::RESOLUTION_MOVED, InventoryAuditItem::RESOLUTION_CREATED],
            $audit->items()->pluck('resolution')->all()
        );
        $this->assertSame($created->id, $audit->items()->where('equipment_type', 'Телефон')->firstOrFail()->room_inventory_id);

        $this->actingAs($admin)->get(route('inventory-audits.review', $audit))->assertOk()->assertSee('Створено');
    }

    public function test_apply_respects_skip_manual_match_and_remove_decisions(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $changed = $this->equipment($branch, '1', 'Монітор', ['quantity' => 1]);
        $unlabelled = $this->equipment($branch, '1', 'МФУ', ['quantity' => 1]);
        $gone = $this->equipment($branch, '1', 'Проектор', ['quantity' => 4, 'notes' => 'стара примітка']);

        $audit = InventoryAudit::factory()->create();
        $room = $this->auditRoom($audit, $branch, '1');
        $skipped = $this->found($room, 'Монітор', ['room_inventory_id' => $changed->id, 'quantity' => 5]);
        $manual = $this->found($room, 'Принтер Canon', ['inventory_number' => 'CN-5', 'quantity' => 1]);
        $duplicate = $this->found($room, 'Ще один принтер');

        $this->actingAs($admin)->post(route('inventory-audits.apply', $audit), [
            'decisions' => json_encode([
                "item:{$skipped->id}" => 'skip',
                "item:{$manual->id}" => "match:{$unlabelled->id}",
                "item:{$duplicate->id}" => "match:{$unlabelled->id}",
                "record:{$gone->id}" => 'remove',
            ]),
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, $changed->fresh()->quantity);

        $unlabelled->refresh();
        $this->assertSame('CN-5', $unlabelled->inventory_number);
        $this->assertSame('МФУ', $unlabelled->equipment_type);
        $this->assertSame($unlabelled->id, $manual->fresh()->room_inventory_id);
        $this->assertSame(InventoryAuditItem::RESOLUTION_SKIPPED, $duplicate->fresh()->resolution);

        $gone->refresh();
        $this->assertSame(0, $gone->quantity);
        $this->assertStringContainsString('стара примітка', $gone->notes);
        $this->assertStringContainsString("Обхід кабінетів #{$audit->id}", $gone->notes);
        $this->assertDatabaseHas('warehouse_movements', ['inventory_id' => $gone->id, 'type' => 'inventory', 'quantity' => -4, 'balance_after' => 0]);

        $this->assertSame(3, $this->equipmentCount());
    }

    public function test_warehouse_stock_changes_only_after_apply_and_logs_a_movement(): void
    {
        $admin = $this->admin();
        $warehouse = Branch::factory()->create(['id' => self::WAREHOUSE_BRANCH_ID]);
        $paper = $this->equipment($warehouse, 'Склад', 'Папір А4', ['quantity' => 10, 'inventory_number' => 'W-1']);
        $audit = InventoryAudit::factory()->create();

        $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), [
            'branch_id' => $warehouse->id,
            'room_number' => 'Склад',
            'items' => ['r'.$paper->id => ['room_inventory_id' => $paper->id, 'quantity' => 7]],
        ])->assertSessionHasNoErrors();

        $this->assertSame(10, $paper->fresh()->quantity);
        $this->assertSame(0, WarehouseMovement::count());

        $this->actingAs($admin)->post(route('inventory-audits.apply', $audit))->assertSessionHasNoErrors();

        $this->assertSame(7, $paper->fresh()->quantity);
        $this->assertDatabaseHas('warehouse_movements', ['inventory_id' => $paper->id, 'type' => 'inventory', 'quantity' => -3, 'balance_after' => 7]);
    }

    public function test_applied_audit_is_locked(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $audit = InventoryAudit::factory()->applied()->create();
        $room = $this->auditRoom($audit, $branch, '1');
        $this->found($room, 'Принтер');

        $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), [
            'branch_id' => $branch->id, 'room_number' => '2', 'items' => [['equipment_type' => 'Монітор']],
        ])->assertSessionHasErrors();
        $this->actingAs($admin)->post(route('inventory-audits.apply', $audit))->assertSessionHasErrors();
        $this->actingAs($admin)->delete(route('inventory-audits.rooms.destroy', [$audit, $room]))->assertSessionHasErrors();
        $this->actingAs($admin)->delete(route('inventory-audits.destroy', $audit))->assertSessionHasErrors();
        $this->actingAs($admin)->get(route('inventory-audits.show', $audit))->assertRedirect(route('inventory-audits.review', $audit));

        $this->assertSame(1, $audit->rooms()->count());
        $this->assertSame(0, $this->equipmentCount());
        $this->assertSame(0, Cabinet::count());
    }

    public function test_draft_room_and_draft_audit_can_be_removed(): void
    {
        $admin = $this->admin();
        $branch = Branch::factory()->create();
        $audit = InventoryAudit::factory()->create();
        $room = $this->auditRoom($audit, $branch, '1');
        $this->found($room, 'Принтер');
        $foreignRoom = $this->auditRoom(InventoryAudit::factory()->create(), $branch, '1');

        $this->actingAs($admin)->delete(route('inventory-audits.rooms.destroy', [$audit, $foreignRoom]))->assertNotFound();
        $this->actingAs($admin)->delete(route('inventory-audits.rooms.destroy', [$audit, $room]))->assertRedirect();
        $this->assertSame(0, $audit->items()->count());

        $this->actingAs($admin)->delete(route('inventory-audits.destroy', $audit))->assertRedirect(route('inventory-audits.index'));
        $this->assertNull($audit->fresh());
    }

    public function test_validation_rejects_missing_location_and_unknown_decisions(): void
    {
        $admin = $this->admin();
        $audit = InventoryAudit::factory()->create();

        $this->actingAs($admin)->post(route('inventory-audits.rooms.save', $audit), ['items' => [['equipment_type' => 'Принтер']]])
            ->assertSessionHasErrors(['branch_id', 'room_number']);
        $this->actingAs($admin)->post(route('inventory-audits.apply', $audit), ['decisions' => json_encode(['item:1' => 'drop table'])])
            ->assertSessionHasErrors();
    }
}
