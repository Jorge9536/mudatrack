<?php

namespace App\Observers;

use App\Models\Servicio;
use App\Services\Firebase\FirestoreRestService;
use Carbon\Carbon;

class ServicioObserver
{
    protected FirestoreRestService $firestore;

    public function __construct(FirestoreRestService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function created(Servicio $servicio): void
    {
        $this->replicar($servicio);
    }

    public function updated(Servicio $servicio): void
    {
        $this->replicar($servicio);
    }

    public function deleted(Servicio $servicio): void
    {
        try {
            $this->firestore->delete('servicios', (string) $servicio->id);
        } catch (\Throwable $e) {
            $this->guardarError($servicio->id, $e);
        }
    }

    protected function replicar(Servicio $servicio): void
    {
        try {
            $servicio->load(['cliente', 'chofer', 'vehiculo', 'ayudantes']);

            $datos = [
                'id' => $servicio->id,
                'chofer_id' => $servicio->chofer_id,
                'estado' => $servicio->estado,
                'estado_label' => $servicio->estado_label,
                'estado_pago' => $servicio->estado_pago,
                'fecha_servicio' => $servicio->fecha_servicio?->format('Y-m-d'),
                'hora_inicio' => $servicio->hora_inicio ? Carbon::parse($servicio->hora_inicio)->format('H:i') : null,
                'hora_fin' => $servicio->hora_fin ? Carbon::parse($servicio->hora_fin)->format('H:i') : null,
                'origen' => $servicio->origen,
                'destino' => $servicio->destino,
                'costo_total' => (float) $servicio->costo_total,
                'observaciones' => $servicio->observaciones,
                'cliente' => $servicio->cliente ? [
                    'id' => $servicio->cliente->id,
                    'nombre_completo' => $servicio->cliente->nombre_completo,
                    'telefono' => $servicio->cliente->telefono,
                    'direccion' => $servicio->cliente->direccion,
                    'latitud' => $servicio->cliente->latitud ? (float) $servicio->cliente->latitud : null,
                    'longitud' => $servicio->cliente->longitud ? (float) $servicio->cliente->longitud : null,
                ] : null,
                'vehiculo' => $servicio->vehiculo ? [
                    'id' => $servicio->vehiculo->id,
                    'placa' => $servicio->vehiculo->placa,
                    'marca' => $servicio->vehiculo->marca,
                    'modelo' => $servicio->vehiculo->modelo,
                    'tipo' => $servicio->vehiculo->tipo_label,
                ] : null,
                'actualizado' => now()->toIso8601String(),
            ];

            $resultado = $this->firestore->set('servicios', (string) $servicio->id, $datos);

            if (!$resultado) {
                $this->guardarError($servicio->id, new \Exception('Firestore set() devolvio false (ver logs de Laravel)'));
            }
        } catch (\Throwable $e) {
            $this->guardarError($servicio->id, $e);
        }
    }

    /**
     * Guardar error en archivo separado SIN problemas de codificación
     */
    protected function guardarError(int $servicioId, \Throwable $e): void
    {
        $logFile = storage_path('logs/firestore_error.log');
        $mensaje = str_repeat("=", 80) . "\n";
        $mensaje .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
        $mensaje .= "Servicio: #{$servicioId}\n";
        $mensaje .= "Error: " . $e->getMessage() . "\n";
        $mensaje .= "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        $mensaje .= "Trace:\n" . $e->getTraceAsString() . "\n";
        $mensaje .= str_repeat("=", 80) . "\n\n";

        file_put_contents($logFile, $mensaje, FILE_APPEND);
    }
}