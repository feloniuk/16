<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'inventory_id',
        'action',
        'old_data',
        'new_data',
        'from_location',
        'to_location',
        'description',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventory()
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }

    public function getActionBadgeAttribute()
    {
        return match($this->action) {
            'created' => '<span class="badge bg-success">Создано</span>',
            'updated' => '<span class="badge bg-info">Обновлено</span>',
            'moved' => '<span class="badge bg-warning">Перемещено</span>',
            'deleted' => '<span class="badge bg-danger">Удалено</span>',
            'assigned' => '<span class="badge bg-primary">Назначено</span>',
            'returned' => '<span class="badge bg-secondary">Возвращено</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->action) . '</span>'
        };
    }
}