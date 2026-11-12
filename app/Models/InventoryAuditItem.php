<?php

namespace App\Models;

use Database\Factories\InventoryAuditItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAuditItem extends Model
{
    /** @use HasFactory<InventoryAuditItemFactory> */
    use HasFactory;

    protected $table = 'inventory_walk_items';

    public const RESOLUTION_CONFIRMED = 'confirmed';

    public const RESOLUTION_UPDATED = 'updated';

    public const RESOLUTION_MOVED = 'moved';

    public const RESOLUTION_CREATED = 'created';

    public const RESOLUTION_SKIPPED = 'skipped';

    protected $fillable = [
        'inventory_audit_room_id',
        'room_inventory_id',
        'equipment_type',
        'brand',
        'model',
        'serial_number',
        'inventory_number',
        'quantity',
        'notes',
        'resolution',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(InventoryAuditRoom::class, 'inventory_audit_room_id');
    }

    public function roomInventory(): BelongsTo
    {
        return $this->belongsTo(RoomInventory::class, 'room_inventory_id');
    }
}
