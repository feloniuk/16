<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalStaff extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalStaffFactory> */
    use HasFactory;

    public const TYPE_DOCTOR = 'doctor';

    public const TYPE_NURSE = 'nurse';

    protected $table = 'medical_staff';

    protected $fillable = [
        'type',
        'last_name',
        'first_name',
        'middle_name',
        'position',
        'employment_status',
        'position_status',
        'ambulatoriya_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function ambulatoriya(): BelongsTo
    {
        return $this->belongsTo(Ambulatoriya::class);
    }

    public function shiftsAsDoctor(): HasMany
    {
        return $this->hasMany(CabinetShift::class, 'doctor_id');
    }

    public function shiftsAsNurse(): HasMany
    {
        return $this->hasMany(CabinetShift::class, 'nurse_id');
    }

    public function scopeDoctors(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_DOCTOR);
    }

    public function scopeNurses(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_NURSE);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim("{$this->last_name} {$this->first_name} {$this->middle_name}"),
        );
    }
}
