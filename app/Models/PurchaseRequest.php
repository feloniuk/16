<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number', 'user_id', 'status', 'description', 
        'total_amount', 'requested_date', 'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'requested_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = 'ZAY-' . date('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => '<span class="badge bg-secondary">Чернетка</span>',
            'submitted' => '<span class="badge bg-warning">Подана</span>',
            'approved' => '<span class="badge bg-success">Затверджена</span>',
            'rejected' => '<span class="badge bg-danger">Відхилена</span>',
            'completed' => '<span class="badge bg-primary">Виконана</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>'
        };
    }

    public function recalculateTotal()
    {
        $total = $this->items()->sum(DB::raw('quantity * COALESCE(estimated_price, 0)'));
        $this->update(['total_amount' => $total]);
        return $total;
    }
}