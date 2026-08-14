<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Totales
        $totalServicios = Servicio::count();
        $totalIngresos = Servicio::where('estado', 'pagado')->sum('costo_total');
        $clientesActivos = Cliente::where('bloqueado', false)->count();
        $pendientesPago = Servicio::where('estado', 'pendiente_pago')->count();

        // Servicios por estado
        $serviciosPorEstado = [
            'pendiente' => Servicio::where('estado', 'pendiente')->count(),
            'confirmado' => Servicio::where('estado', 'confirmado')->count(),
            'en_progreso' => Servicio::where('estado', 'en_progreso')->count(),
            'finalizado' => Servicio::where('estado', 'finalizado')->count(),
            'pendiente_pago' => Servicio::where('estado', 'pendiente_pago')->count(),
            'pagado' => Servicio::where('estado', 'pagado')->count(),
            'cancelado' => Servicio::where('estado', 'cancelado')->count(),
        ];

        // Ingresos por día (últimos 7 días)
        $ingresosPorDia = Servicio::where('estado', 'pagado')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(costo_total) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Servicios recientes
        $serviciosRecientes = Servicio::with(['cliente', 'chofer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Pasar todas las variables a la vista
        return view('dashboard.index', compact(
            'totalServicios',
            'totalIngresos',
            'clientesActivos',
            'pendientesPago',
            'serviciosPorEstado',
            'ingresosPorDia',
            'serviciosRecientes'
        ));
    }
}