<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ChoferApiController;

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthApiController::class, 'logout']);

    Route::middleware('es_chofer')->prefix('chofer')->group(function () {
        Route::get('/perfil', [ChoferApiController::class, 'perfil']);

        Route::get('/servicios', [ChoferApiController::class, 'servicios']);
        Route::get('/servicios/{id}', [ChoferApiController::class, 'detalleServicio']);

        Route::post('/servicios/{id}/aceptar', [ChoferApiController::class, 'aceptarServicio']);
        Route::post('/servicios/{id}/rechazar', [ChoferApiController::class, 'rechazarServicio']);
        Route::post('/servicios/{id}/iniciar', [ChoferApiController::class, 'iniciarServicio']);
        Route::post('/servicios/{id}/finalizar', [ChoferApiController::class, 'finalizarServicio']);
        Route::post('/servicios/{id}/pago-efectivo', [ChoferApiController::class, 'pagoEfectivo']);
    });
});