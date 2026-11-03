<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Chofer;
use App\Models\Ayudante;
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
        $user = auth()->user();
        $rol  = $user->role;

        $esAdmin         = $user->isAdmin();
        $esRecepcionista = $user->isRecepcionista();
        $esChofer        = $user->isChofer();
        $esAyudante      = $user->isAyudante();

        $ahora     = Carbon::now();
        $inicioMes = $ahora->copy()->startOfMonth();
        $finMes    = $ahora->copy()->endOfMonth();
        $hoy       = $ahora->copy()->format('Y-m-d');

        $queryBase = Servicio::query();

        if ($esChofer && $user->chofer) {
            $queryBase->where('chofer_id', $user->chofer->id);
        }

        if ($esAyudante && $user->ayudante) {
            $queryBase->whereHas('ayudantes', function ($q) use ($user) {
                $q->where('ayudante_id', $user->ayudante->id);
            });
        }


        $serviciosPorEstado = [
            'pendiente'   => (clone $queryBase)->where('estado', 'pendiente')->count(),
            'confirmado'  => (clone $queryBase)->where('estado', 'confirmado')->count(),
            'en_progreso' => (clone $queryBase)->where('estado', 'en_progreso')->count(),
            'finalizado'  => (clone $queryBase)->where('estado', 'finalizado')->count(),
            'cancelado'   => (clone $queryBase)->where('estado', 'cancelado')->count(),
        ];

        $totalServicios = array_sum($serviciosPorEstado);

        $serviciosEnProgreso = $serviciosPorEstado['en_progreso'];
        $serviciosEnCamino   = (clone $queryBase)
            ->whereDate('fecha_servicio', $hoy)
            ->where('estado', 'confirmado')
            ->count();

        $serviciosFinalizadosHoy = (clone $queryBase)
            ->where('estado', 'finalizado')
            ->whereDate('fecha_servicio', $hoy)
            ->count();

        
        $serviciosMes = (clone $queryBase)
            ->whereMonth('fecha_servicio', $ahora->month)
            ->whereYear('fecha_servicio', $ahora->year)
            ->count();

        $totalServiciosGlobal = 0;
        $totalIngresos = 0;
        $clientesActivos = 0;
        $pendientesPago = 0;
        $ingresosMes = 0;
        $montoDeudas = 0;
        $deudasPendientes = 0;
        $serviciosPagadosMes = 0;
        $ingresosPorMes = collect();
        $topClientes = collect();
        $rankingChoferes = collect();

        if ($esAdmin) {
            $totalServiciosGlobal = Servicio::count();
            $totalIngresos = Servicio::where('estado_pago', 'pagado')->sum('costo_total');
            $clientesActivos = Cliente::where('bloqueado', false)->count();
            $pendientesPago = Servicio::where('estado_pago', 'pendiente')
                ->whereIn('estado', ['finalizado', 'en_progreso'])
                ->count();

            $deudasPendientes = Deuda::where('estado', 'pendiente')->count();
            $montoDeudas = Deuda::where('estado', 'pendiente')->sum('monto');

            $serviciosPagadosMes = Servicio::where('estado_pago', 'pagado')
                ->whereBetween('updated_at', [$inicioMes, $finMes])
                ->count();

            $ingresosMes = Servicio::where('estado_pago', 'pagado')
                ->whereBetween('updated_at', [$inicioMes, $finMes])
                ->sum('costo_total');


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

            $topClientes = Cliente::withCount('servicios')
                ->orderBy('servicios_count', 'desc')
                ->limit(5)
                ->get();

            $rankingChoferes = Chofer::withCount(['servicios' => function ($q) {
                    $q->where('estado', 'finalizado');
                }])
                ->orderBy('servicios_count', 'desc')
                ->limit(5)
                ->get();
        }

        $clientesRegistrados = 0;
        if ($esRecepcionista) {
            $totalServiciosGlobal = Servicio::count();
            $clientesRegistrados = Cliente::count();
        }

        $serviciosEnCurso = (clone $queryBase)
            ->with(['cliente', 'chofer', 'vehiculo'])
            ->where('estado', 'en_progreso')
            ->orderByDesc('fecha_servicio')
            ->limit(5)
            ->get();

        
        $proximosServicios = (clone $queryBase)
            ->with(['cliente', 'chofer', 'vehiculo'])
            ->whereIn('estado', ['pendiente', 'confirmado'])
            ->whereDate('fecha_servicio', '>=', $hoy)
            ->whereDate('fecha_servicio', '<=', $ahora->copy()->addDay()->format('Y-m-d'))
            ->orderBy('fecha_servicio')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        
        $serviciosRecientes = collect();
        if ($esAdmin || $esRecepcionista) {
            $serviciosRecientes = Servicio::with(['cliente', 'chofer'])
                ->orderByDesc('id')
                ->limit(5)
                ->get();
        }

        
        $serviciosPorPago = [
            'pendiente' => 0,
            'pagado'    => 0,
        ];
        if ($esAdmin) {
            $serviciosPorPago = [
                'pendiente' => Servicio::where('estado_pago', 'pendiente')
                    ->whereIn('estado', ['finalizado', 'en_progreso', 'confirmado'])
                    ->count(),
                'pagado'    => Servicio::where('estado_pago', 'pagado')->count(),
            ];
        }

        return view('dashboard.index', compact(
            
            'serviciosPorEstado',
            'totalServicios',
            'serviciosEnProgreso',
            'serviciosEnCamino',
            'serviciosFinalizadosHoy',
            'serviciosMes',
            'serviciosEnCurso',
            'proximosServicios',
            'totalServiciosGlobal',

            
            'totalIngresos',
            'clientesActivos',
            'pendientesPago',
            'ingresosMes',
            'serviciosPagadosMes',
            'deudasPendientes',
            'montoDeudas',
            'ingresosPorMes',
            'topClientes',
            'rankingChoferes',
            'serviciosPorPago',
            'serviciosRecientes',

            
            'clientesRegistrados',

            
            'rol',
            'esAdmin',
            'esRecepcionista',
            'esChofer',
            'esAyudante'
        ));
    }
}