<?php
// app/Models/Admin.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_id',
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

// app/Models/Branch.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean'
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
}

// app/Models/RepairRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_telegram_id',
        'username',
        'branch_id',
        'room_number',
        'description',
        'phone',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'нова' => '<span class="badge bg-primary">Новая</span>',
            'в_роботі' => '<span class="badge bg-warning">В работе</span>',
            'виконана' => '<span class="badge bg-success">Выполнена</span>',
            default => '<span class="badge bg-secondary">Неизвестно</span>'
        };
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'нова');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'в_роботі');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'виконана');
    }
}

// app/Models/CartridgeReplacement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartridgeReplacement extends Model
{
    use HasFactory;

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
        'replacement_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
}