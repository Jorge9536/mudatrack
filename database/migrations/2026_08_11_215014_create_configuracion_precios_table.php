<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_precios', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio_la_paz', 10, 2)->default(300.00);
            $table->decimal('precio_el_alto', 10, 2)->default(200.00);
            $table->decimal('precio_el_alto_la_paz', 10, 2)->default(250.00);
            $table->decimal('costo_ayudante', 10, 2)->default(80.00);
            $table->decimal('costo_piso_adicional', 10, 2)->default(20.00);
            $table->decimal('costo_callejon', 10, 2)->default(30.00);
            $table->decimal('costo_km_extra', 10, 2)->default(5.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_precios');
    }
};