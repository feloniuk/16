<?php
// app/Models/RepairMaster.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone', 
        'email',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function repairTrackings()
    {
        return $this->hasMany(RepairTracking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}