<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ActualizarEstadosServicios;
use App\Console\Commands\SyncUbicacionesFirestore;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(ActualizarEstadosServicios::class)->everyMinute();

Schedule::command(SyncUbicacionesFirestore::class)->everyMinute();