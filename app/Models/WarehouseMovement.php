<?php
// app/Models/WarehouseMovement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'inventory_id', // ЗМІНЕНО: було warehouse_item_id
        'type', 
        'quantity', 
        'balance_after', 
        'note', 
        'document_number', 
        'issued_to_user_id', 
        'operation_date'
    ];

    protected $casts = [
        'operation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ЗМІНЕНО: тепер зв'язок з RoomInventory
    public function inventoryItem()
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }

    // Для зворотної сумісності
    public function warehouseItem()
    {
        return $this->inventoryItem();
    }

    public function issuedToUser()
    {
        return $this->belongsTo(User::class, 'issued_to_user_id');
    }

    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'receipt' => '<span class="badge bg-success">Надходження</span>',
            'issue' => '<span class="badge bg-warning">Видача</span>',
            'writeoff' => '<span class="badge bg-danger">Списання</span>',
            'inventory' => '<span class="badge bg-info">Інвентаризація</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->type) . '</span>'
        };
    }
}