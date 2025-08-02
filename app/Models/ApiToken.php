<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'token',
        'permissions',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    protected $dates = [
        'created_at'
    ];

    protected $hidden = [
        'token'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = now();
            if (empty($model->token)) {
                $model->token = hash('sha256', uniqid() . time());
            }
        });
    }
}