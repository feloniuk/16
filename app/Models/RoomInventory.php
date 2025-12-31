<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomInventory extends Model
{
    use HasFactory;

    protected $table = 'room_inventory';

    // Вказуємо що використовуємо тільки created_at
    const UPDATED_AT = null;

    // Або можна вимкнути timestamps повністю і керувати created_at вручну:
    // public $timestamps = false;

    protected $fillable = [
        'admin_telegram_id',
        'branch_id',
        'room_number',
        'template_id',
        'equipment_type',
        'full_name',
        'brand',
        'model',
        'serial_number',
        'inventory_number',
        'quantity',
        'unit',
        'price',
        'min_quantity',
        'category',
        'balance_code',
        'notes',
    ];

    protected $casts = [
        'admin_telegram_id' => 'integer',
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Автоматично встановлювати created_at при створенні
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->created_at) {
                $model->created_at = now();
            }
        });
    }

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

    // Скопи
    public function scopeWarehouse($query)
    {
        return $query->where('branch_id', 6);
    }

    public function scopeEquipment($query)
    {
        return $query->where('branch_id', '!=', 6);
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('inventory_number');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity')
            ->where('branch_id', 6);
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
