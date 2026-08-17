<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Mostrar formulario de verificación 2FA
    public function showVerifyForm()
    {
        return view('auth.verify-2fa');
    }

    // Verificar código 2FA
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string|size:6'
        ]);

        $user = auth()->user();

        if (!$user->google2fa_enabled) {
            return redirect()->route('dashboard');
        }

        $secret = decrypt($user->google2fa_secret);
        $google2fa = app('pragmarx.google2fa'); // ← Obtener instancia
        $valid = $google2fa->verifyKey($secret, $request->one_time_password);

        if ($valid) {
            $request->session()->put('2fa_passed', true);
            return redirect()->intended('/dashboard');
        }

        return back()->with('error', '❌ Código inválido. Intenta de nuevo.');
    }

    // Verificar código de recuperación
    public function verifyRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string|size:8'
        ]);

        $user = auth()->user();
        $codes = $user->recovery_codes ?? [];

        $index = array_search(strtoupper($request->recovery_code), array_map('strtoupper', $codes));

        if ($index !== false) {
            unset($codes[$index]);
            $user->update(['recovery_codes' => array_values($codes)]);

            $request->session()->put('2fa_passed', true);
            return redirect()->intended('/dashboard');
        }

        return back()->with('error', '❌ Código de recuperación inválido.');
    }

    // Mostrar configuración de 2FA
    public function showSetup()
    {
        $user = auth()->user();

        if ($user->google2fa_enabled) {
            return view('profile.two-factor');
        }

        try {
            $google2fa = app('pragmarx.google2fa'); // ← Obtener instancia
            $secret = $google2fa->generateSecretKey();
            
            $qrCode = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $secret
            );

            session(['2fa_secret' => $secret]);

            return view('profile.two-factor', compact('qrCode', 'secret'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Error al generar el código QR: ' . $e->getMessage());
        }
    }

    // Activar 2FA
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = auth()->user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return back()->with('error', 'No hay secreto en sesión. Por favor, intenta de nuevo.');
        }

        try {
            $google2fa = app('pragmarx.google2fa'); // ← Obtener instancia
            
            if ($google2fa->verifyKey($secret, $request->code)) {
                $codes = $this->generateRecoveryCodes();

                $user->update([
                    'google2fa_secret' => encrypt($secret),
                    'google2fa_enabled' => true,
                    'recovery_codes' => $codes
                ]);

                session()->forget('2fa_secret');
                session()->put('2fa_passed', true);

                return redirect()->route('dashboard')
                    ->with('success', '✅ 2FA activado correctamente. Guarda tus códigos de recuperación.');
            }

            return back()->with('error', '❌ Código inválido. Intenta de nuevo.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al activar 2FA: ' . $e->getMessage());
        }
    }

    // Desactivar 2FA
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        $user = auth()->user();
        $user->update([
            'google2fa_secret' => null,
            'google2fa_enabled' => false,
            'recovery_codes' => null
        ]);

        session()->forget('2fa_passed');

        return redirect()->route('dashboard')
            ->with('success', '2FA desactivado correctamente.');
    }

    // Mostrar códigos de recuperación
    public function showRecoveryCodes()
    {
        $user = auth()->user();

        if (!$user->google2fa_enabled) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes 2FA activado.');
        }

        return view('profile.recovery-codes', [
            'codes' => $user->recovery_codes ?? []
        ]);
    }

    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(8));
        }
        return $codes;
    }
}