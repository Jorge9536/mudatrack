<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GpsFirebaseController extends Controller
{
    protected $client;
    protected $firebaseUrl;
    protected $projectId;

    public function __construct()
    {
        $this->client = new Client();
        $this->projectId = 'gps1-e12e5';
        // URL de Firestore REST API
        $this->firebaseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    // Obtener todas las ubicaciones desde Firebase usando REST API
    public function getUbicacionesMapa()
    {
        try {
            // Endpoint para listar documentos en la colección "ubicaciones"
            $url = $this->firebaseUrl . '/ubicaciones';
            
            $response = $this->client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $ubicaciones = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    
                    // Extraer datos del documento
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener un dispositivo específico
    public function getUbicacionDispositivo($dispositivoId)
    {
        try {
            $url = $this->firebaseUrl . '/ubicaciones/' . $dispositivoId;
            
            $response = $this->client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Dispositivo no encontrado: ' . $e->getMessage()
            ], 404);
        }
    }

    // Obtener todos los dispositivos activos
    public function getDispositivos()
    {
        try {
            $url = $this->firebaseUrl . '/ubicaciones';
            
            $response = $this->client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $dispositivos = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $document) {
                    $fields = $document['fields'] ?? [];
                    
                    $dispositivo = [
                        'id' => basename($document['name']),
                        'dispositivoId' => $this->getFieldValue($fields, 'dispositivoId', 'string'),
                        'latitud' => (float) $this->getFieldValue($fields, 'latitud', 'double'),
                        'longitud' => (float) $this->getFieldValue($fields, 'longitud', 'double'),
                        'modelo' => $this->getFieldValue($fields, 'modelo', 'string') ?? 'Dispositivo',
                        'plataforma' => $this->getFieldValue($fields, 'plataforma', 'string') ?? 'desconocida',
                        'ultima_actualizacion' => $this->getFieldValue($fields, 'actualizado', 'string'),
                    ];

                    $dispositivos[] = $dispositivo;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $dispositivos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Función auxiliar para extraer valores de Firestore
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

    // Alternativa: Usar autenticación con API Key (más simple)
    public function getUbicacionesSimple()
    {
        try {
            // Usar API Key en lugar de autenticación OAuth
            $apiKey = 'AIzaSyDmOFmU0Gi6dH6uED0RKC1ve3-4-h3CV90';
            $url = "https://firestore.googleapis.com/v1/projects/gps1-e12e5/databases/(default)/documents/ubicaciones?key={$apiKey}";
            
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);
            
            return response()->json($data);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}