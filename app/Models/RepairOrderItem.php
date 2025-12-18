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
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(RepairOrder::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(RoomInventory::class, 'equipment_id');
    }
}
