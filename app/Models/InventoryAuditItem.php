<?php
// app/Models/InventoryAuditItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_id',
        'inventory_id',
        'inventory_number',
        'equipment_type',
        'location',
        'status',
        'notes',
    ];

    public function audit()
    {
        return $this->belongsTo(InventoryAudit::class, 'audit_id');
    }

    public function inventory()
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'found' => '<span class="badge bg-success">Найдено</span>',
            'missing' => '<span class="badge bg-danger">Отсутствует</span>',
            'extra' => '<span class="badge bg-warning">Лишний</span>',
            'damaged' => '<span class="badge bg-secondary">Поврежден</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>'
        };
    }
}