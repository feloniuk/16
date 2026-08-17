<?php

namespace App\Services;

use App\Models\InventoryTransfer;
use App\Models\RepairRequest;
use App\Models\RoomInventory;
use App\Models\User;
use App\Models\WarehouseMovement;
use App\Models\WorkLog;
use Illuminate\Support\Facades\DB;

class WarehouseIssueService
{
    public const WAREHOUSE_BRANCH_ID = 6;

    /**
     * Issue multiple warehouse items in one transaction and optionally
     * route replaced equipment to repair, another room, or back to the warehouse.
     *
     * @param  array<int, array{inventory_id: int, quantity: int, note?: ?string}>  $items
     * @param  array{note?: ?string}  $context  Extra note appended to every movement created by this batch.
     * @param  array{old_inventory_id: int, action: string, to_branch_id?: int, to_room_number?: string, note?: ?string}|null  $replacement
     */
    public function issueBatch(array $items, array $context, ?array $replacement, User $user): void
    {
        DB::transaction(function () use ($items, $context, $replacement, $user): void {
            foreach ($items as $line) {
                $this->issueOne($line['inventory_id'], $line['quantity'], $line['note'] ?? null, $context['note'] ?? null, $user);
            }

            if ($replacement !== null) {
                $this->applyReplacement($replacement, $user);
            }
        });
    }

    private function issueOne(int $inventoryId, int $quantity, ?string $lineNote, ?string $contextNote, User $user): void
    {
        $item = RoomInventory::where('branch_id', self::WAREHOUSE_BRANCH_ID)->findOrFail($inventoryId);

        if ($item->quantity < $quantity) {
            throw new \RuntimeException("Недостатньо товару на складі: {$item->equipment_type}. Залишок: {$item->quantity} {$item->unit}");
        }

        $newBalance = $item->quantity - $quantity;
        $item->update(['quantity' => $newBalance]);

        $note = trim(implode(' ', array_filter([$contextNote, $lineNote])));

        WarehouseMovement::create([
            'user_id' => $user->id,
            'inventory_id' => $item->id,
            'type' => 'issue',
            'quantity' => -$quantity,
            'balance_after' => $newBalance,
            'note' => $note !== '' ? $note : null,
            'operation_date' => now()->toDateString(),
        ]);
    }

    private function applyReplacement(array $replacement, User $user): void
    {
        $oldItem = RoomInventory::findOrFail($replacement['old_inventory_id']);

        match ($replacement['action']) {
            'repair' => $this->toRepair($oldItem, $replacement['note'] ?? null),
            'transfer' => $this->toRoom($oldItem, (int) $replacement['to_branch_id'], $replacement['to_room_number'], $user, $replacement['note'] ?? null),
            'warehouse' => $this->toRoom($oldItem, self::WAREHOUSE_BRANCH_ID, 'Склад', $user, $replacement['note'] ?? null),
            default => throw new \InvalidArgumentException("Невідома дія заміни: {$replacement['action']}"),
        };
    }

    private function toRepair(RoomInventory $oldItem, ?string $note): RepairRequest
    {
        $label = $oldItem->full_name ?: $oldItem->equipment_type;
        $description = "Заміна: {$label}, інв. № {$oldItem->inventory_number}.";

        if ($note) {
            $description .= " {$note}";
        }

        return RepairRequest::create([
            'user_telegram_id' => 0,
            'branch_id' => $oldItem->branch_id,
            'room_number' => $oldItem->room_number,
            'old_inventory_id' => $oldItem->id,
            'description' => $description,
            'status' => 'нова',
        ]);
    }

    private function toRoom(RoomInventory $oldItem, int $toBranchId, string $toRoomNumber, User $user, ?string $notes): InventoryTransfer
    {
        $fromBranchId = $oldItem->branch_id;
        $fromRoomNumber = $oldItem->room_number;
        $quantity = $oldItem->quantity;

        $oldItem->update([
            'branch_id' => $toBranchId,
            'room_number' => $toRoomNumber,
        ]);

        $transfer = InventoryTransfer::create([
            'inventory_id' => $oldItem->id,
            'from_branch_id' => $fromBranchId,
            'from_room_number' => $fromRoomNumber,
            'to_branch_id' => $toBranchId,
            'to_room_number' => $toRoomNumber,
            'quantity' => $quantity,
            'user_id' => $user->id,
            'transfer_date' => now()->toDateString(),
            'notes' => $notes,
        ]);

        WorkLog::create([
            'work_type' => 'inventory_transfer',
            'description' => "Заміна обладнання: {$oldItem->equipment_type} {$fromRoomNumber} → {$toRoomNumber}",
            'branch_id' => $toBranchId,
            'room_number' => $toRoomNumber,
            'performed_at' => now(),
            'user_id' => $user->id,
            'loggable_type' => InventoryTransfer::class,
            'loggable_id' => $transfer->id,
            'notes' => $notes,
        ]);

        WarehouseMovement::create([
            'user_id' => $user->id,
            'inventory_id' => $oldItem->id,
            'type' => 'transfer',
            'quantity' => $quantity,
            'balance_after' => $quantity,
            'note' => "Заміна обладнання: {$fromRoomNumber} → {$toRoomNumber}".($notes ? " ({$notes})" : ''),
            'operation_date' => now()->toDateString(),
        ]);

        return $transfer;
    }
}
