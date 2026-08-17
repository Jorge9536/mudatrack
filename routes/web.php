<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ChoferController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\GpsController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AyudanteController;
use App\Http\Controllers\ConfiguracionPrecioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TwoFactorController;

// ============================================
// RUTAS DE AUTENTICACIÓN (PÚBLICAS)
// ============================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ============================================
// RUTA PRINCIPAL
// ============================================
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ============================================
// RUTAS DE 2FA (FUERA DEL MIDDLEWARE 2FA)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify.form');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::delete('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('2fa.recovery');
    Route::post('/2fa/recovery-verify', [TwoFactorController::class, 'verifyRecoveryCode'])->name('2fa.recovery.verify');
});

// ============================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN Y 2FA)
// ============================================
Route::middleware(['auth', '2fa'])->group(function () {

    // ---------- DASHBOARD (Todos los roles) ----------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------- CLIENTES (Todos los roles) ----------
    Route::resource('clientes', ClienteController::class);
    Route::post('/clientes/{cliente}/toggle-bloqueo', [ClienteController::class, 'toggleBloqueo'])->name('clientes.toggle-bloqueo');

    // ---------- SERVICIOS (Todos los roles) ----------
    Route::resource('servicios', ServicioController::class);
    Route::put('/servicios/{servicio}/estado', [ServicioController::class, 'updateStatus'])->name('servicios.estado');
    Route::get('/servicios/{servicio}/asignar', [ServicioController::class, 'showAsignarForm'])->name('servicios.asignar.form');
    Route::post('/servicios/{servicio}/asignar', [ServicioController::class, 'assignPersonal'])->name('servicios.asignar');
    Route::get('/servicios/{servicio}/comprobante', [ServicioController::class, 'generarComprobante'])->name('servicios.comprobante');
    Route::post('/servicios/{servicio}/pago', [ServicioController::class, 'registrarPago'])->name('servicios.pago');

    // ---------- PAGOS (Todos los roles) ----------
    // Las rutas de pagos están aquí, dentro del grupo protegido
    Route::prefix('pagos')->group(function () {
        Route::get('/configuracion-qr', [PagoController::class, 'configuracionQr'])->name('pagos.configuracion-qr');
        Route::put('/configuracion-qr', [PagoController::class, 'actualizarQr'])->name('pagos.configuracion-qr.update');
        Route::get('/{servicio}', [PagoController::class, 'index'])->name('pagos.index');
        Route::post('/{servicio}/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
    });

    // ---------- GPS / SEGUIMIENTO (Todos los roles) ----------
    Route::prefix('gps')->group(function () {
        // Lista de servicios con GPS
        Route::get('/', [GpsController::class, 'index'])->name('gps.index');
        
        // Seguimiento de un servicio específico
        Route::get('/seguimiento/{id}', [GpsController::class, 'seguimiento'])->name('gps.seguimiento');
        
        // Actualizar ubicación (desde app móvil)
        Route::post('/actualizar', [GpsController::class, 'actualizar'])->name('gps.actualizar');
        
        // Obtener última ubicación
        Route::get('/{id}/ultima', [GpsController::class, 'ultimaUbicacion'])->name('gps.ultima');
        
        // Obtener historial de ubicaciones
        Route::get('/{id}/historial', [GpsController::class, 'historial'])->name('gps.historial');
        
        // API para Firebase - ubicaciones en tiempo real
        Route::get('/firebase/ubicaciones', [GpsController::class, 'getFirebaseUbicaciones'])->name('gps.firebase.ubicaciones');
        
        // ---------- GPS ADMIN (SOLO ADMIN) ----------
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin-mapa', [GpsController::class, 'adminMapa'])->name('gps.admin.mapa');
            Route::get('/api/vehiculos', [GpsController::class, 'getUbicacionesVehiculos'])->name('api.gps.vehiculos');
        });
    });

    // ---------- CHOFERES (SOLO ADMIN) ----------
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/choferes', [ChoferController::class, 'index'])->name('choferes.index');
        Route::get('/choferes/create', [ChoferController::class, 'create'])->name('choferes.create');
        Route::post('/choferes', [ChoferController::class, 'store'])->name('choferes.store');
        Route::get('/choferes/{chofer}', [ChoferController::class, 'show'])->name('choferes.show');
        Route::get('/choferes/{chofer}/edit', [ChoferController::class, 'edit'])->name('choferes.edit');
        Route::put('/choferes/{chofer}', [ChoferController::class, 'update'])->name('choferes.update');
        Route::delete('/choferes/{chofer}', [ChoferController::class, 'destroy'])->name('choferes.destroy');
        Route::get('/chofer-panel', [ChoferController::class, 'panel'])->name('choferes.panel');
    });

    // ---------- VEHÍCULOS (SOLO ADMIN) ----------
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('vehiculos', VehiculoController::class);
        Route::post('/vehiculos/{vehiculo}/toggle-disponibilidad', [VehiculoController::class, 'toggleDisponibilidad'])->name('vehiculos.toggle-disponibilidad');
    });

    // ---------- REPORTES (SOLO ADMIN Y RECEPCIONISTA) ----------
    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
        Route::get('/reportes/morosos', [ReporteController::class, 'morosos'])->name('reportes.morosos');
    });

    // ---------- AYUDANTES (SOLO ADMIN) ----------
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('ayudantes', AyudanteController::class);
        Route::post('/ayudantes/{ayudante}/toggle-disponibilidad', [AyudanteController::class, 'toggleDisponibilidad'])->name('ayudantes.toggle-disponibilidad');
    });

    // ---------- USUARIOS (SOLO ADMIN) ----------
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // ---------- CONFIGURACIÓN PRECIOS (SOLO ADMIN) ----------
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/configuracion/precios', [ConfiguracionPrecioController::class, 'index'])->name('configuracion.precios');
        Route::put('/configuracion/precios', [ConfiguracionPrecioController::class, 'update'])->name('configuracion.precios.update');
    });

}); // FIN DEL GRUPO PRINCIPAL (auth, 2fa)