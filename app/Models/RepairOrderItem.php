<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_order_id',
        'equipment_id',
        'repair_description',
        'repair_notes',
        'cost',
        'returned_at',
        'return_document_number',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'returned_at' => 'date',
        ];
    }

    public function getFormattedCostAttribute(): string
    {
        if ($this->cost === null) {
            return '-';
        }

        return number_format($this->cost, 2, ',', ' ').' грн';
    }

    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(RoomInventory::class, 'equipment_id');
    }
}
