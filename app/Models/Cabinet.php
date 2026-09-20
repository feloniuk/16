<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabinet extends Model
{
    /** @use HasFactory<\Database\Factories\CabinetFactory> */
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'number',
        'ambulatoriya_id',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function ambulatoriya(): BelongsTo
    {
        return $this->belongsTo(Ambulatoriya::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(CabinetShift::class);
    }
}
