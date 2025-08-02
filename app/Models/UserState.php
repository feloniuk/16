<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserState extends Model
{
    protected $primaryKey = 'telegram_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'telegram_id',
        'current_state',
        'temp_data'
    ];

    protected $casts = [
        'telegram_id' => 'integer',
        'temp_data' => 'array',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->updated_at = now();
        });
    }
}