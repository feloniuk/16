<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CabinetShift extends Model
{
    /** @use HasFactory<\Database\Factories\CabinetShiftFactory> */
    use HasFactory;

    public const SHIFT_FIRST = 1;

    public const SHIFT_SECOND = 2;

    protected $fillable = [
        'cabinet_id',
        'shift',
        'doctor_id',
        'nurse_id',
    ];

    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(MedicalStaff::class, 'doctor_id');
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(MedicalStaff::class, 'nurse_id');
    }
}
