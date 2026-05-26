<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WriteoffRequestItem extends Model
{
    protected $fillable = [
        'writeoff_request_id', 'inventory_id', 'item_name', 'unit', 'quantity', 'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function writeoffRequest(): BelongsTo
    {
        return $this->belongsTo(WriteoffRequest::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }
}
