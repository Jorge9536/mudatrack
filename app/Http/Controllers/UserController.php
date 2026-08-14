<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $users = User::orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,recepcionista,chofer'
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        return redirect()->route('users.index')
            ->with('success', '✅ Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // No permitir editar al usuario admin principal (ID 1)
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes editar al usuario administrador principal.');
        }
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // No permitir editar al usuario admin principal (ID 1)
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes editar al usuario administrador principal.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,recepcionista,chofer'
        ]);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', '✅ Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // No permitir eliminar al usuario admin principal (ID 1)
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes eliminar al usuario administrador principal.');
        }

        // No permitir eliminarse a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes eliminarte a ti mismo.');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', '✅ Usuario eliminado exitosamente.');
    }

    public function resetPassword(Request $request, User $user)
    {
        // No permitir resetear contraseña del admin principal (ID 1)
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes resetear la contraseña del administrador principal.');
        }

        $validated = $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('users.index')
            ->with('success', '✅ Contraseña actualizada exitosamente.');
    }
}