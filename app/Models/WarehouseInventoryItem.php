<?php
// app/Models/WarehouseInventoryItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id', // ЗМІНЕНО: було warehouse_item_id
        'warehouse_inventory_id', 
        'system_quantity', 
        'actual_quantity', 
        'difference', 
        'note'
    ];

    public function warehouseInventory()
    {
        return $this->belongsTo(WarehouseInventory::class, 'warehouse_inventory_id');
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