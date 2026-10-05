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
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\DispositivoController;

// ============================================
// 🔥 RUTAS PÚBLICAS DE SEGUIMIENTO (SIN LOGIN)
// ============================================
Route::get('/seguimiento/{token}', [GpsController::class, 'seguimientoPublico'])
    ->name('seguimiento.publico');

Route::get('/seguimiento/{token}/ubicacion', [GpsController::class, 'ubicacionPublica'])
    ->name('seguimiento.publico.ubicacion');

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
// RUTAS DE 2FA
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
// RUTAS PROTEGIDAS
// ============================================
Route::middleware(['auth', '2fa'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CLIENTES
    Route::resource('clientes', ClienteController::class);
    Route::post('/clientes/{cliente}/toggle-bloqueo', [ClienteController::class, 'toggleBloqueo'])->name('clientes.toggle-bloqueo');

    // SERVICIOS
    Route::resource('servicios', ServicioController::class);
    Route::put('/servicios/{servicio}/estado', [ServicioController::class, 'updateStatus'])->name('servicios.estado');
    Route::get('/servicios/{servicio}/asignar', [ServicioController::class, 'showAsignarForm'])->name('servicios.asignar.form');
    Route::post('/servicios/{servicio}/asignar', [ServicioController::class, 'assignPersonal'])->name('servicios.asignar');
    Route::get('/servicios/{servicio}/comprobante', [ServicioController::class, 'generarComprobante'])->name('servicios.comprobante');
    Route::post('/servicios/{servicio}/pago', [ServicioController::class, 'registrarPago'])->name('servicios.pago');
    Route::post('/servicios/{servicio}/enviar-whatsapp', [ServicioController::class, 'enviarWhatsApp'])->name('servicios.enviar.whatsapp');

    // PAGOS
    Route::prefix('pagos')->group(function () {
        Route::get('/configuracion-qr', [PagoController::class, 'configuracionQr'])->name('pagos.configuracion-qr');
        Route::put('/configuracion-qr', [PagoController::class, 'actualizarQr'])->name('pagos.configuracion-qr.update');
        Route::get('/{servicio}', [PagoController::class, 'index'])->name('pagos.index');
        Route::post('/{servicio}/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
    });

    // GPS / SEGUIMIENTO (privado)
    Route::prefix('gps')->group(function () {
        Route::get('/', [GpsController::class, 'index'])->name('gps.index');
        Route::get('/seguimiento/{id}', [GpsController::class, 'seguimiento'])->name('gps.seguimiento');
        Route::post('/actualizar', [GpsController::class, 'actualizar'])->name('gps.actualizar');
        Route::get('/{id}/ultima', [GpsController::class, 'ultimaUbicacion'])->name('gps.ultima');
        Route::get('/{id}/historial', [GpsController::class, 'historial'])->name('gps.historial');
        Route::get('/firebase/ubicaciones', [GpsController::class, 'getFirebaseUbicaciones'])->name('gps.firebase.ubicaciones');
        
        Route::get('/dispositivo/{dispositivoId}/ubicacion', [GpsController::class, 'getUbicacionDispositivoApi'])
            ->name('gps.dispositivo.ubicacion');
        
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin-mapa', [GpsController::class, 'adminMapa'])->name('gps.admin.mapa');
            Route::get('/api/vehiculos', [GpsController::class, 'getUbicacionesVehiculos'])->name('api.gps.vehiculos');
        });
    });

    // CHOFERES
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

    // VEHÍCULOS
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('vehiculos', VehiculoController::class);
        Route::post('/vehiculos/{vehiculo}/toggle-disponibilidad', [VehiculoController::class, 'toggleDisponibilidad'])->name('vehiculos.toggle-disponibilidad');
    });

    // ============================================
    // 🔥 REPORTES (ACTUALIZADO CON NUEVA RUTA)
    // ============================================
    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
        Route::get('/reportes/morosos', [ReporteController::class, 'morosos'])->name('reportes.morosos');
        Route::get('/reportes/morosos/{clienteId}/deudas', [ReporteController::class, 'deudasCliente'])->name('reportes.morosos.deudas');
    });

    // AYUDANTES
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('ayudantes', AyudanteController::class);
        Route::post('/ayudantes/{ayudante}/toggle-disponibilidad', [AyudanteController::class, 'toggleDisponibilidad'])->name('ayudantes.toggle-disponibilidad');
    });

    // DISPOSITIVOS GPS
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dispositivos/firestore/lista', [DispositivoController::class, 'obtenerDeFirestore'])
            ->name('dispositivos.firestore.lista');

        Route::delete('/dispositivos/firestore/{dispositivoId}/eliminar', [DispositivoController::class, 'eliminarDeFirestorePorId'])
            ->name('dispositivos.firestore.eliminar');

        Route::delete('/dispositivos/{dispositivo}/eliminar-firestore', [DispositivoController::class, 'eliminarDeFirestore'])
            ->name('dispositivos.eliminar.firestore');

        Route::resource('dispositivos', DispositivoController::class);
    });

    // USUARIOS
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // CONFIGURACIÓN PRECIOS
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/configuracion/precios', [ConfiguracionPrecioController::class, 'index'])->name('configuracion.precios');
        Route::put('/configuracion/precios', [ConfiguracionPrecioController::class, 'update'])->name('configuracion.precios.update');
    });

    // CALENDARIO
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/api/eventos', [CalendarioController::class, 'eventos'])->name('api.eventos');
    Route::get('/api/recursos-disponibles', [CalendarioController::class, 'recursosDisponibles'])->name('api.recursos-disponibles');

});