<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\UbicacionGps;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;

class GpsController extends BaseController
{
    protected $firebaseClient;
    protected $firebaseUrl;

    public function __construct()
    {
        $this->middleware('auth');
        
        // Inicializar cliente para Firebase
        $this->firebaseClient = new Client();
        $this->firebaseUrl = "https://firestore.googleapis.com/v1/projects/gps1-e12e5/databases/(default)/documents";
    }

    /**
     * Lista de servicios con seguimiento GPS
     */
    public function index(Request $request)
    {
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
        
        // Obtener dispositivos activos desde Firebase
        $dispositivosFirebase = $this->getTodasUbicaciones();

        return view('gps.index', compact('servicios', 'dispositivosFirebase'));
    }

    /**
     * Muestra el seguimiento GPS de un servicio con datos de Firebase
     */
    public function seguimiento($id)
    {
        $servicio = Servicio::with(['cliente', 'chofer', 'vehiculo'])->findOrFail($id);
        
        // Verificar que el servicio esté activo
        if (!in_array($servicio->estado, ['confirmado', 'en_progreso', 'pendiente'])) {
            abort(404, 'Servicio no disponible para seguimiento');
        }

        // Obtener ubicaciones GPS locales
        $ubicaciones = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        // Última ubicación local
        $ultimaUbicacion = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'desc')
            ->first();

        // Obtener ubicación del chofer desde Firebase
        $ubicacionFirebase = null;
        $dispositivosFirebase = [];

        if ($servicio->chofer) {
            // Buscar el dispositivo del chofer en Firebase
            $ubicacionFirebase = $this->getUbicacionDispositivo($servicio->chofer->id);
            
            // Obtener todos los dispositivos activos
            $dispositivosFirebase = $this->getTodasUbicaciones();
        }

        return view('gps.seguimiento', compact(
            'servicio', 
            'ubicaciones', 
            'ultimaUbicacion',
            'ubicacionFirebase',
            'dispositivosFirebase'
        ));
    }

    /**
     * Actualiza la ubicación GPS (desde app móvil o web)
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
    public function ultimaUbicacion($id)
    {
        $servicio = Servicio::findOrFail($id);
        
        // Buscar en BD local
        $ubicacionLocal = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'desc')
            ->first();

        // Buscar en Firebase
        $ubicacionFirebase = null;
        if ($servicio->chofer) {
            $ubicacionFirebase = $this->getUbicacionDispositivo($servicio->chofer->id);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'local' => $ubicacionLocal,
                'firebase' => $ubicacionFirebase
            ]
        ]);
    }

    /**
     * Obtiene el historial de ubicaciones de un servicio
     */
    public function historial($id)
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

    // ============================================
    // MÉTODOS PARA FIREBASE
    // ============================================

    /**
     * Obtener ubicación de un dispositivo específico desde Firebase
     */
    public function getUbicacionDispositivo($dispositivoId)
    {
        try {
            $url = $this->firebaseUrl . '/ubicaciones';
            $response = $this->firebaseClient->get($url);
            $data = json_decode($response->getBody(), true);

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    $id = $this->getFieldValue($fields, 'dispositivoId', 'string');
                    
                    // Buscar por ID de chofer o dispositivo
                    if ($id == $dispositivoId || strpos($id, (string)$dispositivoId) !== false) {
                        return [
                            'lat' => (float) $this->getFieldValue($fields, 'latitud', 'double'),
                            'lng' => (float) $this->getFieldValue($fields, 'longitud', 'double'),
                            'actualizado' => $this->getFieldValue($fields, 'actualizado', 'string'),
                            'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string'),
                            'modelo' => $this->getFieldValue($fields, 'modelo', 'string'),
                            'dispositivoId' => $id,
                        ];
                    }
                }
            }
            return null;

        } catch (\Exception $e) {
            \Log::error('Error al obtener ubicación de Firebase: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener todas las ubicaciones desde Firebase
     */
    public function getTodasUbicaciones()
    {
        try {
            $url = $this->firebaseUrl . '/ubicaciones';
            $response = $this->firebaseClient->get($url);
            $data = json_decode($response->getBody(), true);
            
            $ubicaciones = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    
                    $lat = (float) $this->getFieldValue($fields, 'latitud', 'double');
                    $lng = (float) $this->getFieldValue($fields, 'longitud', 'double');
                    
                    // Solo incluir ubicaciones válidas
                    if ($lat != 0 && $lng != 0) {
                        $ubicaciones[] = [
                            'id' => basename($document['name']),
                            'dispositivoId' => $this->getFieldValue($fields, 'dispositivoId', 'string'),
                            'lat' => $lat,
                            'lng' => $lng,
                            'nombre' => $this->getFieldValue($fields, 'modelo', 'string') ?? 'Dispositivo',
                            'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string') ?? 'desconocida',
                            'actualizado' => $this->getFieldValue($fields, 'actualizado', 'string'),
                            'timestamp' => (int) $this->getFieldValue($fields, 'timestamp', 'integer'),
                        ];
                    }
                }
            }

            return $ubicaciones;

        } catch (\Exception $e) {
            \Log::error('Error al obtener todas las ubicaciones: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * API para obtener ubicaciones de Firebase (para el mapa en tiempo real)
     */
    public function getFirebaseUbicaciones()
    {
        $ubicaciones = $this->getTodasUbicaciones();
        return response()->json([
            'success' => true,
            'data' => $ubicaciones
        ]);
    }

    /**
     * ✅ CORREGIDO: Vista del administrador para ver todos los vehículos en el mapa
     */
    public function adminMapa()
    {
        // Verificar que sea admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // ✅ CORREGIDO: Obtener todos los vehículos con sus servicios activos
        $vehiculos = Vehiculo::with(['servicios' => function($query) {
            $query->whereIn('estado', ['confirmado', 'en_progreso'])
                  ->with(['chofer', 'cliente']);
        }])->get();
        
        // Obtener ubicaciones de Firebase
        $ubicacionesFirebase = $this->getTodasUbicaciones();
        
        // Obtener servicios activos
        $serviciosActivos = Servicio::whereIn('estado', ['confirmado', 'en_progreso'])
            ->with(['cliente', 'chofer', 'vehiculo'])
            ->get();

        return view('gps.admin-mapa', compact('vehiculos', 'ubicacionesFirebase', 'serviciosActivos'));
    }

    /**
     * API para obtener ubicaciones de vehículos (para el mapa admin)
     */
    public function getUbicacionesVehiculos()
    {
        try {
            $ubicaciones = $this->getTodasUbicaciones();
            
            // ✅ CORREGIDO: Obtener vehículos con servicios activos
            $vehiculos = Vehiculo::with(['servicios' => function($query) {
                $query->whereIn('estado', ['confirmado', 'en_progreso'])
                      ->with(['chofer', 'cliente']);
            }])->get();
            
            $serviciosActivos = Servicio::whereIn('estado', ['confirmado', 'en_progreso'])
                ->with(['cliente', 'chofer', 'vehiculo'])
                ->get();

            $data = [];
            
            foreach ($ubicaciones as $ubicacion) {
                // Buscar si este dispositivo pertenece a algún chofer/vehículo
                $vehiculo = null;
                $servicio = null;
                $chofer = null;
                
                // Buscar en los servicios activos
                foreach ($serviciosActivos as $s) {
                    if ($s->chofer && strpos($ubicacion['dispositivoId'], (string)$s->chofer_id) !== false) {
                        $vehiculo = $s->vehiculo;
                        $chofer = $s->chofer;
                        $servicio = $s;
                        break;
                    }
                }
                
                $data[] = [
                    'id' => $ubicacion['id'],
                    'dispositivoId' => $ubicacion['dispositivoId'],
                    'lat' => $ubicacion['lat'],
                    'lng' => $ubicacion['lng'],
                    'vehiculo' => $vehiculo ? [
                        'id' => $vehiculo->id,
                        'placa' => $vehiculo->placa,
                        'marca' => $vehiculo->marca,
                        'modelo' => $vehiculo->modelo,
                        'tipo' => $vehiculo->tipo,
                        'capacidad_kg' => $vehiculo->capacidad_kg,
                    ] : null,
                    'chofer' => $chofer ? [
                        'id' => $chofer->id,
                        'nombre' => $chofer->nombre_completo,
                        'telefono' => $chofer->telefono ?? 'Sin teléfono',
                        'licencia' => $chofer->licencia ?? 'Sin licencia',
                    ] : null,
                    'servicio' => $servicio ? [
                        'id' => $servicio->id,
                        'cliente' => $servicio->cliente->nombre_completo ?? 'Sin cliente',
                        'origen' => $servicio->origen,
                        'destino' => $servicio->destino,
                        'estado' => $servicio->estado,
                        'fecha' => $servicio->fecha_servicio->format('d/m/Y'),
                    ] : null,
                    'plataforma' => $ubicacion['plataforma'],
                    'actualizado' => $ubicacion['actualizado'],
                    'timestamp' => $ubicacion['timestamp'] ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Función auxiliar para extraer valores de Firestore
     */
    private function getFieldValue($fields, $fieldName, $type)
    {
        if (!isset($fields[$fieldName])) {
            return null;
        }

        $field = $fields[$fieldName];
        
        switch ($type) {
            case 'string':
                return $field['stringValue'] ?? null;
            case 'double':
                return $field['doubleValue'] ?? null;
            case 'integer':
                return $field['integerValue'] ?? null;
            case 'boolean':
                return $field['booleanValue'] ?? null;
            default:
                return null;
        }
    }
}