<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'user_id',
        'from_branch_id',
        'to_branch_id',
        'from_room',
        'to_room',
        'transfer_date',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function items()
    {
        return $this->hasMany(InventoryTransferItem::class, 'transfer_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'planned' => '<span class="badge bg-secondary">Запланировано</span>',
            'in_transit' => '<span class="badge bg-warning">В пути</span>',
            'completed' => '<span class="badge bg-success">Завершено</span>',
            'cancelled' => '<span class="badge bg-danger">Отменено</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>'
        };
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->transfer_number)) {
                $model->transfer_number = 'TR-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}