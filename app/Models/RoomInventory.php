<?php 
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
        'notes'
    ];

    protected $casts = [
        'admin_telegram_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Существующие связи
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

    // Новые связи для системы перемещений и аудитов
    public function transferItems()
    {
        return $this->hasMany(InventoryTransferItem::class, 'inventory_id');
    }

    public function auditItems()
    {
        return $this->hasMany(InventoryAuditItem::class, 'inventory_id');
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class, 'inventory_id');
    }

    public function contractorOperations()
    {
        return $this->hasMany(ContractorOperation::class, 'inventory_id');
    }

    // Вычисляемые атрибуты
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->brand, $this->model]);
        return empty($parts) ? $this->equipment_type : implode(' ', $parts);
    }

    public function getLocationAttribute()
    {
        return $this->branch->name . ' - ' . $this->room_number;
    }

    // Скопы
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByRoom($query, $branchId, $roomNumber)
    {
        return $query->where('branch_id', $branchId)->where('room_number', $roomNumber);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('equipment_type', 'like', '%' . $type . '%');
    }

    public function scopePrinters($query)
    {
        return $query->where(function($q) {
            $q->where('equipment_type', 'like', '%принтер%')
              ->orWhere('equipment_type', 'like', '%МФУ%')
              ->orWhere('equipment_type', 'like', '%сканер%');
        });
    }
}