<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCabinetShiftRequest;
use App\Models\CabinetShift;
use Illuminate\Http\RedirectResponse;

class CabinetShiftController extends Controller
{
    public function update(UpdateCabinetShiftRequest $request, CabinetShift $cabinetShift): RedirectResponse
    {
        $cabinetShift->update($request->validated());

        return redirect()->route('cabinets.edit', $cabinetShift->cabinet_id)->with('success', 'Призначення на зміну оновлено');
    }
}
