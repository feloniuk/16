<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id', 'warehouse_item_id', 'item_name', 
        'item_code', 'quantity', 'unit', 'estimated_price', 'specifications'
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function warehouseItem()
    {
        return $this->belongsTo(WarehouseItem::class);
    }

    public function getTotalAttribute()
    {
        return $this->quantity * ($this->estimated_price ?? 0);
    }
}