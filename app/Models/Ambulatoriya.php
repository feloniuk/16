<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ambulatoriya extends Model
{
    /** @use HasFactory<\Database\Factories\AmbulatoriyaFactory> */
    use HasFactory;

    protected $table = 'ambulatorii';

    protected $fillable = [
        'branch_id',
        'name',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function medicalStaff(): HasMany
    {
        return $this->hasMany(MedicalStaff::class);
    }

    public function cabinets(): HasMany
    {
        return $this->hasMany(Cabinet::class);
    }
}
