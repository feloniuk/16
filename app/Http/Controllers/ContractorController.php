<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\ContractorOperation;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,warehouse_manager']);
    }

    public function index(Request $request)
    {
        $query = Contractor::withCount('operations');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $contractors = $query->orderBy('name')->paginate(20);

        return view('contractors.index', compact('contractors'));
    }

    public function show(Contractor $contractor)
    {
        $contractor->load(['operations' => function($query) {
            $query->with(['user', 'inventory'])->orderBy('operation_date', 'desc');
        }]);
        
        return view('contractors.show', compact('contractor'));
    }

    public function create()
    {
        return view('contractors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'type' => 'required|in:repair,supply,service',
            'notes' => 'nullable|string|max:1000',
        ]);

        Contractor::create($request->all());

        return redirect()->route('contractors.index')
            ->with('success', 'Подрядчик добавлен');
    }

    public function edit(Contractor $contractor)
    {
        return view('contractors.edit', compact('contractor'));
    }

    public function update(Request $request, Contractor $contractor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'type' => 'required|in:repair,supply,service',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $contractor->update($request->all());

        return redirect()->route('contractors.index')
            ->with('success', 'Подрядчик обновлен');
    }

    public function destroy(Contractor $contractor)
    {
        if ($contractor->operations()->count() > 0) {
            return redirect()->back()
                ->withErrors(['Нельзя удалить подрядчика с существующими операциями']);
        }

        $contractor->delete();
        
        return redirect()->route('contractors.index')
            ->with('success', 'Подрядчик удален');
    }
}