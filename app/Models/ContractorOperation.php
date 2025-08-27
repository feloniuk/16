<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'user_id',
        'inventory_id',
        'type',
        'contract_number',
        'operation_date',
        'cost',
        'description',
        'status',
        'notes',
    ];

    protected $casts = [
        'operation_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventory()
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'in_progress' => '<span class="badge bg-warning">В процессе</span>',
            'completed' => '<span class="badge bg-success">Завершено</span>',
            'cancelled' => '<span class="badge bg-danger">Отменено</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>'
        };
    }

    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'send_for_repair' => '<span class="badge bg-warning">Отправка на ремонт</span>',
            'receive_from_repair' => '<span class="badge bg-info">Получение с ремонта</span>',
            'purchase' => '<span class="badge bg-success">Закупка</span>',
            'service' => '<span class="badge bg-primary">Обслуживание</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->type) . '</span>'
        };
    }
}