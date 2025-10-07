<?php
// app/Models/WarehouseInventoryItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_inventory_id',  // ВАЖЛИВО: правильна назва колонки
        'inventory_id',             // Зв'язок з room_inventory
        'system_quantity', 
        'actual_quantity', 
        'difference', 
        'note'
    ];

    protected $casts = [
        'system_quantity' => 'integer',
        'actual_quantity' => 'integer',
        'difference' => 'integer',
    ];

    // Зв'язок з інвентаризацією
    public function warehouseInventory()
    {
        return $this->belongsTo(WarehouseInventory::class, 'warehouse_inventory_id');
    }

    // Зв'язок з товаром/обладнанням з room_inventory
    public function inventoryItem()
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }

    // Для зворотної сумісності (якщо десь використовується)
    public function warehouseItem()
    {
        return $this->inventoryItem();
    }

    // Accessor для статусу різниці
    public function getDifferenceStatusAttribute()
    {
        if ($this->difference > 0) {
            return '<span class="badge bg-success">+' . $this->difference . '</span>';
        } elseif ($this->difference < 0) {
            return '<span class="badge bg-danger">' . $this->difference . '</span>';
        } else {
            return '<span class="badge bg-light text-dark">0</span>';
        }
    }
}