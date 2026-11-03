<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->google2fa_enabled) {
            return $next($request);
        }


        if ($request->session()->get('2fa_passed')) {
            return $next($request);
        }

        return redirect()->route('2fa.verify.form');
    }
}