<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'audit_number',
        'audit_date',
        'status',
        'total_items',
        'checked_items',
        'missing_items',
        'extra_items',
        'notes',
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryAuditItem::class, 'audit_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'planned' => '<span class="badge bg-secondary">Запланирована</span>',
            'in_progress' => '<span class="badge bg-warning">В процессе</span>',
            'completed' => '<span class="badge bg-success">Завершена</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>'
        };
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->total_items == 0) {
            return 0;
        }
        
        return round(($this->checked_items / $this->total_items) * 100, 1);
    }
}