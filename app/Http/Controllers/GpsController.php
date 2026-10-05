<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\UbicacionGps;
use App\Models\Vehiculo;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;

class GpsController extends BaseController
{
    protected $firebaseClient;
    protected $firebaseUrl;

    public function __construct()
    {
        $this->middleware('auth')->except([
            'seguimientoPublico',
            'ubicacionPublica'
        ]);
        
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('cliente', function($q2) use ($search) {
                    $q2->where('nombre_completo', 'LIKE', "%{$search}%");
                })->orWhere('origen', 'LIKE', "%{$search}%")
                  ->orWhere('destino', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_servicio', $request->fecha);
        }

        $servicios = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $dispositivosFirebase = $this->getTodasUbicaciones();

        return view('gps.index', compact('servicios', 'dispositivosFirebase'));
    }

    /**
     * Seguimiento GPS (privado, con login)
     */
    public function seguimiento($id)
    {
        $servicio = Servicio::with(['cliente', 'chofer', 'vehiculo'])->findOrFail($id);
        
        if (!in_array($servicio->estado, ['confirmado', 'en_progreso', 'pendiente'])) {
            abort(404, 'Servicio no disponible para seguimiento');
        }

        $ubicaciones = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $ultimaUbicacion = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'desc')
            ->first();

        $ubicacionFirebase = null;
        $dispositivosFirebase = [];
        $dispositivoAsignado = null;

        if ($servicio->chofer) {
            $dispositivoAsignado = Dispositivo::where('chofer_id', $servicio->chofer->id)
                ->where('activo', true)
                ->first();

            if ($dispositivoAsignado) {
                $ubicacionFirebase = $this->getUbicacionPorDispositivoId($dispositivoAsignado->dispositivo_id);
                
                if ($ubicacionFirebase) {
                    $dispositivosFirebase = [$ubicacionFirebase];
                }
            }
        }

        return view('gps.seguimiento', compact(
            'servicio', 
            'ubicaciones', 
            'ultimaUbicacion',
            'ubicacionFirebase',
            'dispositivosFirebase',
            'dispositivoAsignado'
        ));
    }

    /**
     * 🔥 VISTA PÚBLICA DE SEGUIMIENTO (sin login)
     */
    public function seguimientoPublico($token)
    {
        $servicio = Servicio::where('token_seguimiento', $token)
            ->with(['cliente', 'chofer', 'vehiculo'])
            ->first();

        if (!$servicio) {
            abort(404, 'Enlace de seguimiento no válido o expirado');
        }

        if (!in_array($servicio->estado, ['confirmado', 'en_progreso', 'pendiente'])) {
            return view('gps.seguimiento-publico-inactivo', compact('servicio'));
        }

        $ubicaciones = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        $dispositivoAsignado = null;
        $ubicacionFirebase = null;

        if ($servicio->chofer) {
            $dispositivoAsignado = Dispositivo::where('chofer_id', $servicio->chofer->id)
                ->where('activo', true)
                ->first();

            if ($dispositivoAsignado) {
                $ubicacionFirebase = $this->getUbicacionPorDispositivoId($dispositivoAsignado->dispositivo_id);
            }
        }

        return view('gps.seguimiento-publico', compact(
            'servicio',
            'ubicaciones',
            'dispositivoAsignado',
            'ubicacionFirebase',
            'token'
        ));
    }

    /**
     * 🔥 API PÚBLICA: Obtener ubicación por token (para AJAX)
     */
    public function ubicacionPublica($token)
    {
        $servicio = Servicio::where('token_seguimiento', $token)->first();

        if (!$servicio) {
            return response()->json(['success' => false, 'message' => 'Token inválido'], 404);
        }

        if (!$servicio->chofer) {
            return response()->json(['success' => false, 'message' => 'Sin chofer asignado'], 404);
        }

        $dispositivo = Dispositivo::where('chofer_id', $servicio->chofer->id)
            ->where('activo', true)
            ->first();

        if (!$dispositivo) {
            return response()->json(['success' => false, 'message' => 'Sin dispositivo asignado'], 404);
        }

        $ubicacion = $this->getUbicacionPorDispositivoId($dispositivo->dispositivo_id);

        if (!$ubicacion) {
            return response()->json(['success' => false, 'message' => 'Sin ubicación disponible'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ubicacion
        ]);
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
     * Última ubicación de un servicio
     */
    public function ultimaUbicacion($id)
    {
        $servicio = Servicio::findOrFail($id);
        
        $ubicacionLocal = UbicacionGps::where('servicio_id', $servicio->id)
            ->orderBy('fecha_hora', 'desc')
            ->first();

        $ubicacionFirebase = null;
        if ($servicio->chofer) {
            $dispositivo = Dispositivo::where('chofer_id', $servicio->chofer->id)
                ->where('activo', true)
                ->first();
            
            if ($dispositivo) {
                $ubicacionFirebase = $this->getUbicacionPorDispositivoId($dispositivo->dispositivo_id);
            }
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
     * Historial de ubicaciones
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

    private function getDispositivosAsignados()
    {
        return Dispositivo::with(['chofer', 'vehiculo'])->where('activo', true)->get();
    }

    // ============================================
    // MÉTODOS PARA FIREBASE
    // ============================================

    /**
     * Obtener ubicación de UN dispositivo específico
     */
    private function getUbicacionPorDispositivoId($dispositivoId)
    {
        try {
            $url = $this->firebaseUrl . '/ubicaciones/' . $dispositivoId;
            $response = $this->firebaseClient->get($url);
            $data = json_decode($response->getBody(), true);

            if (!isset($data['fields'])) {
                return null;
            }

            $fields = $data['fields'];
            
            $lat = (float) $this->getFieldValue($fields, 'latitud', 'double');
            $lng = (float) $this->getFieldValue($fields, 'longitud', 'double');

            if ($lat == 0 && $lng == 0) {
                return null;
            }

            return [
                'id' => basename($data['name']),
                'dispositivoId' => $this->getFieldValue($fields, 'dispositivoId', 'string') ?? $dispositivoId,
                'lat' => $lat,
                'lng' => $lng,
                'nombre' => $this->getFieldValue($fields, 'modelo', 'string') ?? 'Dispositivo',
                'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string') ?? 'desconocida',
                'actualizado' => $this->getFieldValue($fields, 'actualizado', 'string'),
                'timestamp' => (int) $this->getFieldValue($fields, 'timestamp', 'integer'),
            ];

        } catch (\Exception $e) {
            \Log::error('Error al obtener ubicación por ID:', [
                'dispositivo_id' => $dispositivoId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * API: Obtener ubicación de UN dispositivo específico
     */
    public function getUbicacionDispositivoApi($dispositivoId)
    {
        try {
            $ubicacion = $this->getUbicacionPorDispositivoId($dispositivoId);

            if (!$ubicacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay ubicación disponible para este dispositivo'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $ubicacion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener TODAS las ubicaciones (admin)
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
     * API: Todas las ubicaciones (admin)
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
     * Vista del administrador
     */
    public function adminMapa()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $vehiculos = Vehiculo::with(['servicios' => function($query) {
            $query->whereIn('estado', ['confirmado', 'en_progreso'])
                  ->with(['chofer', 'cliente']);
        }])->get();
        
        $ubicacionesFirebase = $this->getTodasUbicaciones();
        
        $serviciosActivos = Servicio::whereIn('estado', ['confirmado', 'en_progreso'])
            ->with(['cliente', 'chofer', 'vehiculo'])
            ->get();

        return view('gps.admin-mapa', compact('vehiculos', 'ubicacionesFirebase', 'serviciosActivos'));
    }

    /**
     * API con nombres de choferes (admin)
     */
    public function getUbicacionesVehiculos()
    {
        try {
            $dispositivos = $this->getDispositivosAsignados();
            $ubicaciones = $this->getTodasUbicaciones();
            
            $data = [];
            
            foreach ($ubicaciones as $ubicacion) {
                $dispositivo = $dispositivos->firstWhere('dispositivo_id', $ubicacion['dispositivoId']);
                
                $data[] = [
                    'id' => $ubicacion['id'],
                    'dispositivoId' => $ubicacion['dispositivoId'],
                    'lat' => $ubicacion['lat'],
                    'lng' => $ubicacion['lng'],
                    'chofer' => $dispositivo ? [
                        'id' => $dispositivo->chofer->id ?? null,
                        'nombre' => $dispositivo->chofer->nombre_completo ?? 'Sin asignar',
                        'telefono' => $dispositivo->chofer->telefono ?? null,
                    ] : null,
                    'vehiculo' => $dispositivo ? [
                        'id' => $dispositivo->vehiculo->id ?? null,
                        'placa' => $dispositivo->vehiculo->placa ?? 'Sin vehículo',
                        'marca' => $dispositivo->vehiculo->marca ?? '',
                        'modelo' => $dispositivo->vehiculo->modelo ?? '',
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
     * Función auxiliar para Firestore
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