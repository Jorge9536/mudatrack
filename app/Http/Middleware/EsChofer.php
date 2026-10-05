<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsChofer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isChofer()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado. Solo choferes pueden acceder.',
            ], 403);
        }

        if (!$user->chofer) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario chofer sin perfil de chofer asociado. Contacta al administrador.',
            ], 403);
        }

        return $next($request);
    }
}