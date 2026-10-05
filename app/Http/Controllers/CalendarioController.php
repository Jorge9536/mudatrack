<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Models\Chofer;
use App\Models\Ayudante;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $choferes = Chofer::where('disponible', true)->get();
        $ayudantes = Ayudante::where('disponible', true)->get();

        return view('calendario.index', compact('vehiculos', 'choferes', 'ayudantes'));
    }

    public function eventos(Request $request)
    {
        $query = Servicio::with(['cliente', 'vehiculo', 'chofer', 'ayudantes']);

        if ($request->filled('vehiculo')) {
            $query->where('vehiculo_id', $request->vehiculo);
        }
        if ($request->filled('chofer')) {
            $query->where('chofer_id', $request->chofer);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('fecha_servicio', [
                Carbon::parse($request->start)->startOfDay(),
                Carbon::parse($request->end)->endOfDay()
            ]);
        }

        $servicios = $query->get();

        $eventos = [];
        foreach ($servicios as $servicio) {
            $start = $servicio->fecha_servicio->format('Y-m-d') . 'T' . 
                     ($servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i:s') : '00:00:00');
            $end = $servicio->fecha_servicio->format('Y-m-d') . 'T' . 
                   ($servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i:s') : '23:59:59');

            $ayudantesNombres = $servicio->ayudantes->pluck('nombre_completo')->toArray();

            $eventos[] = [
                'id' => $servicio->id,
                'title' => "#{$servicio->id} - {$servicio->cliente->nombre_completo}",
                'start' => $start,
                'end' => $end,
                'color' => $this->getEstadoColor($servicio->estado),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'servicio_id' => $servicio->id,
                    'cliente' => $servicio->cliente->nombre_completo,
                    'cliente_telefono' => $servicio->cliente->telefono,
                    'cliente_direccion' => $servicio->cliente->direccion ?? 'N/A',
                    'vehiculo_id' => $servicio->vehiculo_id,
                    'vehiculo' => $servicio->vehiculo ? $servicio->vehiculo->placa . ' - ' . $servicio->vehiculo->marca : 'Sin asignar',
                    'vehiculo_tipo' => $servicio->vehiculo ? $servicio->vehiculo->tipo_label : 'N/A',
                    'chofer_id' => $servicio->chofer_id,
                    'chofer' => $servicio->chofer ? $servicio->chofer->nombre_completo : 'Sin asignar',
                    'chofer_telefono' => $servicio->chofer ? $servicio->chofer->telefono : 'N/A',
                    'ayudantes' => $ayudantesNombres,
                    'ayudantes_count' => count($ayudantesNombres),
                    'origen' => $servicio->origen,
                    'destino' => $servicio->destino,
                    'estado' => $servicio->estado,
                    'estado_label' => $servicio->estado_label,
                    'costo' => $servicio->costo_total,
                    'fecha' => $servicio->fecha_servicio->format('d/m/Y'),
                    'hora_inicio' => $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : 'N/A',
                    'hora_fin' => $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : 'N/A',
                    'cantidad_ayudantes' => $servicio->cantidad_ayudantes,
                    'numero_pisos' => $servicio->numero_pisos,
                    'es_callejon' => $servicio->es_callejon,
                    'observaciones' => $servicio->observaciones,
                    'url' => route('servicios.show', $servicio->id),
                    'url_asignar' => route('servicios.asignar.form', $servicio->id),
                ]
            ];
        }

        return response()->json($eventos);
    }

    public function recursosDisponibles(Request $request)
    {
        try {
            $request->validate([
                'fecha' => 'required|date',
                'hora_inicio' => 'required',
                'hora_fin' => 'required'
            ]);

            $fecha = $request->fecha;
            $horaInicio = $request->hora_inicio;
            $horaFin = $request->hora_fin;

            $vehiculosDisponibles = Vehiculo::where('disponible', true)
                ->get()
                ->filter(function($vehiculo) use ($fecha, $horaInicio, $horaFin) {
                    return $vehiculo->isAvailable($fecha, $horaInicio, $horaFin);
                })
                ->values();

            $choferesDisponibles = Chofer::where('disponible', true)
                ->get()
                ->filter(function($chofer) use ($fecha, $horaInicio, $horaFin) {
                    return $chofer->isAvailable($fecha, $horaInicio, $horaFin);
                })
                ->values();

            $ayudantesDisponibles = Ayudante::where('disponible', true)
                ->get()
                ->filter(function($ayudante) use ($fecha, $horaInicio, $horaFin) {
                    return $ayudante->isAvailable($fecha, $horaInicio, $horaFin);
                })
                ->values();

            return response()->json([
                'vehiculos' => $vehiculosDisponibles,
                'choferes' => $choferesDisponibles,
                'ayudantes' => $ayudantesDisponibles
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function getEstadoColor($estado)
    {
        $colores = [
            'pendiente' => '#FFA500',
            'confirmado' => '#17A2B8',
            'en_progreso' => '#007BFF',
            'finalizado' => '#28A745',
            'cancelado' => '#DC3545',
            'pendiente_pago' => '#FFC107',
            'pagado' => '#28A745',
        ];
        return $colores[$estado] ?? '#6C757D';
    }
}