<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'inventory_id',
        'status',
        'notes',
    ];

    public function transfer()
    {
        return $this->belongsTo(InventoryTransfer::class, 'transfer_id');
    }

    public function inventory()
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
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
}
