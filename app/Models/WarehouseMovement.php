<?php

// app/Models/WarehouseMovement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseMovement extends Model
{
    protected $fillable = [
        'user_id',
        'inventory_id',
        'type',
        'quantity',
        'balance_after',
        'note',
        'document_number',
        'operation_date',
    ];

    protected $casts = [
        'operation_date' => 'date',
        'quantity' => 'integer',
        'balance_after' => 'integer',
    ];

    /**
     * Користувач, що виконав операцію
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Товар зі складу (room_inventory)
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }

    /**
     * Alias для inventoryItem (для сумісності)
     */
    public function warehouseItem(): BelongsTo
    {
        return $this->inventoryItem();
    }

    /**
     * Отримати badge для типу операції
     */
    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'receipt' => '<span class="badge bg-success">Надходження</span>',
            'issue' => '<span class="badge bg-warning">Видача</span>',
            'writeoff' => '<span class="badge bg-danger">Списання</span>',
            'inventory' => '<span class="badge bg-info">Інвентаризація</span>',
            'transfer' => '<span class="badge bg-primary">Переміщення</span>',
            default => '<span class="badge bg-secondary">'.$this->type.'</span>',
        };
    }

    /**
     * Scope для фільтрації за типом
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope для фільтрації за періодом
     */
    public function scopeInPeriod($query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('operation_date', [$dateFrom, $dateTo]);
    }

    /**
     * Отримати загальний залишок для групи товарів (всі записи з одним equipment_type)
     */
    public function getGroupedBalanceAfterAttribute(): int
    {
        $equipmentType = $this->inventoryItem->equipment_type ?? null;

        if (! $equipmentType) {
            return $this->balance_after;
        }

        // Суммируем quantity для всех товаров с одинаковым equipment_type в складе (branch_id = 6)
        return RoomInventory::where('branch_id', 6)
            ->where('equipment_type', $equipmentType)
            ->sum('quantity');
    }
}
