<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Servicio;
use App\Observers\ServicioObserver;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Servicio::observe(ServicioObserver::class);
    }
}