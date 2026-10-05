<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\Chofer;
use App\Models\Deuda;
use App\Services\Firebase\FirestoreRestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChoferApiController extends Controller
{
    protected FirestoreRestService $firestore;

    public function __construct(FirestoreRestService $firestore)
    {
        $this->firestore = $firestore;
    }

    /**
     * Perfil del chofer autenticado.
     * GET /api/chofer/perfil
     */
    public function perfil(Request $request)
    {
        $user = $request->user();
        $chofer = $user->chofer;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'chofer' => [
                    'id' => $chofer->id,
                    'nombre_completo' => $chofer->nombre_completo,
                    'telefono' => $chofer->telefono,
                    'licencia' => $chofer->licencia,
                    'disponible' => $chofer->disponible,
                ],
            ],
        ]);
    }

    /**
     * Lista de servicios asignados al chofer.
     * GET /api/chofer/servicios
     *
     * Query params opcionales:
     *   ?estado=confirmado|en_progreso|finalizado
     *   ?fecha=YYYY-MM-DD
     */
    public function servicios(Request $request)
    {
        $chofer = $request->user()->chofer;

        $query = Servicio::with(['cliente', 'vehiculo', 'ayudantes'])
            ->where('chofer_id', $chofer->id);

        // Filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        } else {
            // Por defecto: servicios activos (no cancelados ni viejos)
            $query->whereIn('estado', ['pendiente', 'confirmado', 'en_progreso']);
        }

        // Filtrar por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_servicio', $request->fecha);
        } else {
            // Por defecto: desde ayer hasta 7 días adelante
            $query->whereDate('fecha_servicio', '>=', now()->subDay()->format('Y-m-d'))
                  ->whereDate('fecha_servicio', '<=', now()->addDays(7)->format('Y-m-d'));
        }

        $servicios = $query
            ->orderBy('fecha_servicio', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get()
            ->map(fn($s) => $this->formatearServicio($s));

        return response()->json([
            'success' => true,
            'data' => $servicios,
            'total' => $servicios->count(),
        ]);
    }

    /**
     * Detalle de un servicio.
     * GET /api/chofer/servicios/{id}
     */
    public function detalleServicio(Request $request, $id)
    {
        $chofer = $request->user()->chofer;

        $servicio = Servicio::with(['cliente', 'vehiculo', 'ayudantes', 'bienes'])
            ->where('chofer_id', $chofer->id)
            ->find($id);

        if (!$servicio) {
            return response()->json([
                'success' => false,
                'message' => 'Servicio no encontrado o no asignado a ti.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatearServicio($servicio, true),
        ]);
    }

    /**
     * Aceptar un servicio.
     * POST /api/chofer/servicios/{id}/aceptar
     */
    public function aceptarServicio(Request $request, $id)
    {
        $chofer = $request->user()->chofer;
        $servicio = Servicio::where('chofer_id', $chofer->id)->find($id);

        if (!$servicio) {
            return response()->json(['success' => false, 'message' => 'Servicio no encontrado.'], 404);
        }

        if ($servicio->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => "No se puede aceptar un servicio en estado '{$servicio->estado}'.",
            ], 422);
        }

        $servicio->update(['estado' => 'confirmado']);

        // Replicar a Firestore
        $this->replicarServicioAFirestore($servicio);

        return response()->json([
            'success' => true,
            'message' => '✅ Servicio aceptado correctamente.',
            'data' => $this->formatearServicio($servicio->fresh()),
        ]);
    }

    /**
     * Rechazar un servicio.
     * POST /api/chofer/servicios/{id}/rechazar
     */
    public function rechazarServicio(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        $chofer = $request->user()->chofer;
        $servicio = Servicio::where('chofer_id', $chofer->id)->find($id);

        if (!$servicio) {
            return response()->json(['success' => false, 'message' => 'Servicio no encontrado.'], 404);
        }

        if (!in_array($servicio->estado, ['pendiente', 'confirmado'])) {
            return response()->json([
                'success' => false,
                'message' => "No se puede rechazar un servicio en estado '{$servicio->estado}'.",
            ], 422);
        }

        $observaciones = $servicio->observaciones;
        if ($request->filled('motivo')) {
            $observaciones = trim($observaciones . "\n[Rechazado por chofer: {$request->motivo}]");
        }

        $servicio->update([
            'estado' => 'cancelado',
            'observaciones' => $observaciones,
        ]);

        $this->replicarServicioAFirestore($servicio);

        return response()->json([
            'success' => true,
            'message' => 'Servicio rechazado. El administrador fue notificado.',
        ]);
    }

    /**
     * Iniciar un servicio.
     * POST /api/chofer/servicios/{id}/iniciar
     */
    public function iniciarServicio(Request $request, $id)
    {
        $chofer = $request->user()->chofer;
        $servicio = Servicio::where('chofer_id', $chofer->id)->find($id);

        if (!$servicio) {
            return response()->json(['success' => false, 'message' => 'Servicio no encontrado.'], 404);
        }

        if ($servicio->estado !== 'confirmado') {
            return response()->json([
                'success' => false,
                'message' => "Solo se puede iniciar un servicio confirmado. Estado actual: '{$servicio->estado}'.",
            ], 422);
        }

        $servicio->update(['estado' => 'en_progreso']);

        $this->replicarServicioAFirestore($servicio);

        return response()->json([
            'success' => true,
            'message' => '🚚 Servicio iniciado. ¡Buena suerte!',
            'data' => $this->formatearServicio($servicio->fresh()),
        ]);
    }

    /**
     * Finalizar un servicio.
     * POST /api/chofer/servicios/{id}/finalizar
     */
    public function finalizarServicio(Request $request, $id)
    {
        $chofer = $request->user()->chofer;
        $servicio = Servicio::where('chofer_id', $chofer->id)->find($id);

        if (!$servicio) {
            return response()->json(['success' => false, 'message' => 'Servicio no encontrado.'], 404);
        }

        if ($servicio->estado !== 'en_progreso') {
            return response()->json([
                'success' => false,
                'message' => "Solo se puede finalizar un servicio en progreso. Estado actual: '{$servicio->estado}'.",
            ], 422);
        }

        DB::beginTransaction();
        try {
            $servicio->update(['estado' => 'finalizado']);

            // Si no está pagado, crear deuda
            if (!$servicio->estaPagado()) {
                if (!$servicio->deuda) {
                    Deuda::create([
                        'cliente_id' => $servicio->cliente_id,
                        'servicio_id' => $servicio->id,
                        'monto' => $servicio->costo_total,
                        'fecha_vencimiento' => now()->addDays(1),
                        'estado' => 'pendiente',
                        'observaciones' => 'Servicio finalizado sin pago',
                    ]);
                }
            }

            DB::commit();

            $this->replicarServicioAFirestore($servicio);

            return response()->json([
                'success' => true,
                'message' => '✅ Servicio finalizado correctamente.',
                'data' => $this->formatearServicio($servicio->fresh()),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar pago en efectivo.
     * POST /api/chofer/servicios/{id}/pago-efectivo
     */
    public function pagoEfectivo(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
        ]);

        $chofer = $request->user()->chofer;
        $servicio = Servicio::where('chofer_id', $chofer->id)->find($id);

        if (!$servicio) {
            return response()->json(['success' => false, 'message' => 'Servicio no encontrado.'], 404);
        }

        if ($servicio->estado === 'cancelado') {
            return response()->json(['success' => false, 'message' => 'Servicio cancelado.'], 422);
        }

        if ($servicio->estado_pago === 'pagado') {
            return response()->json(['success' => false, 'message' => 'El servicio ya está pagado.'], 422);
        }

        if (abs($request->monto - $servicio->costo_total) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => "El monto ({$request->monto}) no coincide con el total ({$servicio->costo_total}).",
            ], 422);
        }

        DB::beginTransaction();
        try {
            $servicio->update([
                'estado_pago' => 'pagado',
                'metodo_pago' => 'efectivo',
            ]);

            if ($servicio->deuda) {
                $servicio->deuda->update(['estado' => 'pagado']);
            }

            DB::commit();

            // 🔥 IMPORTANTE: Escribir a Firestore para notificar al sistema principal
            $this->firestore->set('pagos_efectivo', (string) $servicio->id, [
                'servicio_id' => $servicio->id,
                'chofer_id' => $chofer->id,
                'monto' => (float) $servicio->costo_total,
                'metodo_pago' => 'efectivo',
                'fecha' => now()->toIso8601String(),
                'procesado' => false,
            ]);

            $this->replicarServicioAFirestore($servicio);

            return response()->json([
                'success' => true,
                'message' => '💵 Pago en efectivo registrado. El sistema principal lo confirmará.',
                'data' => [
                    'servicio_id' => $servicio->id,
                    'estado_pago' => 'pagado',
                    'metodo_pago' => 'efectivo',
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Formatear un servicio para la respuesta JSON.
     */
    protected function formatearServicio(Servicio $servicio, bool $conDetalle = false): array
    {
        $data = [
            'id' => $servicio->id,
            'token_seguimiento' => $servicio->token_seguimiento,
            'estado' => $servicio->estado,
            'estado_label' => $servicio->estado_label,
            'estado_pago' => $servicio->estado_pago,
            'estado_pago_label' => $servicio->estado_pago_label,
            'fecha_servicio' => $servicio->fecha_servicio?->format('Y-m-d'),
            'hora_inicio' => $servicio->hora_inicio ? Carbon::parse($servicio->hora_inicio)->format('H:i') : null,
            'hora_fin' => $servicio->hora_fin ? Carbon::parse($servicio->hora_fin)->format('H:i') : null,
            'origen' => $servicio->origen,
            'destino' => $servicio->destino,
            'costo_total' => (float) $servicio->costo_total,
            'metodo_pago' => $servicio->metodo_pago,
            'cliente' => $servicio->cliente ? [
                'id' => $servicio->cliente->id,
                'nombre_completo' => $servicio->cliente->nombre_completo,
                'telefono' => $servicio->cliente->telefono,
                'direccion' => $servicio->cliente->direccion,
                'latitud' => $servicio->cliente->latitud,
                'longitud' => $servicio->cliente->longitud,
            ] : null,
            'vehiculo' => $servicio->vehiculo ? [
                'id' => $servicio->vehiculo->id,
                'placa' => $servicio->vehiculo->placa,
                'marca' => $servicio->vehiculo->marca,
                'modelo' => $servicio->vehiculo->modelo,
                'tipo' => $servicio->vehiculo->tipo_label,
            ] : null,
        ];

        if ($conDetalle) {
            $data['cantidad_ayudantes'] = $servicio->cantidad_ayudantes;
            $data['numero_pisos'] = $servicio->numero_pisos;
            $data['es_callejon'] = (bool) $servicio->es_callejon;
            $data['distancia_km'] = $servicio->distancia_km ? (float) $servicio->distancia_km : null;
            $data['observaciones'] = $servicio->observaciones;
            $data['ayudantes'] = $servicio->ayudantes->map(fn($a) => [
                'id' => $a->id,
                'nombre_completo' => $a->nombre_completo,
                'telefono' => $a->telefono,
            ])->toArray();
            $data['bienes'] = $servicio->bienes->map(fn($b) => [
                'id' => $b->id,
                'nombre' => $b->nombre,
                'cantidad' => $b->cantidad,
                'descripcion' => $b->descripcion,
            ])->toArray();
        }

        return $data;
    }

    /**
     * Replicar el servicio a Firestore para que el APK y el sistema principal lo vean.
     */
    protected function replicarServicioAFirestore(Servicio $servicio): void
    {
        try {
            $this->firestore->set('servicios', (string) $servicio->id, [
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
                'cliente_nombre' => $servicio->cliente?->nombre_completo,
                'cliente_telefono' => $servicio->cliente?->telefono,
                'cliente_direccion' => $servicio->cliente?->direccion,
                'cliente_lat' => $servicio->cliente?->latitud,
                'cliente_lng' => $servicio->cliente?->longitud,
                'actualizado' => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error replicando servicio {$servicio->id} a Firestore: " . $e->getMessage());
        }
    }
}