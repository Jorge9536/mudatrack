<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chofer;
use App\Models\Ayudante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        $roles = User::rolesDisponibles();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // 1. Reglas de validación base
        $rules = [
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'in:admin,recepcionista,chofer,ayudante'],
        ];

        // 2. Reglas condicionales
        if (in_array($request->role, ['chofer', 'ayudante'])) {
            $rules['telefono'] = ['required', 'string', 'max:20'];
        }

        if ($request->role === 'chofer') {
            $rules['licencia'] = ['required', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        // 3. Transacción para guardar al usuario y a su perfil
        DB::beginTransaction();

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
            ]);

            if ($request->role === 'chofer') {
                Chofer::create([
                    'user_id'         => $user->id,
                    'nombre_completo' => $user->name,
                    'telefono'        => $request->telefono,
                    'licencia'        => $request->licencia,
                    'disponible'      => true,
                ]);
            } elseif ($request->role === 'ayudante') {
                Ayudante::create([
                    'user_id'         => $user->id,
                    'nombre_completo' => $user->name,
                    'telefono'        => $request->telefono,
                    'disponible'      => true,
                ]);
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', '✅ Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes editar al usuario administrador principal.');
        }

        $roles = User::rolesDisponibles();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes editar al usuario administrador principal.');
        }

        $rules = [
            'name'  => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:admin,recepcionista,chofer,ayudante'],
        ];

        if (in_array($request->role, ['chofer', 'ayudante'])) {
            $rules['telefono'] = ['required', 'string', 'max:20'];
        }

        if ($request->role === 'chofer') {
            $rules['licencia'] = ['required', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            $user->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'role'  => $validated['role'],
            ]);

            // Actualizar o crear el perfil según el nuevo rol
            if ($request->role === 'chofer') {
                Chofer::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nombre_completo' => $user->name,
                        'telefono'        => $request->telefono,
                        'licencia'        => $request->licencia,
                    ]
                );
                // Si el usuario ya no es ayudante, eliminamos su perfil de ayudante si existía
                Ayudante::where('user_id', $user->id)->delete();
                
            } elseif ($request->role === 'ayudante') {
                Ayudante::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nombre_completo' => $user->name,
                        'telefono'        => $request->telefono,
                    ]
                );
                Chofer::where('user_id', $user->id)->delete();
            } else {
                // Si el rol cambió a admin o recepcionista, eliminamos perfiles de personal
                Chofer::where('user_id', $user->id)->delete();
                Ayudante::where('user_id', $user->id)->delete();
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', '✅ Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes eliminar al usuario administrador principal.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes eliminarte a ti mismo.');
        }

        DB::beginTransaction();
        try {
            // Eliminar perfiles asociados antes de eliminar el usuario
            Chofer::where('user_id', $user->id)->delete();
            Ayudante::where('user_id', $user->id)->delete();
            
            $user->delete();
            DB::commit();
            
            return redirect()->route('users.index')
                ->with('success', '✅ Usuario eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.index')
                ->with('error', '❌ Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', '❌ No puedes resetear la contraseña del administrador principal.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', '✅ Contraseña actualizada exitosamente.');
    }
}