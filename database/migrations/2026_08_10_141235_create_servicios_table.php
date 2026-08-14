<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->onDelete('set null');
            $table->foreignId('chofer_id')->nullable()->constrained('choferes')->onDelete('set null');
            
            $table->string('origen');
            $table->string('destino');
            $table->date('fecha_servicio');
            $table->datetime('hora_inicio')->nullable();
            $table->datetime('hora_fin')->nullable();
            
            $table->integer('cantidad_ayudantes')->default(0);
            $table->integer('numero_pisos')->default(1);
            $table->boolean('es_callejon')->default(false);
            
            $table->decimal('costo_total', 10, 2)->default(0);
            
            $table->enum('estado', [
                'pendiente', 
                'confirmado', 
                'en_progreso', 
                'finalizado', 
                'cancelado',
                'pendiente_pago',
                'pagado'
            ])->default('pendiente');
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['estado', 'fecha_servicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};