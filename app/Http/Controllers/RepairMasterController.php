<?php 
// app/Http/Controllers/RepairMasterController.php
namespace App\Http\Controllers;

use App\Models\RepairMaster;
use Illuminate\Http\Request;

class RepairMasterController extends Controller
{
    public function index()
    {
        $masters = RepairMaster::withCount('repairTrackings')->orderBy('name')->get();
        return view('repair-masters.index', compact('masters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        RepairMaster::create($request->all());

        return redirect()->route('repair-masters.index')
            ->with('success', 'Майстра додано');
    }

    public function update(Request $request, RepairMaster $repairMaster)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20', 
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $repairMaster->update($request->all());

        return redirect()->route('repair-masters.index')
            ->with('success', 'Дані майстра оновлено');
    }

    public function destroy(RepairMaster $repairMaster)
    {
        if ($repairMaster->repairTrackings()->count() > 0) {
            return redirect()->back()
                ->withErrors(['Неможливо видалити майстра, який має записи про ремонти']);
        }

        $repairMaster->delete();
        return redirect()->route('repair-masters.index')
            ->with('success', 'Майстра видалено');
    }
}