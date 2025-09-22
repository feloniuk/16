<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_number', 'user_id', 'inventory_date', 'status', 'notes'
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->inventory_number)) {
                $model->inventory_number = 'INV-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
            
            // Устанавливаем дату по умолчанию если не указана
            if (empty($model->inventory_date)) {
                $model->inventory_date = now()->toDateString();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(WarehouseInventoryItem::class, 'inventory_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'in_progress' => '<span class="badge bg-warning">В процесі</span>',
            'completed' => '<span class="badge bg-success">Завершена</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>'
        };
    }

    public function getTotalDiscrepanciesAttribute()
    {
        return $this->items()->where('difference', '!=', 0)->count();
    }
}