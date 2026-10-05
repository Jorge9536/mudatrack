<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Servicio;
use App\Observers\ServicioObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔥 Observer de Servicio → replica a Firestore
        Servicio::observe(ServicioObserver::class);
    }
}