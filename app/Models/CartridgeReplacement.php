<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartridgeReplacement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_telegram_id',
        'username',
        'branch_id',
        'room_number',
        'printer_inventory_id',
        'printer_info',
        'cartridge_type',
        'replacement_date',
        'notes'
    ];

    protected $casts = [
        'user_telegram_id' => 'integer',
        'replacement_date' => 'date',
    ];

    protected $dates = [
        'created_at',
        'replacement_date'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function printer()
    {
        return $this->belongsTo(RoomInventory::class, 'printer_inventory_id');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('replacement_date', [$startDate, $endDate]);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}
