<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Deuda;
use App\Models\Chofer;
use App\Models\Ayudante;
use App\Exports\PersonalExport;
use App\Exports\ServiciosExport;
use App\Exports\MorososExport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReporteController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', now()->format('Y-m-d'));

        $query = $this->buildQuery($request, $fechaInicio, $fechaFin);

        $servicios = (clone $query)
            ->with('cliente')
            ->orderByDesc('fecha_servicio')
            ->paginate(20);

        $totalRecaudado = (clone $query)->where('estado_pago', 'pagado')->sum('costo_total');
        $totalPendiente = (clone $query)->where('estado_pago', 'pendiente')->sum('costo_total');

        $clientesMorosos = Deuda::where('estado', 'pendiente')
            ->distinct('cliente_id')
            ->count('cliente_id');

        $clientes = Cliente::orderBy('nombre_completo')->get();

        $estadisticas = [
            'pendiente'   => (clone $query)->where('estado', 'pendiente')->count(),
            'confirmado'  => (clone $query)->where('estado', 'confirmado')->count(),
            'en_progreso' => (clone $query)->where('estado', 'en_progreso')->count(),
            'finalizado'  => (clone $query)->where('estado', 'finalizado')->count(),
            'cancelado'   => (clone $query)->where('estado', 'cancelado')->count(),
        ];

        $estadisticasPago = [
            'pendiente' => (clone $query)->where('estado_pago', 'pendiente')->count(),
            'pagado'    => (clone $query)->where('estado_pago', 'pagado')->count(),
        ];

        return view('reportes.index', compact(
            'servicios',
            'totalRecaudado',
            'totalPendiente',
            'clientesMorosos',
            'clientes',
            'estadisticas',
            'estadisticasPago',
            'fechaInicio',
            'fechaFin'
        ));
    }
    public function exportar(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', now()->format('Y-m-d'));

        $servicios = $this->buildQuery($request, $fechaInicio, $fechaFin)
            ->with('cliente')
            ->orderByDesc('fecha_servicio')
            ->get();

        $filename = 'servicios_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new ServiciosExport($servicios, $fechaInicio, $fechaFin),
            $filename
        );
    }

    public function morosos()
    {
        $morosos = Cliente::whereHas('deudas', fn($q) => $q->where('estado', 'pendiente'))
            ->with(['deudas' => fn($q) => $q->where('estado', 'pendiente')->orderBy('fecha_vencimiento')])
            ->orderBy('nombre_completo')
            ->get();

        return view('reportes.morosos', compact('morosos'));
    }

    public function exportarMorosos()
    {
        $morosos = Cliente::whereHas('deudas', fn($q) => $q->where('estado', 'pendiente'))
            ->with(['deudas' => fn($q) => $q->where('estado', 'pendiente')])
            ->orderBy('nombre_completo')
            ->get();

        $filename = 'morosos_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new MorososExport($morosos), $filename);
    }

    public function deudasCliente($clienteId)
    {
        try {
            $cliente = Cliente::findOrFail($clienteId);

            $deudas = Deuda::with('servicio')
                ->where('cliente_id', $clienteId)
                ->where('estado', 'pendiente')
                ->orderBy('fecha_vencimiento')
                ->get()
                ->map(function ($d) {
                    $dias = now()->diffInDays($d->fecha_vencimiento, false);
                    return [
                        'servicio_id'       => $d->servicio_id,
                        'monto'             => number_format($d->monto, 2),
                        'monto_raw'         => (float) $d->monto,
                        'fecha_vencimiento' => $d->fecha_vencimiento->format('d/m/Y'),
                        'vencida'           => $d->estaVencida(),
                        'dias_restantes'    => (int) $dias,
                    ];
                });

            return response()->json([
                'success'  => true,
                'cliente'  => [
                    'nombre_completo' => $cliente->nombre_completo,
                    'telefono'        => $cliente->telefono,
                ],
                'deudas'   => $deudas,
                'total'    => $deudas->sum('monto_raw'),
                'cantidad' => $deudas->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function personal(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', now()->format('Y-m-d'));
        $tipo        = $request->input('tipo', 'todos');

        $choferes  = collect();
        $ayudantes = collect();
        $resumen   = $this->resumenGeneral($fechaInicio, $fechaFin);

        if (in_array($tipo, ['todos', 'chofer'])) {
            $choferes = $this->serviciosPorChofer($fechaInicio, $fechaFin);
        }

        if (in_array($tipo, ['todos', 'ayudante'])) {
            $ayudantes = $this->serviciosPorAyudante($fechaInicio, $fechaFin);
        }

        $topChoferes  = $choferes->sortByDesc('total_ingresos')->take(5)->values();
        $topAyudantes = $ayudantes->sortByDesc('total_ingresos')->take(5)->values();

        return view('reportes.personal', compact(
            'choferes',
            'ayudantes',
            'resumen',
            'topChoferes',
            'topAyudantes',
            'fechaInicio',
            'fechaFin',
            'tipo'
        ));
    }

    public function exportarPersonal(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', now()->format('Y-m-d'));
        $tipo        = $request->input('tipo', 'todos');

        $choferes = in_array($tipo, ['todos', 'chofer'])
            ? $this->serviciosPorChofer($fechaInicio, $fechaFin)
            : collect();

        $ayudantes = in_array($tipo, ['todos', 'ayudante'])
            ? $this->serviciosPorAyudante($fechaInicio, $fechaFin)
            : collect();

        $resumen = $this->resumenGeneral($fechaInicio, $fechaFin);

        $filename = 'personal_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new PersonalExport($choferes, $ayudantes, $resumen, $tipo, $fechaInicio, $fechaFin),
            $filename
        );
    }

    protected function buildQuery(Request $request, string $fechaInicio, string $fechaFin)
    {
        $q = Servicio::query()
            ->whereDate('fecha_servicio', '>=', $fechaInicio)
            ->whereDate('fecha_servicio', '<=', $fechaFin);

        if ($request->filled('cliente_id')) {
            $q->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('estado')) {
            $q->where('estado', $request->estado);
        }
        if ($request->filled('estado_pago')) {
            $q->where('estado_pago', $request->estado_pago);
        }

        return $q;
    }

    protected function resumenGeneral(string $inicio, string $fin): array
    {
        $servicios = Servicio::whereDate('fecha_servicio', '>=', $inicio)
            ->whereDate('fecha_servicio', '<=', $fin)
            ->get();

        $totalServicios = $servicios->count();
        $totalIngresos  = $servicios->sum('costo_total');

        $totalHoras = $servicios->sum(function ($s) {
            return $this->calcularHoras($s->hora_inicio, $s->hora_fin);
        });

        return [
            'total_servicios'   => $totalServicios,
            'total_ingresos'    => (float) $totalIngresos,
            'total_horas'       => round($totalHoras, 1),
            'promedio_ingresos' => $totalServicios > 0 ? round($totalIngresos / $totalServicios, 2) : 0,
            'promedio_horas'    => $totalServicios > 0 ? round($totalHoras / $totalServicios, 1) : 0,
        ];
    }
    protected function calcularHoras($inicio, $fin): float
    {
        if (!$inicio || !$fin) return 0;

        try {
            $i = Carbon::parse($inicio);
            $f = Carbon::parse($fin);
            return max(0, $i->diffInMinutes($f) / 60);
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function serviciosPorChofer(string $inicio, string $fin)
    {
        return Chofer::query()
            ->whereHas('servicios', function ($q) use ($inicio, $fin) {
                $q->whereDate('fecha_servicio', '>=', $inicio)
                  ->whereDate('fecha_servicio', '<=', $fin);
            })
            ->with([
                'servicios' => function ($q) use ($inicio, $fin) {
                    $q->whereDate('fecha_servicio', '>=', $inicio)
                      ->whereDate('fecha_servicio', '<=', $fin)
                      ->with(['cliente', 'vehiculo', 'ayudantes'])
                      ->orderBy('fecha_servicio');
                },
            ])
            ->orderBy('nombre_completo')
            ->get()
            ->map(function ($chofer) {
                $servicios = $chofer->servicios;

                $totalHoras = $servicios->sum(function ($s) {
                    return $this->calcularHoras($s->hora_inicio, $s->hora_fin);
                });

                $totalIngresos = $servicios->sum('costo_total');

                $vehiculos = $servicios->pluck('vehiculo.placa')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $ayudantesNombres = $servicios
                    ->flatMap(fn($s) => $s->ayudantes->pluck('nombre_completo'))
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                return [
                    'id'              => $chofer->id,
                    'nombre'          => $chofer->nombre_completo ?? '—',
                    'telefono'        => $chofer->telefono ?? '—',
                    'total_servicios' => $servicios->count(),
                    'total_horas'     => round($totalHoras, 1),
                    'total_ingresos'  => (float) $totalIngresos,
                    'promedio_horas'  => $servicios->count() > 0 ? round($totalHoras / $servicios->count(), 1) : 0,
                    'vehiculos'       => $vehiculos,
                    'ayudantes'       => $ayudantesNombres,
                    'servicios'       => $servicios->map(fn($s) => $this->mapServicio($s))->toArray(),
                ];
            });
    }

    protected function serviciosPorAyudante(string $inicio, string $fin)
    {
        return Ayudante::query()
            ->whereHas('servicios', function ($q) use ($inicio, $fin) {
                $q->whereDate('fecha_servicio', '>=', $inicio)
                  ->whereDate('fecha_servicio', '<=', $fin);
            })
            ->with([
                'servicios' => function ($q) use ($inicio, $fin) {
                    $q->whereDate('fecha_servicio', '>=', $inicio)
                      ->whereDate('fecha_servicio', '<=', $fin)
                      ->with(['cliente', 'chofer', 'vehiculo'])
                      ->orderBy('fecha_servicio');
                },
            ])
            ->orderBy('nombre_completo')
            ->get()
            ->map(function ($ayudante) {
                $servicios = $ayudante->servicios;

                $totalHoras = $servicios->sum(function ($s) {
                    return $this->calcularHoras($s->hora_inicio, $s->hora_fin);
                });

                $totalIngresos = $servicios->sum('costo_total');

                $choferesNombres = $servicios
                    ->pluck('chofer.nombre_completo')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $vehiculos = $servicios->pluck('vehiculo.placa')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                return [
                    'id'              => $ayudante->id,
                    'nombre'          => $ayudante->nombre_completo ?? '—',
                    'telefono'        => $ayudante->telefono ?? '—',
                    'total_servicios' => $servicios->count(),
                    'total_horas'     => round($totalHoras, 1),
                    'total_ingresos'  => (float) $totalIngresos,
                    'promedio_horas'  => $servicios->count() > 0 ? round($totalHoras / $servicios->count(), 1) : 0,
                    'choferes'        => $choferesNombres,
                    'vehiculos'       => $vehiculos,
                    'servicios'       => $servicios->map(fn($s) => $this->mapServicio($s))->toArray(),
                ];
            });
    }

    protected function mapServicio(Servicio $s): array
    {
        $horaInicio = $s->hora_inicio ? Carbon::parse($s->hora_inicio)->format('H:i') : '—';
        $horaFin    = $s->hora_fin    ? Carbon::parse($s->hora_fin)->format('H:i')    : '—';
        $horas      = $this->calcularHoras($s->hora_inicio, $s->hora_fin);

        return [
            'id'          => $s->id,
            'cliente'     => $s->cliente->nombre_completo ?? '—',
            'origen'      => $s->origen,
            'destino'     => $s->destino,
            'fecha'       => optional($s->fecha_servicio)->format('d/m/Y') ?? '—',
            'hora_inicio' => $horaInicio,
            'hora_fin'    => $horaFin,
            'horas'       => round($horas, 1),
            'costo'       => (float) $s->costo_total,
            'vehiculo'    => $s->vehiculo->placa ?? '—',
            'chofer'      => $s->chofer->nombre_completo ?? '—',
            'ayudantes'   => $s->ayudantes->pluck('nombre_completo')->toArray(),
            'estado'      => $s->estado_label ?? $s->estado,
        ];
    }
}