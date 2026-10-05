<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Chofer;
use App\Models\Deuda;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // ============================================
        // FECHAS DE REFERENCIA
        // ============================================
        $ahora = Carbon::now();
        $inicioMes = $ahora->copy()->startOfMonth();
        $finMes = $ahora->copy()->endOfMonth();
        $hoy = $ahora->copy()->format('Y-m-d');

        // ============================================
        // TARJETAS PRINCIPALES
        // ============================================
        $totalServicios = Servicio::count();
        
        $totalIngresos = Servicio::where('estado_pago', 'pagado')
            ->sum('costo_total');
        
        $clientesActivos = Cliente::where('bloqueado', false)->count();
        
        $pendientesPago = Servicio::where('estado_pago', 'pendiente')
            ->whereIn('estado', ['finalizado', 'en_progreso'])
            ->count();

        // ============================================
        // TARJETAS SECUNDARIAS
        // ============================================
        // Servicios EN PROGRESO ahora mismo
        $serviciosEnProgreso = Servicio::where('estado', 'en_progreso')->count();
        
        // Servicios FINALIZADOS hoy
        $serviciosFinalizadosHoy = Servicio::where('estado', 'finalizado')
            ->whereDate('fecha_servicio', $hoy)
            ->count();
        
        // Servicios PAGADOS este mes
        $serviciosPagadosMes = Servicio::where('estado_pago', 'pagado')
            ->whereBetween('updated_at', [$inicioMes, $finMes])
            ->count();
        
        // Ingresos del mes
        $ingresosMes = Servicio::where('estado_pago', 'pagado')
            ->whereBetween('updated_at', [$inicioMes, $finMes])
            ->sum('costo_total');

        // Servicios que empiezan HOY y aún no están en progreso
        $serviciosEnCamino = Servicio::whereDate('fecha_servicio', $hoy)
            ->whereIn('estado', ['confirmado'])
            ->count();

        // ============================================
        // GRÁFICA 1: SERVICIOS POR ESTADO OPERATIVO
        // ============================================
        $serviciosPorEstado = [
            'pendiente' => Servicio::where('estado', 'pendiente')->count(),
            'confirmado' => Servicio::where('estado', 'confirmado')->count(),
            'en_progreso' => Servicio::where('estado', 'en_progreso')->count(),
            'finalizado' => Servicio::where('estado', 'finalizado')->count(),
            'cancelado' => Servicio::where('estado', 'cancelado')->count(),
        ];

        // ============================================
        // GRÁFICA 2: SERVICIOS POR ESTADO DE PAGO
        // ============================================
        $serviciosPorPago = [
            'pendiente' => Servicio::where('estado_pago', 'pendiente')
                ->whereIn('estado', ['finalizado', 'en_progreso', 'confirmado'])
                ->count(),
            'pagado' => Servicio::where('estado_pago', 'pagado')->count(),
        ];

        // ============================================
        // GRÁFICA 3: INGRESOS POR MES (últimos 6 meses)
        // ============================================
        $ingresosPorMes = Servicio::where('estado_pago', 'pagado')
            ->where('updated_at', '>=', $ahora->copy()->subMonths(6))
            ->select(
                DB::raw("TO_CHAR(updated_at, 'YYYY-MM') as mes"),
                DB::raw('SUM(costo_total) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // ============================================
        // TABLA 1: SERVICIOS EN CURSO (EN PROGRESO)
        // ============================================
        $serviciosEnCurso = Servicio::with(['cliente', 'chofer', 'vehiculo'])
            ->where('estado', 'en_progreso')
            ->orderBy('fecha_servicio', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // TABLA 2: PRÓXIMOS SERVICIOS (hoy y mañana)
        // ============================================
        $proximosServicios = Servicio::with(['cliente', 'chofer', 'vehiculo'])
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->whereDate('fecha_servicio', '>=', $hoy)
            ->whereDate('fecha_servicio', '<=', $ahora->copy()->addDay()->format('Y-m-d'))
            ->orderBy('fecha_servicio', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->limit(5)
            ->get();

        // ============================================
        // TABLA 3: TOP 5 CLIENTES (más servicios)
        // ============================================
        $topClientes = Cliente::withCount('servicios')
            ->orderBy('servicios_count', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // TABLA 4: RANKING DE CHOFERES
        // ============================================
        $rankingChoferes = Chofer::withCount(['servicios' => function($q) {
                $q->where('estado', 'finalizado');
            }])
            ->orderBy('servicios_count', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // SERVICIOS RECIENTES (los últimos creados)
        // ============================================
        $serviciosRecientes = Servicio::with(['cliente', 'chofer'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // DEUDAS PENDIENTES
        // ============================================
        $deudasPendientes = Deuda::where('estado', 'pendiente')->count();
        $montoDeudas = Deuda::where('estado', 'pendiente')->sum('monto');

        // ============================================
        // RETORNAR VISTA
        // ============================================
        return view('dashboard.index', compact(
            // Tarjetas principales
            'totalServicios',
            'totalIngresos',
            'clientesActivos',
            'pendientesPago',
            
            // Tarjetas secundarias
            'serviciosEnProgreso',
            'serviciosFinalizadosHoy',
            'serviciosPagadosMes',
            'ingresosMes',
            'serviciosEnCamino',
            
            // Gráficas
            'serviciosPorEstado',
            'serviciosPorPago',
            'ingresosPorMes',
            
            // Tablas
            'serviciosEnCurso',
            'proximosServicios',
            'topClientes',
            'rankingChoferes',
            'serviciosRecientes',
            
            // Deudas
            'deudasPendientes',
            'montoDeudas'
        ));
    }
}