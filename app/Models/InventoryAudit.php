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

    // Связи
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

    // Автогенерация номера аудита
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->audit_number)) {
                $model->audit_number = 'AUD-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
            
            if (empty($model->status)) {
                $model->status = 'planned';
            }
        });
    }

    // Вычисляемые атрибуты
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

    public function getAccuracyPercentageAttribute()
    {
        if ($this->total_items == 0) {
            return 100;
        }
        
        $foundItems = $this->total_items - $this->missing_items - $this->extra_items;
        return round(($foundItems / $this->total_items) * 100, 1);
    }

    // Методы для работы с аудитом
    public function startAudit()
    {
        $this->update(['status' => 'in_progress']);
    }

    public function completeAudit()
    {
        $this->update(['status' => 'completed']);
        
        // Пересчитываем статистику
        $this->recalculateStats();
    }

    private function recalculateStats()
    {
        $stats = $this->items()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "found" THEN 1 ELSE 0 END) as found,
                SUM(CASE WHEN status = "missing" THEN 1 ELSE 0 END) as missing,
                SUM(CASE WHEN status = "extra" THEN 1 ELSE 0 END) as extra
            ')
            ->first();

        $this->update([
            'checked_items' => $stats->total,
            'missing_items' => $stats->missing,
            'extra_items' => $stats->extra,
        ]);
    }
}