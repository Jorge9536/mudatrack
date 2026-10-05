<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class GpsFirebaseController extends Controller
{
    protected $client;
    protected $firestoreUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->firestoreUrl = config('services.firebase.base_url');
        $this->apiKey = config('services.firebase.api_key');
    }

    /**
     * Obtener todas las ubicaciones desde Firestore
     */
    public function getUbicacionesMapa()
    {
        try {
            $url = "{$this->firestoreUrl}/ubicaciones?key={$this->apiKey}";
            
            $response = $this->client->get($url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $data = json_decode($response->getBody(), true);
            $ubicaciones = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    
                    $ubicacion = [
                        'id' => basename($document['name']),
                        'dispositivoId' => $this->getFieldValue($fields, 'dispositivoId', 'string'),
                        'lat' => (float) $this->getFieldValue($fields, 'latitud', 'double'),
                        'lng' => (float) $this->getFieldValue($fields, 'longitud', 'double'),
                        'nombre' => $this->getFieldValue($fields, 'modelo', 'string') ?? 'Dispositivo',
                        'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string') ?? 'desconocida',
                        'actualizado' => $this->getFieldValue($fields, 'actualizado', 'string'),
                        'timestamp' => (int) $this->getFieldValue($fields, 'timestamp', 'integer'),
                    ];

                    // Solo agregar si tiene coordenadas válidas
                    if ($ubicacion['lat'] != 0 && $ubicacion['lng'] != 0) {
                        $ubicaciones[] = $ubicacion;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $ubicaciones
            ]);

        } catch (GuzzleException $e) {
            Log::error('Error obteniendo ubicaciones:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un dispositivo específico desde Firestore
     */
    public function getUbicacionDispositivo($dispositivoId)
    {
        try {
            $url = "{$this->firestoreUrl}/ubicaciones/{$dispositivoId}?key={$this->apiKey}";
            
            $response = $this->client->get($url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $document = json_decode($response->getBody(), true);
            $fields = $document['fields'] ?? [];

            $ubicacion = [
                'id' => basename($document['name']),
                'dispositivoId' => $this->getFieldValue($fields, 'dispositivoId', 'string'),
                'lat' => (float) $this->getFieldValue($fields, 'latitud', 'double'),
                'lng' => (float) $this->getFieldValue($fields, 'longitud', 'double'),
                'nombre' => $this->getFieldValue($fields, 'modelo', 'string') ?? 'Dispositivo',
                'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string') ?? 'desconocida',
                'actualizado' => $this->getFieldValue($fields, 'actualizado', 'string'),
                'timestamp' => (int) $this->getFieldValue($fields, 'timestamp', 'integer'),
            ];

            return response()->json([
                'success' => true,
                'data' => $ubicacion
            ]);

        } catch (GuzzleException $e) {
            Log::error('Error obteniendo dispositivo:', [
                'dispositivo_id' => $dispositivoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Dispositivo no encontrado: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Obtener todos los dispositivos activos
     */
    public function getDispositivos()
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
                    
                    $dispositivos[] = [
                        'id' => basename($document['name']),
                        'dispositivoId' => $this->getFieldValue($fields, 'dispositivoId', 'string'),
                        'latitud' => (float) $this->getFieldValue($fields, 'latitud', 'double'),
                        'longitud' => (float) $this->getFieldValue($fields, 'longitud', 'double'),
                        'modelo' => $this->getFieldValue($fields, 'modelo', 'string') ?? 'Dispositivo',
                        'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string') ?? 'desconocida',
                        'ultima_actualizacion' => $this->getFieldValue($fields, 'actualizado', 'string'),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $dispositivos
            ]);

        } catch (GuzzleException $e) {
            Log::error('Error obteniendo dispositivos:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 ELIMINAR DISPOSITIVO DE FIRESTORE
     */
    public function eliminarDispositivo($dispositivoId)
    {
        try {
            $url = "{$this->firestoreUrl}/ubicaciones/{$dispositivoId}?key={$this->apiKey}";

            $response = $this->client->delete($url, [
                'headers' => ['Content-Type' => 'application/json']
            ]);

            Log::info('Dispositivo eliminado de Firestore:', [
                'dispositivo_id' => $dispositivoId
            ]);

            return response()->json([
                'success' => true,
                'message' => "Dispositivo '{$dispositivoId}' eliminado correctamente"
            ]);

        } catch (GuzzleException $e) {
            Log::error('Error eliminando dispositivo:', [
                'dispositivo_id' => $dispositivoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar: ' . $e->getMessage()
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