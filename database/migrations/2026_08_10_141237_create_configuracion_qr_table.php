<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_qr', function (Blueprint $table) {
            $table->id();
            $table->string('imagen_qr')->nullable();
            $table->string('url_qr')->nullable();
            $table->timestamp('fecha_actualizacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_qr');
    }
};