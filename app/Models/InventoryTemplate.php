<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTemplate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'equipment_type',
        'brand',
        'model',
        'requires_serial',
        'requires_inventory'
    ];

    protected $casts = [
        'requires_serial' => 'boolean',
        'requires_inventory' => 'boolean'
    ];

    protected $dates = [
        'created_at'
    ];

    public function inventoryItems()
    {
        return $this->hasMany(RoomInventory::class, 'template_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}
