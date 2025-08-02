<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomInventory extends Model
{
    use HasFactory;

    protected $table = 'room_inventory';
    public $timestamps = false;

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
    ];

    protected $dates = [
        'created_at'
    ];

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

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}