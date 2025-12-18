<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RepairOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'repair_master_id',
        'invoice_number',
        'sent_date',
        'returned_date',
        'total_cost',
        'description',
        'notes',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'sent_date' => 'date',
        'returned_date' => 'date',
        'total_cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->order_number)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count();
                $model->order_number = 'REPAIR-'.$year.'-'.str_pad($count + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RepairOrderItem::class);
    }

    public function repairMaster()
    {
        return $this->belongsTo(RepairMaster::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function recalculateTotal(): void
    {
        $total = $this->items()->sum(DB::raw('COALESCE(cost, 0)'));
        $this->update(['total_cost' => $total]);
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($this->status !== 'draft') {
            return false;
        }

        return $this->user_id === $user->id;
    }

    public function canBeApprovedBy(User $user): bool
    {
        return in_array($user->role, ['admin', 'director']) && $this->status === 'pending_approval';
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }

    public function reject(User $approver, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Чернетка</span>',
            'pending_approval' => '<span class="badge bg-warning">На затвердженні</span>',
            'approved' => '<span class="badge bg-success">Затверджено</span>',
            'rejected' => '<span class="badge bg-danger">Відхилено</span>',
            'sent' => '<span class="badge bg-info">Відправлено</span>',
            'in_repair' => '<span class="badge bg-primary">На ремонті</span>',
            'completed' => '<span class="badge bg-success">Завершено</span>',
            'cancelled' => '<span class="badge bg-dark">Скасовано</span>',
            default => '<span class="badge bg-secondary">'.ucfirst($this->status).'</span>'
        };
    }
}
