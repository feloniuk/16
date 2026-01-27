<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkLog extends Model
{
    protected $fillable = [
        'work_type',
        'description',
        'branch_id',
        'room_number',
        'performed_at',
        'user_id',
        'loggable_type',
        'loggable_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    public function scopeByWorkType($query, $workType)
    {
        return $query->where('work_type', $workType);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('performed_at', [$startDate, $endDate]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
                ->orWhere('room_number', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    public function getWorkTypeLabel(): string
    {
        return match ($this->work_type) {
            'inventory_transfer' => 'Переміщення інвентарю',
            'cartridge_replacement' => 'Заміна картриджа',
            'repair_sent' => 'Відправка на ремонт',
            'repair_returned' => 'Повернення з ремонту',
            'manual' => 'Інше',
            default => 'Невідомо',
        };
    }
}
