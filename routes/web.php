<?php

use Illuminate\Support\Facades\Route;
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
use App\Models\User;

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
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// ============================================
Route::middleware(['auth'])->group(function () {

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
    Route::get('/pagos/configuracion-qr', [PagoController::class, 'configuracionQr'])->name('pagos.configuracion-qr');
    Route::put('/pagos/configuracion-qr', [PagoController::class, 'actualizarQr'])->name('pagos.configuracion-qr.update');
    Route::get('/pagos/{servicio}', [PagoController::class, 'index'])->name('pagos.index');
    Route::post('/pagos/{servicio}/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');

    // ---------- GPS / SEGUIMIENTO (Todos los roles) ----------
    Route::get('/gps', [GpsController::class, 'index'])->name('gps.index');
    Route::get('/seguimiento/{servicio}', [GpsController::class, 'seguimiento'])->name('gps.seguimiento');
    Route::post('/gps/actualizar', [GpsController::class, 'actualizar'])->name('gps.actualizar');
    Route::get('/gps/{servicio}/ultima', [GpsController::class, 'ultimaUbicacion'])->name('gps.ultima');
    Route::get('/gps/{servicio}/historial', [GpsController::class, 'historial'])->name('gps.historial');

    // ---------- CHOFERES (SOLO ADMIN) ----------
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('choferes', ChoferController::class);
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
Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
});
Route::middleware(['role:admin'])->group(function () {
    Route::get('/configuracion/precios', [ConfiguracionPrecioController::class, 'index'])->name('configuracion.precios');
    Route::put('/configuracion/precios', [ConfiguracionPrecioController::class, 'update'])->name('configuracion.precios.update');
});
});