<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['inventoryLogs' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,warehouse_manager,director',
            'is_active' => 'boolean',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'role', 'is_active']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Пользователь обновлен');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->withErrors(['Нельзя удалить свой собственный аккаунт']);
        }

        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'Пользователь удален');
    }
}