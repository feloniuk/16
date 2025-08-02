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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}
