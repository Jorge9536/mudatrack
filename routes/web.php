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

Route::get('/seguimiento/{token}', [GpsController::class, 'seguimientoPublico'])
    ->name('seguimiento.publico');

Route::get('/seguimiento/{token}/ubicacion', [GpsController::class, 'ubicacionPublica'])
    ->name('seguimiento.publico.ubicacion');


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify.form');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::delete('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('2fa.recovery');
    Route::post('/2fa/recovery-verify', [TwoFactorController::class, 'verifyRecoveryCode'])->name('2fa.recovery.verify');
});

Route::middleware(['auth', '2fa'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::resource('clientes', ClienteController::class);
        Route::post('/clientes/{cliente}/toggle-bloqueo', [ClienteController::class, 'toggleBloqueo'])
            ->name('clientes.toggle-bloqueo');
    });
    Route::middleware(['role:admin,recepcionista,chofer,ayudante'])->group(function () {
        Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
    });

    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::get('/servicios/create', [ServicioController::class, 'create'])->name('servicios.create');
        Route::post('/servicios', [ServicioController::class, 'store'])->name('servicios.store');
    });

    Route::middleware(['role:admin,recepcionista,chofer,ayudante'])->group(function () {
        Route::get('/servicios/{servicio}', [ServicioController::class, 'show'])->name('servicios.show');
        Route::get('/servicios/{servicio}/comprobante', [ServicioController::class, 'generarComprobante'])->name('servicios.comprobante');
    });

    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::get('/servicios/{servicio}/edit', [ServicioController::class, 'edit'])->name('servicios.edit');
        Route::put('/servicios/{servicio}', [ServicioController::class, 'update'])->name('servicios.update');

        Route::get('/servicios/{servicio}/asignar', [ServicioController::class, 'showAsignarForm'])->name('servicios.asignar.form');
        Route::post('/servicios/{servicio}/asignar', [ServicioController::class, 'assignPersonal'])->name('servicios.asignar');
        Route::post('/servicios/{servicio}/pago', [ServicioController::class, 'registrarPago'])->name('servicios.pago');
        Route::post('/servicios/{servicio}/enviar-whatsapp', [ServicioController::class, 'enviarWhatsApp'])->name('servicios.enviar.whatsapp');
        Route::post('/servicios/{servicio}/enviar-seguimiento', [ServicioController::class, 'enviarSeguimiento'])->name('servicios.enviar.seguimiento');
    });

    Route::middleware(['role:admin,recepcionista,chofer'])->group(function () {
        Route::put('/servicios/{servicio}/estado', [ServicioController::class, 'updateStatus'])->name('servicios.estado');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::delete('/servicios/{servicio}', [ServicioController::class, 'destroy'])->name('servicios.destroy');
    });

    Route::middleware(['role:admin,recepcionista'])->prefix('pagos')->group(function () {
        Route::get('/configuracion-qr', [PagoController::class, 'configuracionQr'])->name('pagos.configuracion-qr');
        Route::put('/configuracion-qr', [PagoController::class, 'actualizarQr'])->name('pagos.configuracion-qr.update');
    });

    Route::middleware(['role:admin,recepcionista,chofer'])->prefix('pagos')->group(function () {
        Route::get('/{servicio}', [PagoController::class, 'index'])->name('pagos.index');
        Route::post('/{servicio}/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
    });

    Route::prefix('gps')->group(function () {
        Route::middleware(['role:admin,recepcionista,chofer,ayudante'])->group(function () {
            Route::get('/', [GpsController::class, 'index'])->name('gps.index');
            Route::get('/seguimiento/{id}', [GpsController::class, 'seguimiento'])->name('gps.seguimiento');
            Route::get('/{id}/ultima', [GpsController::class, 'ultimaUbicacion'])->name('gps.ultima');
            Route::get('/{id}/historial', [GpsController::class, 'historial'])->name('gps.historial');
            Route::get('/dispositivo/{dispositivoId}/ubicacion', [GpsController::class, 'getUbicacionDispositivoApi'])
                ->name('gps.dispositivo.ubicacion');
        });

        Route::middleware(['role:admin,recepcionista'])->group(function () {
            Route::post('/actualizar', [GpsController::class, 'actualizar'])->name('gps.actualizar');
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::get('/admin-mapa', [GpsController::class, 'adminMapa'])->name('gps.admin.mapa');
            Route::get('/api/vehiculos', [GpsController::class, 'getUbicacionesVehiculos'])->name('api.gps.vehiculos');
            Route::get('/firebase/ubicaciones', [GpsController::class, 'getFirebaseUbicaciones'])->name('gps.firebase.ubicaciones');
        });
    });

    Route::middleware(['role:admin,recepcionista'])->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/exportar', [ReporteController::class, 'exportar'])->name('exportar');

        Route::get('/personal', [ReporteController::class, 'personal'])->name('personal');
        Route::get('/personal/exportar', [ReporteController::class, 'exportarPersonal'])->name('personal.exportar');

        Route::get('/morosos', [ReporteController::class, 'morosos'])->name('morosos');
        Route::get('/morosos/exportar', [ReporteController::class, 'exportarMorosos'])->name('morosos.exportar');
        Route::get('/morosos/{clienteId}/deudas', [ReporteController::class, 'deudasCliente'])->name('morosos.deudas');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/choferes', [ChoferController::class, 'index'])->name('choferes.index');
        Route::get('/choferes/create', [ChoferController::class, 'create'])->name('choferes.create');
        Route::post('/choferes', [ChoferController::class, 'store'])->name('choferes.store');
        Route::get('/choferes/{chofer}', [ChoferController::class, 'show'])->name('choferes.show');
        Route::get('/choferes/{chofer}/edit', [ChoferController::class, 'edit'])->name('choferes.edit');
        Route::put('/choferes/{chofer}', [ChoferController::class, 'update'])->name('choferes.update');
        Route::delete('/choferes/{chofer}', [ChoferController::class, 'destroy'])->name('choferes.destroy');
    });

    Route::middleware(['role:chofer'])->group(function () {
        Route::get('/chofer-panel', [ChoferController::class, 'panel'])->name('choferes.panel');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('vehiculos', VehiculoController::class);
        Route::post('/vehiculos/{vehiculo}/toggle-disponibilidad', [VehiculoController::class, 'toggleDisponibilidad'])
            ->name('vehiculos.toggle-disponibilidad');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('ayudantes', AyudanteController::class);
        Route::post('/ayudantes/{ayudante}/toggle-disponibilidad', [AyudanteController::class, 'toggleDisponibilidad'])
            ->name('ayudantes.toggle-disponibilidad');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dispositivos/firestore/lista', [DispositivoController::class, 'obtenerDeFirestore'])
            ->name('dispositivos.firestore.lista');

        Route::delete('/dispositivos/firestore/{dispositivoId}/eliminar', [DispositivoController::class, 'eliminarDeFirestorePorId'])
            ->name('dispositivos.firestore.eliminar');

        Route::delete('/dispositivos/{dispositivo}/eliminar-firestore', [DispositivoController::class, 'eliminarDeFirestore'])
            ->name('dispositivos.eliminar.firestore');

        Route::resource('dispositivos', DispositivoController::class);
    });
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/configuracion/precios', [ConfiguracionPrecioController::class, 'index'])->name('configuracion.precios');
        Route::put('/configuracion/precios', [ConfiguracionPrecioController::class, 'update'])->name('configuracion.precios.update');
    });

    Route::middleware(['role:admin,recepcionista,chofer,ayudante'])->group(function () {
        Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('/api/eventos', [CalendarioController::class, 'eventos'])->name('api.eventos');
    });

    Route::middleware(['role:admin,recepcionista'])->group(function () {
        Route::get('/api/recursos-disponibles', [CalendarioController::class, 'recursosDisponibles'])->name('api.recursos-disponibles');
    });

});