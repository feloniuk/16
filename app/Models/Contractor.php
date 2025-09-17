<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function operations()
    {
        return $this->hasMany(ContractorOperation::class);
    }

    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'repair' => '<span class="badge bg-warning">Ремонт</span>',
            'supply' => '<span class="badge bg-success">Поставки</span>',
            'service' => '<span class="badge bg-info">Обслуживание</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->type) . '</span>'
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}