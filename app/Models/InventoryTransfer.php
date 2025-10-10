<?php
// app/Models/InventoryTransfer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'from_branch_id',
        'from_room_number',
        'to_branch_id',
        'to_room_number',
        'quantity',
        'user_id',
        'transfer_date',
        'notes'
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'quantity' => 'integer',
    ];

    public function inventory()
    {
        return $this->belongsTo(RoomInventory::class, 'inventory_id');
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor для відображення переміщення
     */
    public function getTransferDescriptionAttribute()
    {
        $from = $this->fromBranch ? $this->fromBranch->name : 'Невідомо';
        $to = $this->toBranch ? $this->toBranch->name : 'Невідомо';
        
        return "{$from} ({$this->from_room_number}) → {$to} ({$this->to_room_number})";
    }
}