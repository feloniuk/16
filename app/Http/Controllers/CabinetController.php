<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCabinetRequest;
use App\Http\Requests\UpdateCabinetRequest;
use App\Models\Ambulatoriya;
use App\Models\Branch;
use App\Models\Cabinet;
use App\Models\CabinetShift;
use App\Models\MedicalStaff;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CabinetController extends Controller
{
    public function index(): View
    {
        $branches = Branch::with(['cabinets' => function ($query) {
            $query->with(['shifts.doctor', 'shifts.nurse', 'ambulatoriya'])->orderBy('number');
        }])->orderBy('name')->get();

        return view('cabinets.index', compact('branches'));
    }

    public function create(): View
    {
        $branches = Branch::orderBy('name')->get();
        $ambulatorii = Ambulatoriya::orderBy('name')->get();

        return view('cabinets.create', compact('branches', 'ambulatorii'));
    }

    public function store(StoreCabinetRequest $request): RedirectResponse
    {
        $cabinet = Cabinet::create($request->validated() + ['is_active' => true]);

        foreach ([CabinetShift::SHIFT_FIRST, CabinetShift::SHIFT_SECOND] as $shift) {
            CabinetShift::create([
                'cabinet_id' => $cabinet->id,
                'shift' => $shift,
            ]);
        }

        return redirect()->route('cabinets.edit', $cabinet)->with('success', 'Кабінет створено');
    }

    public function edit(Cabinet $cabinet): View
    {
        $cabinet->load(['shifts.doctor', 'shifts.nurse', 'branch', 'ambulatoriya']);

        $branches = Branch::orderBy('name')->get();
        $ambulatorii = Ambulatoriya::orderBy('name')->get();
        $doctors = MedicalStaff::doctors()->active()->orderBy('last_name')->get();
        $nurses = MedicalStaff::nurses()->active()->orderBy('last_name')->get();

        return view('cabinets.edit', compact('cabinet', 'branches', 'ambulatorii', 'doctors', 'nurses'));
    }

    public function update(UpdateCabinetRequest $request, Cabinet $cabinet): RedirectResponse
    {
        $cabinet->update($request->validated());

        return redirect()->route('cabinets.edit', $cabinet)->with('success', 'Кабінет оновлено');
    }

    public function destroy(Cabinet $cabinet): RedirectResponse
    {
        $hasAssignments = $cabinet->shifts()
            ->where(fn ($query) => $query->whereNotNull('doctor_id')->orWhereNotNull('nurse_id'))
            ->exists();

        if ($hasAssignments) {
            return redirect()->back()
                ->withErrors(['Неможливо видалити кабінет із призначеними змінами']);
        }

        $cabinet->delete();

        return redirect()->route('cabinets.index')->with('success', 'Кабінет видалено');
    }
}
