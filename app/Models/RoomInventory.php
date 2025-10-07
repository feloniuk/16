<?php
// app/Models/RoomInventory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomInventory extends Model
{
    use HasFactory;

    protected $table = 'room_inventory';
    public $timestamps = true;

    protected $fillable = [
        'admin_telegram_id',
        'branch_id',
        'room_number',
        'template_id',
        'equipment_type',
        'brand',
        'model',
        'serial_number',
        'inventory_number',
        'quantity',
        'unit',
        'price',
        'min_quantity',
        'category',
        'notes'
    ];

    protected $casts = [
        'admin_telegram_id' => 'integer',
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Відносини
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function template()
    {
        return $this->belongsTo(InventoryTemplate::class, 'template_id');
    }

    public function cartridgeReplacements()
    {
        return $this->hasMany(CartridgeReplacement::class, 'printer_inventory_id');
    }

    public function movements()
    {
        return $this->hasMany(WarehouseMovement::class, 'inventory_id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(WarehouseInventoryItem::class, 'inventory_id');
    }

    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class, 'inventory_id');
    }

    // Скопи (для складських товарів)
    public function scopeWarehouse($query)
    {
        // Філія "Склад" має ID = 6
        return $query->where('branch_id', 6);
    }

    public function scopeEquipment($query)
    {
        // Всі крім складу
        return $query->where('branch_id', '!=', 6);
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('inventory_number');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity')
                     ->where('branch_id', 6); // Тільки для складу
    }

    // Методи
    public function isLowStock()
    {
        return $this->quantity <= $this->min_quantity && $this->branch_id == 6;
    }

    public function isWarehouseItem()
    {
        return $this->branch_id == 6;
    }

    public function getTotalValueAttribute()
    {
        return $this->quantity * ($this->price ?? 0);
    }
}