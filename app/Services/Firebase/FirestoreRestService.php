<?php

namespace App\Services\Firebase;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class FirestoreRestService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $projectId;
    protected $credentials;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', 'gps1-e12e5');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";

        $credentialsArray = null;

        // 1. Prioridad 1: Variable de Entorno (Ideal para Render, a prueba de fallos)
        $jsonContent = env('FIREBASE_CREDENTIALS_JSON');
        if (!empty($jsonContent)) {
            $credentialsArray = json_decode($jsonContent, true);
        }

        // 2. Prioridad 2: Archivo Secret de Render (Linux)
        if (!is_array($credentialsArray) || !isset($credentialsArray['private_key'])) {
            $renderSecretPath = '/etc/secrets/service-account.json';
            if (file_exists($renderSecretPath)) {
                $jsonContent = @file_get_contents($renderSecretPath);
                if ($jsonContent !== false) {
                    $credentialsArray = json_decode($jsonContent, true);
                }
            }
        }

        // 3. Aplicar credenciales si se encontraron
        if (is_array($credentialsArray) && isset($credentialsArray['private_key'])) {
            $this->credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/datastore',
                $credentialsArray
            );
        } else {
            // 4. Fallback final: Ruta local de Windows (Desarrollo)
            $credentialsPath = storage_path('app/firebase/service-account.json');
            $this->credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/datastore',
                $credentialsPath
            );
        }

        $this->client = new Client();
    }

    protected function getAccessToken(): string
    {
        $token = $this->credentials->fetchAuthToken(
            HttpHandlerFactory::build()
        );
        return $token['access_token'];
    }

    protected function toFirestoreFormat(array $datos): array
    {
        $fields = [];
        foreach ($datos as $key => $value) {
            $fields[$key] = $this->convertValue($value);
        }
        return ['fields' => $fields];
    }

    protected function convertValue($value): array
    {
        if (is_null($value)) {
            return ['nullValue' => null];
        }
        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }
        if (is_int($value)) {
            return ['integerValue' => (string) $value];
        }
        if (is_float($value)) {
            return ['doubleValue' => $value];
        }
        if (is_array($value)) {
            if (array_keys($value) !== range(0, count($value) - 1)) {
                return [
                    'mapValue' => [
                        'fields' => $this->toFirestoreFormat($value)['fields'],
                    ],
                ];
            }
            return [
                'arrayValue' => [
                    'values' => array_map(fn($v) => $this->convertValue($v), $value),
                ],
            ];
        }
        if (is_string($value)) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                return ['timestampValue' => $value];
            }
            return ['stringValue' => $value];
        }
        return ['stringValue' => (string) $value];
    }

    public function set(string $coleccion, string $id, array $datos): bool
    {
        try {
            $url = "{$this->baseUrl}/{$coleccion}/{$id}";
            $token = $this->getAccessToken();
            $payload = $this->toFirestoreFormat($datos);

            $response = $this->client->patch($url, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 30,
            ]);

            return $response->getStatusCode() === 200;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = $e->getResponse()->getBody()->getContents();
            $this->guardarErrorFirestore('CLIENT ERROR', $coleccion, $id, $body);
            return false;
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $body = $e->getResponse()->getBody()->getContents();
            $this->guardarErrorFirestore('SERVER ERROR', $coleccion, $id, $body);
            return false;
        } catch (\Throwable $e) {
            $this->guardarErrorFirestore('EXCEPTION', $coleccion, $id, $e->getMessage());
            return false;
        }
    }

    public function get(string $coleccion, string $id): ?array
    {
        try {
            $url = "{$this->baseUrl}/{$coleccion}/{$id}";
            $token = $this->getAccessToken();

            $response = $this->client->get($url, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody(), true);
            return $this->fromFirestoreFormat($data['fields'] ?? []);
        } catch (\Throwable $e) {
            $this->guardarErrorFirestore('GET ERROR', $coleccion, $id, $e->getMessage());
            return null;
        }
    }

    public function delete(string $coleccion, string $id): bool
    {
        try {
            $url = "{$this->baseUrl}/{$coleccion}/{$id}";
            $token = $this->getAccessToken();

            $response = $this->client->delete($url, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'timeout' => 30,
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            $this->guardarErrorFirestore('DELETE ERROR', $coleccion, $id, $e->getMessage());
            return false;
        }
    }

    protected function fromFirestoreFormat(array $fields): array
    {
        $resultado = [];
        foreach ($fields as $key => $field) {
            $resultado[$key] = $this->extractValue($field);
        }
        return $resultado;
    }

    protected function extractValue(array $field)
    {
        if (isset($field['stringValue'])) return $field['stringValue'];
        if (isset($field['integerValue'])) return (int) $field['integerValue'];
        if (isset($field['doubleValue'])) return (float) $field['doubleValue'];
        if (isset($field['booleanValue'])) return (bool) $field['booleanValue'];
        if (isset($field['timestampValue'])) return $field['timestampValue'];
        if (isset($field['nullValue'])) return null;
        if (isset($field['arrayValue'])) {
            return array_map(fn($v) => $this->extractValue($v), $field['arrayValue']['values'] ?? []);
        }
        if (isset($field['mapValue'])) {
            return $this->fromFirestoreFormat($field['mapValue']['fields'] ?? []);
        }
        return null;
    }

    protected function guardarErrorFirestore(string $tipo, string $coleccion, string $id, string $detalle): void
    {
        $logFile = storage_path('logs/firestore_error.log');
        $mensaje = str_repeat("=", 80) . "\n";
        $mensaje .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
        $mensaje .= "Tipo: {$tipo}\n";
        $mensaje .= "Ruta: {$coleccion}/{$id}\n";
        $mensaje .= "Detalle: {$detalle}\n";
        $mensaje .= str_repeat("=", 80) . "\n\n";

        @file_put_contents($logFile, $mensaje, FILE_APPEND);
    }
}