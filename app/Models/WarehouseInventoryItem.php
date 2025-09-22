<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id', 'warehouse_item_id', 'system_quantity', 
        'actual_quantity', 'difference', 'note'
    ];

    public function inventory()
    {
        return $this->belongsTo(WarehouseInventory::class, 'inventory_id');
    }

    public function warehouseItem()
    {
        return $this->belongsTo(WarehouseItem::class);
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