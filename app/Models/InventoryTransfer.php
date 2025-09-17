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

    // Связи
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

    // Автогенерация номера перемещения
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->transfer_number)) {
                $model->transfer_number = 'TR-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
            
            // По умолчанию статус "запланировано"
            if (empty($model->status)) {
                $model->status = 'planned';
            }
        });
    }

    // Методы для работы со статусами
    public function start()
    {
        $this->update(['status' => 'in_transit']);
        
        // Обновляем статусы элементов
        $this->items()->update(['status' => 'in_transit']);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
        
        // Перемещаем инвентарь
        foreach ($this->items as $item) {
            $inventory = $item->inventory;
            $inventory->update([
                'branch_id' => $this->to_branch_id,
                'room_number' => $this->to_room ?? $inventory->room_number,
            ]);
            
            $item->update(['status' => 'completed']);
        }
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
        $this->items()->update(['status' => 'cancelled']);
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

    // Скопы
    public function scopePending($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}