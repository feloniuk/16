<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WriteoffRequest extends Model
{
    protected $fillable = [
        'request_number', 'user_id', 'status', 'description',
        'notes', 'writeoff_date', 'archived_at',
    ];

    protected $casts = [
        'writeoff_date' => 'date',
        'archived_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = 'SPY-'.date('Y').'-'.str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WriteoffRequestItem::class);
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Чернетка</span>',
            'submitted' => '<span class="badge bg-warning">Подана</span>',
            'approved' => '<span class="badge bg-success">Затверджена</span>',
            'completed' => '<span class="badge bg-primary">Завершена</span>',
            default => '<span class="badge bg-light text-dark">'.ucfirst($this->status).'</span>',
        };
    }
}
