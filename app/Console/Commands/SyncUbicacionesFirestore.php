<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dispositivo;
use App\Models\UbicacionGps;
use App\Models\Servicio;
use GuzzleHttp\Client;
use Carbon\Carbon;

class SyncUbicacionesFirestore extends Command
{
    protected $signature = 'firebase:sync-ubicaciones';
    protected $description = 'Lee las ubicaciones de Firestore y actualiza los servicios en progreso';

    public function handle()
    {
        $this->info('📡 Leyendo ubicaciones de Firestore...');

        $client = new Client();
        $url = 'https://firestore.googleapis.com/v1/projects/gps1-e12e5/databases/(default)/documents/ubicaciones';

        try {
            $response = $client->get($url, ['timeout' => 10]);
            $data = json_decode($response->getBody(), true);
        } catch (\Throwable $e) {
            $this->error('❌ Error consultando Firestore: ' . $e->getMessage());
            return 1;
        }

        if (!isset($data['documents'])) {
            $this->warn('Sin ubicaciones en Firestore.');
            return 0;
        }

        $sincronizadas = 0;

        foreach ($data['documents'] as $document) {
            $fields = $document['fields'] ?? [];
            $dispositivoId = $fields['dispositivoId']['stringValue'] ?? null;

            if (!$dispositivoId) continue;

            $lat = (float) ($fields['latitud']['doubleValue'] ?? 0);
            $lng = (float) ($fields['longitud']['doubleValue'] ?? 0);

            if ($lat == 0 && $lng == 0) continue;

            // Buscar el dispositivo en la BD
            $dispositivo = Dispositivo::where('dispositivo_id', $dispositivoId)
                ->where('activo', true)
                ->first();

            if (!$dispositivo || !$dispositivo->chofer_id) continue;

            // Buscar el servicio en progreso del chofer
            $servicio = Servicio::where('chofer_id', $dispositivo->chofer_id)
                ->where('estado', 'en_progreso')
                ->first();

            if (!$servicio) continue;

            // Guardar ubicación en PostgreSQL
            UbicacionGps::create([
                'servicio_id' => $servicio->id,
                'latitud' => $lat,
                'longitud' => $lng,
                'velocidad' => $fields['velocidad']['doubleValue'] ?? 0,
                'fecha_hora' => now(),
            ]);

            $sincronizadas++;
            $this->line("   📍 {$dispositivoId} → Servicio #{$servicio->id} ({$lat}, {$lng})");
        }

        $this->info("✅ {$sincronizadas} ubicaciones sincronizadas.");
        return 0;
    }
}