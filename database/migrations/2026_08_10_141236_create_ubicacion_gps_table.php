<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubicacion_gps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->decimal('velocidad', 5, 2)->nullable();
            $table->datetime('fecha_hora');
            $table->timestamps();
            
            $table->index(['servicio_id', 'fecha_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubicacion_gps');
    }
};