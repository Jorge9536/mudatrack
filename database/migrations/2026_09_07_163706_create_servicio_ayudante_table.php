<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servicio_ayudante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->foreignId('ayudante_id')->constrained('ayudantes')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['servicio_id', 'ayudante_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('servicio_ayudante');
    }
};