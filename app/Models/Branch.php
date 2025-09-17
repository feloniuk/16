<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $dates = [
        'created_at'
    ];

    // Существующие связи
    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class);
    }

    public function cartridgeReplacements()
    {
        return $this->hasMany(CartridgeReplacement::class);
    }

    public function inventory()
    {
        return $this->hasMany(RoomInventory::class);
    }

    // Новые связи
    public function audits()
    {
        return $this->hasMany(InventoryAudit::class);
    }

    public function transfersFrom()
    {
        return $this->hasMany(InventoryTransfer::class, 'from_branch_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(InventoryTransfer::class, 'to_branch_id');
    }

    public function allTransfers()
    {
        return InventoryTransfer::where('from_branch_id', $this->id)
                                ->orWhere('to_branch_id', $this->id);
    }

    // Скопы
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Вычисляемые атрибуты
    public function getInventoryCountAttribute()
    {
        return $this->inventory()->count();
    }

    public function getPrintersCountAttribute()
    {
        return $this->inventory()->printers()->count();
    }

    // Автоустановка created_at
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}