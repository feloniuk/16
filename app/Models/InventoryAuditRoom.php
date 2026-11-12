<?php

namespace App\Models;

use Database\Factories\InventoryAuditRoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryAuditRoom extends Model
{
    /** @use HasFactory<InventoryAuditRoomFactory> */
    use HasFactory;

    protected $table = 'inventory_walk_rooms';

    protected $fillable = [
        'inventory_audit_id',
        'branch_id',
        'room_number',
        'cabinet_id',
        'notes',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryAuditItem::class);
    }
}
