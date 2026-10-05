<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ActualizarEstadosServicios;
use App\Console\Commands\SyncUbicacionesFirestore;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🔥 Actualizar estados de servicios cada minuto
Schedule::command(ActualizarEstadosServicios::class)->everyMinute();

// 🔥 Sincronizar ubicaciones de Firestore cada minuto
Schedule::command(SyncUbicacionesFirestore::class)->everyMinute();