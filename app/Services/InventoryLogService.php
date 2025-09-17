<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\RoomInventory;
use Illuminate\Support\Facades\Auth;

class InventoryLogService
{
    public function logAction(RoomInventory $inventory, string $action, array $oldData = null, array $newData = null, string $description = null): InventoryLog
    {
        return InventoryLog::create([
            'user_id' => Auth::id(),
            'inventory_id' => $inventory->id,
            'action' => $action,
            'old_data' => $oldData,
            'new_data' => $newData,
            'from_location' => $oldData ? ($oldData['branch'] ?? '') . ':' . ($oldData['room'] ?? '') : null,
            'to_location' => $newData ? ($newData['branch'] ?? '') . ':' . ($newData['room'] ?? '') : null,
            'description' => $description,
        ]);
    }

    public function logMove(RoomInventory $inventory, array $from, array $to, string $reason = null): InventoryLog
    {
        return $this->logAction(
            $inventory,
            'moved',
            $from,
            $to,
            $reason ?? 'Перемещение инвентаря'
        );
    }

    public function logCreate(RoomInventory $inventory): InventoryLog
    {
        return $this->logAction(
            $inventory,
            'created',
            null,
            $inventory->toArray(),
            'Добавление нового оборудования'
        );
    }

    public function logUpdate(RoomInventory $inventory, array $oldData): InventoryLog
    {
        return $this->logAction(
            $inventory,
            'updated',
            $oldData,
            $inventory->toArray(),
            'Обновление данных оборудования'
        );
    }

    public function logDelete(RoomInventory $inventory): InventoryLog
    {
        return $this->logAction(
            $inventory,
            'deleted',
            $inventory->toArray(),
            null,
            'Удаление оборудования'
        );
    }
}