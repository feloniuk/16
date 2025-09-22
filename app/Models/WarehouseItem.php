<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'unit', 'quantity', 
        'min_quantity', 'price', 'category', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(WarehouseMovement::class);
    }

    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(WarehouseInventoryItem::class);
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->min_quantity;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity');
    }
}