<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Login del APK.
     * POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        if (!$user->isChofer()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta app es solo para choferes.',
            ], 403);
        }

        if (!$user->chofer) {
            return response()->json([
                'success' => false,
                'message' => 'Tu usuario no tiene un perfil de chofer asociado. Contacta al administrador.',
            ], 403);
        }

        // Revocar tokens anteriores del mismo dispositivo
        $user->tokens()->where('name', $request->device_name)->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'chofer' => [
                    'id' => $user->chofer->id,
                    'nombre_completo' => $user->chofer->nombre_completo,
                    'telefono' => $user->chofer->telefono,
                ],
            ],
        ]);
    }

    /**
     * Logout.
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}