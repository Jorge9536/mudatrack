<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            if (!Schema::hasColumn('servicios', 'estado_pago')) {
                $table->enum('estado_pago', ['pendiente', 'pagado'])
                      ->default('pendiente')
                      ->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            if (Schema::hasColumn('servicios', 'estado_pago')) {
                $table->dropColumn('estado_pago');
            }
        });
    }
};