<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomInventory extends Model
{
    use HasFactory;

    protected $table = 'room_inventory';
    // Включаем timestamps для корректной работы с created_at
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
}