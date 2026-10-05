<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servicio;
use App\Models\Deuda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActualizarEstadosServicios extends Command
{
    protected $signature = 'servicios:actualizar-estados';
    protected $description = 'Actualiza automáticamente los estados de los servicios según fecha y hora';

    public function handle()
    {
        $ahora = Carbon::now();
        $cambios = 0;

        // ============================================
        // 1. CONFIRMADO → EN_PROGRESO
        // ============================================
        $servicios = Servicio::where('estado', 'confirmado')
            ->whereNotNull('hora_inicio')
            ->get();

        foreach ($servicios as $servicio) {
            $fechaHoraInicio = Carbon::parse(
                $servicio->fecha_servicio->format('Y-m-d') . ' ' . 
                \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i:s')
            );

            if ($fechaHoraInicio->lessThanOrEqualTo($ahora)) {
                $servicio->update(['estado' => 'en_progreso']);
                $cambios++;
                $this->info("✅ Servicio #{$servicio->id} → En Progreso");
                Log::info("Servicio #{$servicio->id} cambió a en_progreso");
            }
        }

        // ============================================
        // 2. EN_PROGRESO → FINALIZADO
        // 🔥 SOLO cambia el estado operativo, NO el estado_pago
        // ============================================
        $servicios = Servicio::where('estado', 'en_progreso')
            ->whereNotNull('hora_fin')
            ->get();

        foreach ($servicios as $servicio) {
            $fechaHoraFin = Carbon::parse(
                $servicio->fecha_servicio->format('Y-m-d') . ' ' . 
                \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i:s')
            );

            if ($fechaHoraFin->lessThanOrEqualTo($ahora)) {
                // Solo cambiar el estado operativo
                $servicio->update(['estado' => 'finalizado']);
                
                // Si no está pagado, crear deuda (para gestión)
                if (!$servicio->estaPagado() && !$servicio->deuda) {
                    Deuda::create([
                        'cliente_id' => $servicio->cliente_id,
                        'servicio_id' => $servicio->id,
                        'monto' => $servicio->costo_total,
                        'fecha_vencimiento' => now()->addDays(1),
                        'estado' => 'pendiente',
                        'observaciones' => 'Servicio finalizado sin pago (automático)'
                    ]);
                }
                
                $cambios++;
                
                if ($servicio->estaPagado()) {
                    $this->info("✅ Servicio #{$servicio->id} → Finalizado (Pagado)");
                } else {
                    $this->info("⏳ Servicio #{$servicio->id} → Finalizado (Pendiente de Pago)");
                }
                
                Log::info("Servicio #{$servicio->id} finalizó automáticamente");
            }
        }

        if ($cambios === 0) {
            $this->info('ℹ️  No hay servicios para actualizar');
        } else {
            $this->info("🎉 Total: {$cambios} servicios actualizados");
        }

        return Command::SUCCESS;
    }
}