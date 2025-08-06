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

// app/Models/RepairTracking.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'repair_master_id',
        'sent_date',
        'returned_date',
        'invoice_number',
        'our_description',
        'repair_description',
        'cost',
        'status',
        'notes'
    ];

    protected $casts = [
        'sent_date' => 'date',
        'returned_date' => 'date',
        'cost' => 'decimal:2'
    ];

    public function equipment()
    {
        return $this->belongsTo(RoomInventory::class, 'equipment_id');
    }

    public function repairMaster()
    {
        return $this->belongsTo(RepairMaster::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'sent' => '<span class="badge bg-warning">Відправлено</span>',
            'in_repair' => '<span class="badge bg-info">На ремонті</span>',
            'completed' => '<span class="badge bg-success">Завершено</span>',
            'cancelled' => '<span class="badge bg-danger">Скасовано</span>',
            default => '<span class="badge bg-secondary">Невідомо</span>'
        };
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeInRepair($query)
    {
        return $query->whereIn('status', ['sent', 'in_repair']);
    }
}