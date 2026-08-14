<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Deuda;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

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
        $query = Servicio::with(['cliente', 'chofer', 'vehiculo']);

        // Filtros de fecha
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $servicios = $query->orderBy('created_at', 'desc')->paginate(20);

        // Totales
        $totalRecaudado = Servicio::where('estado', 'pagado')
            ->when($request->filled('fecha_inicio'), function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->fecha_inicio);
            })
            ->when($request->filled('fecha_fin'), function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->fecha_fin);
            })
            ->sum('costo_total');

        $totalPendiente = Servicio::where('estado', 'pendiente_pago')
            ->when($request->filled('fecha_inicio'), function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->fecha_inicio);
            })
            ->when($request->filled('fecha_fin'), function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->fecha_fin);
            })
            ->sum('costo_total');

        $clientesMorosos = Deuda::where('estado', 'pendiente')
            ->distinct('cliente_id')
            ->count('cliente_id');

        $clientes = Cliente::orderBy('nombre_completo')->get();

        // Estadísticas por estado
        $estadisticas = [
            'pendiente' => Servicio::where('estado', 'pendiente')->count(),
            'confirmado' => Servicio::where('estado', 'confirmado')->count(),
            'en_progreso' => Servicio::where('estado', 'en_progreso')->count(),
            'finalizado' => Servicio::where('estado', 'finalizado')->count(),
            'pendiente_pago' => Servicio::where('estado', 'pendiente_pago')->count(),
            'pagado' => Servicio::where('estado', 'pagado')->count(),
            'cancelado' => Servicio::where('estado', 'cancelado')->count(),
        ];

        return view('reportes.index', compact(
            'servicios',
            'totalRecaudado',
            'totalPendiente',
            'clientesMorosos',
            'clientes',
            'estadisticas'
        ));
    }

    /**
     * Exportar reporte en Excel (simulado)
     */
    public function exportar(Request $request)
    {
        return redirect()->route('reportes.index')
            ->with('success', 'Reporte exportado exitosamente (simulación)');
    }

    /**
     * Genera reporte de clientes morosos
     */
    public function morosos()
    {
        $morosos = Cliente::whereHas('deudas', function($q) {
            $q->where('estado', 'pendiente');
        })->with(['deudas' => function($q) {
            $q->where('estado', 'pendiente');
        }])->get();

        return view('reportes.morosos', compact('morosos'));
    }
}