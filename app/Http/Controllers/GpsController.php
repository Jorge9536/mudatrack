<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\UbicacionGps;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class GpsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Lista de servicios con seguimiento GPS
     */
    public function index(Request $request)
    {
        // 🔥 CONSTRUIR LA CONSULTA CON FILTROS
        $query = Servicio::with(['cliente', 'chofer', 'vehiculo'])
            ->whereIn('estado', ['confirmado', 'en_progreso', 'pendiente']);

        // FILTRO POR BÚSQUEDA
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('cliente', function($q2) use ($search) {
                    $q2->where('nombre_completo', 'LIKE', "%{$search}%");
                })->orWhere('origen', 'LIKE', "%{$search}%")
                  ->orWhere('destino', 'LIKE', "%{$search}%");
            });
        }

        // FILTRO POR ESTADO
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // FILTRO POR FECHA
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_servicio', $request->fecha);
        }

        $servicios = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // 🔥 IMPORTANTE: PASAR LA VARIABLE $servicio PARA EL MAPA
        $servicio = $servicios->first();

        return view('gps.index', compact('servicios', 'servicio'));
    }

    /**
     * Muestra el seguimiento GPS de un servicio
     */
    public function seguimiento($id)  // ← Cambiar a $id en lugar de Servicio $servicio
    {
        $servicio = Servicio::with(['cliente', 'chofer', 'vehiculo'])->findOrFail($id);
        
        // Verificar que el servicio esté activo
        if (!in_array($servicio->estado, ['confirmado', 'en_progreso', 'pendiente'])) {
            abort(404, 'Servicio no disponible para seguimiento');
        }

        // Obtener ubicaciones GPS del servicio
        $ubicaciones = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // Última ubicación
        $ultimaUbicacion = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'desc')
            ->first();

        return view('gps.seguimiento', compact('servicio', 'ubicaciones', 'ultimaUbicacion'));
    }

    /**
     * Actualiza la ubicación GPS
     */
    public function actualizar(Request $request)
    {
        $validated = $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'velocidad' => 'nullable|numeric|min:0'
        ]);

        $ubicacion = UbicacionGps::create([
            'servicio_id' => $validated['servicio_id'],
            'latitud' => $validated['latitud'],
            'longitud' => $validated['longitud'],
            'velocidad' => $validated['velocidad'] ?? 0,
            'fecha_hora' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ubicación actualizada',
            'data' => $ubicacion
        ]);
    }

    /**
     * Obtiene la última ubicación de un servicio
     */
    public function ultimaUbicacion($id)  // ← Cambiar a $id
    {
        $servicio = Servicio::findOrFail($id);
        $ubicacion = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $ubicacion
        ]);
    }

    /**
     * Obtiene el historial de ubicaciones de un servicio
     */
    public function historial($id)  // ← Cambiar a $id
    {
        $servicio = Servicio::findOrFail($id);
        $ubicaciones = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $ubicaciones
        ]);
    }
}