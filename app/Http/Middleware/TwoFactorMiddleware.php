<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Si el usuario no está autenticado o no tiene 2FA activado
        if (!$user || !$user->google2fa_enabled) {
            return $next($request);
        }

        // Verificar si ya pasó la verificación 2FA en esta sesión
        if ($request->session()->get('2fa_passed')) {
            return $next($request);
        }

        // Redirigir a la verificación 2FA
        return redirect()->route('2fa.verify.form');
    }
}