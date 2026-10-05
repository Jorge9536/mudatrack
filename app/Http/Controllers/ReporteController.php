<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Deuda;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class ReporteController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el panel de reportes
     */
    public function index(Request $request)
    {
        // ============================================
        // FECHAS POR DEFECTO
        // ============================================
        $fechaInicio = $request->filled('fecha_inicio') 
            ? $request->fecha_inicio 
            : now()->startOfMonth()->format('Y-m-d');
        
        $fechaFin = $request->filled('fecha_fin') 
            ? $request->fecha_fin 
            : now()->format('Y-m-d');

        // ============================================
        // QUERY BASE
        // ============================================
        $query = Servicio::with(['cliente', 'chofer', 'vehiculo'])
            ->whereDate('fecha_servicio', '>=', $fechaInicio)
            ->whereDate('fecha_servicio', '<=', $fechaFin);

        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro por estado operativo
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por estado de pago
        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        $servicios = $query->orderBy('id', 'desc')->paginate(20);

        // ============================================
        // TOTALES (usando estado_pago)
        // ============================================
        $totalRecaudado = (clone $query)
            ->where('estado_pago', 'pagado')
            ->sum('costo_total');

        $totalPendiente = (clone $query)
            ->where('estado_pago', 'pendiente')
            ->whereIn('estado', ['finalizado', 'en_progreso'])
            ->sum('costo_total');

        $clientesMorosos = Deuda::where('estado', 'pendiente')
            ->distinct('cliente_id')
            ->count('cliente_id');

        $clientes = Cliente::orderBy('nombre_completo')->get();

        // ============================================
        // ESTADÍSTICAS POR ESTADO OPERATIVO
        // ============================================
        $estadisticas = [
            'pendiente' => (clone $query)->where('estado', 'pendiente')->count(),
            'confirmado' => (clone $query)->where('estado', 'confirmado')->count(),
            'en_progreso' => (clone $query)->where('estado', 'en_progreso')->count(),
            'finalizado' => (clone $query)->where('estado', 'finalizado')->count(),
            'cancelado' => (clone $query)->where('estado', 'cancelado')->count(),
        ];

        // ============================================
        // ESTADÍSTICAS POR ESTADO DE PAGO
        // ============================================
        $estadisticasPago = [
            'pendiente' => (clone $query)->where('estado_pago', 'pendiente')->count(),
            'pagado' => (clone $query)->where('estado_pago', 'pagado')->count(),
        ];

        // ============================================
        // SERVICIOS POR DÍA (últimos 7 días)
        // ============================================
        $dias = collect();
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $dias->put($fecha, 0);
        }
        
        $serviciosPorDia = Servicio::whereBetween('created_at', [
                now()->subDays(7)->startOfDay(),
                now()->endOfDay()
            ])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');
        
        $dias = $dias->merge($serviciosPorDia);

        return view('reportes.index', compact(
            'servicios',
            'totalRecaudado',
            'totalPendiente',
            'clientesMorosos',
            'clientes',
            'estadisticas',
            'estadisticasPago',
            'dias',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Exportar reporte en Excel (simulado)
     */
    public function exportar(Request $request)
    {
        return redirect()->route('reportes.index')
            ->with('info', 'Función de exportación en desarrollo');
    }

    /**
     * 🔥 Genera reporte de clientes morosos (CON DATOS REALES)
     */
    public function morosos()
    {
        $morosos = Cliente::whereHas('deudas', function($q) {
                $q->where('estado', 'pendiente');
            })
            ->with(['deudas' => function($q) {
                $q->where('estado', 'pendiente')
                  ->with('servicio')
                  ->orderBy('fecha_vencimiento', 'asc');
            }])
            ->orderBy('nombre_completo')
            ->get();

        return view('reportes.morosos', compact('morosos'));
    }

    /**
     * 🔥 API: Obtener deudas de un cliente específico (para AJAX)
     */
    public function deudasCliente($clienteId)
    {
        try {
            $cliente = Cliente::findOrFail($clienteId);
            
            $deudas = Deuda::with('servicio')
                ->where('cliente_id', $clienteId)
                ->where('estado', 'pendiente')
                ->orderBy('fecha_vencimiento', 'asc')
                ->get()
                ->map(function($deuda) {
                    $diasRestantes = now()->diffInDays($deuda->fecha_vencimiento, false);
                    
                    return [
                        'id' => $deuda->id,
                        'servicio_id' => $deuda->servicio_id,
                        'monto' => number_format($deuda->monto, 2),
                        'monto_raw' => (float) $deuda->monto,
                        'fecha_vencimiento' => $deuda->fecha_vencimiento->format('d/m/Y'),
                        'vencida' => $deuda->estaVencida(),
                        'estado' => $deuda->estado,
                        'observaciones' => $deuda->observaciones,
                        'dias_restantes' => (int) $diasRestantes,
                    ];
                });

            return response()->json([
                'success' => true,
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre_completo' => $cliente->nombre_completo,
                    'telefono' => $cliente->telefono,
                ],
                'deudas' => $deudas,
                'total' => $deudas->sum('monto_raw'),
                'cantidad' => $deudas->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar deudas: ' . $e->getMessage()
            ], 500);
        }
    }
}