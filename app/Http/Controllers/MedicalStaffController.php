<?php

namespace App\Http\Controllers;

use App\Models\Ambulatoriya;
use App\Models\MedicalStaff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalStaffController extends Controller
{
    public function index(Request $request): View
    {
        $staff = MedicalStaff::query()
            ->with('ambulatoriya.branch')
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('ambulatoriya_id'), fn ($query) => $query->where('ambulatoriya_id', $request->input('ambulatoriya_id')))
            ->when($request->filled('branch_id'), fn ($query) => $query->whereHas('ambulatoriya', fn ($q) => $q->where('branch_id', $request->input('branch_id'))))
            ->orderBy('last_name')
            ->paginate(30)
            ->withQueryString();

        $ambulatorii = Ambulatoriya::orderBy('name')->get();

        return view('medical-staff.index', compact('staff', 'ambulatorii'));
    }

    public function update(Request $request, MedicalStaff $medicalStaff): RedirectResponse
    {
        $request->validate([
            'is_active' => ['boolean'],
        ]);

        $medicalStaff->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('medical-staff.index')->with('success', 'Дані співробітника оновлено');
    }
}
