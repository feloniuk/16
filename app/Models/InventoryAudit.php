<?php

namespace App\Models;

use Database\Factories\InventoryAuditFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class InventoryAudit extends Model
{
    /** @use HasFactory<InventoryAuditFactory> */
    use HasFactory;

    protected $table = 'inventory_walks';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_APPLIED = 'applied';

    protected $fillable = [
        'user_id',
        'title',
        'status',
        'notes',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(InventoryAuditRoom::class);
    }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(InventoryAuditItem::class, InventoryAuditRoom::class);
    }

    public function isApplied(): bool
    {
        return $this->status === self::STATUS_APPLIED;
    }
}
