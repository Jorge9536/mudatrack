<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use App\Models\Chofer;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class DispositivoController extends Controller
{
    protected $client;
    protected $firestoreUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');

        $this->client = new Client();
        $this->firestoreUrl = config('services.firebase.base_url');
        $this->apiKey = config('services.firebase.api_key');
    }

    public function index()
    {
        $dispositivos = Dispositivo::with(['chofer', 'vehiculo'])->get();
        return view('dispositivos.index', compact('dispositivos'));
    }

    public function create()
    {
        $choferes = Chofer::where('disponible', true)->get();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        return view('dispositivos.create', compact('choferes', 'vehiculos'));
    }




    public function store(Request $request)
{
    $validated = $request->validate([
        'dispositivo_id' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_]+$/', 'unique:dispositivos'],
        'chofer_id' => ['required', 'exists:choferes,id'],
        'vehiculo_id' => ['nullable', 'exists:vehiculos,id'],
    ]);

    Dispositivo::create($validated);

    return redirect()->route('dispositivos.index')
        ->with('success', '✅ Dispositivo asignado correctamente.');
}

public function update(Request $request, Dispositivo $dispositivo)
{
    $validated = $request->validate([
        'dispositivo_id' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9\-_]+$/', 'unique:dispositivos,dispositivo_id,' . $dispositivo->id],
        'chofer_id' => ['required', 'exists:choferes,id'],
        'vehiculo_id' => ['nullable', 'exists:vehiculos,id'],
        'activo' => ['boolean'],
    ]);

    $dispositivo->update($validated);

    return redirect()->route('dispositivos.index')
        ->with('success', '✅ Dispositivo actualizado correctamente.');
}

    public function edit(Dispositivo $dispositivo)
    {
        $choferes = Chofer::where('disponible', true)->get();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        return view('dispositivos.edit', compact('dispositivo', 'choferes', 'vehiculos'));
    }


    public function destroy(Dispositivo $dispositivo)
    {
        $dispositivo->delete();
        return redirect()->route('dispositivos.index')
            ->with('success', '✅ Dispositivo eliminado correctamente.');
    }

    /**
     * 🔥 OBTENER DISPOSITIVOS DE FIRESTORE
     */
    public function obtenerDeFirestore()
    {
        try {
            $url = "{$this->firestoreUrl}/ubicaciones?key={$this->apiKey}";
            
            $response = $this->client->get($url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $data = json_decode($response->getBody(), true);
            $dispositivos = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    $dispositivoId = $this->getFieldValue($fields, 'dispositivoId', 'string');

                    if ($dispositivoId) {
                        $dispositivos[] = [
                            'dispositivoId' => $dispositivoId,
                            'actualizado' => $this->getFieldValue($fields, 'actualizado', 'string'),
                            'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string') ?? 'N/A',
                            'lat' => (float) $this->getFieldValue($fields, 'latitud', 'double'),
                            'lng' => (float) $this->getFieldValue($fields, 'longitud', 'double'),
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $dispositivos
            ]);

        } catch (GuzzleException $e) {
            Log::error('Error obteniendo dispositivos de Firestore:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al conectar con Firebase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 ELIMINAR DISPOSITIVO DE FIRESTORE (por modelo)
     */
    public function eliminarDeFirestore(Dispositivo $dispositivo)
    {
        try {
            if (!$dispositivo->dispositivo_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'El dispositivo no tiene ID de Firebase'
                ]);
            }

            $dispositivoId = $dispositivo->dispositivo_id;
            $url = "{$this->firestoreUrl}/ubicaciones/{$dispositivoId}?key={$this->apiKey}";

            $this->client->delete($url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);

            Log::info('Dispositivo eliminado de Firestore:', [
                'dispositivo_id' => $dispositivoId
            ]);

            return response()->json([
                'success' => true,
                'message' => "Dispositivo '{$dispositivoId}' eliminado de Firebase correctamente"
            ]);

        } catch (GuzzleException $e) {
            Log::error('Error eliminando de Firestore:', [
                'error' => $e->getMessage(),
                'dispositivo_id' => $dispositivo->dispositivo_id ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 ELIMINAR DISPOSITIVO DE FIRESTORE POR SU ID
     * (sin necesidad de que esté registrado en la BD local)
     */
    public function eliminarDeFirestorePorId($dispositivoId)
    {
        try {
            if (empty($dispositivoId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de dispositivo inválido'
                ], 400);
            }

            $url = "{$this->firestoreUrl}/ubicaciones/{$dispositivoId}?key={$this->apiKey}";

            $this->client->delete($url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);

            Log::info('Dispositivo eliminado de Firestore:', [
                'dispositivo_id' => $dispositivoId
            ]);

            return response()->json([
                'success' => true,
                'message' => "Dispositivo '{$dispositivoId}' eliminado de Firebase correctamente"
            ]);

        } catch (GuzzleException $e) {
            Log::error('Error eliminando de Firestore:', [
                'dispositivo_id' => $dispositivoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
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
            case 'timestamp':
                return $field['timestampValue'] ?? null;
            default:
                return null;
        }
    }
}