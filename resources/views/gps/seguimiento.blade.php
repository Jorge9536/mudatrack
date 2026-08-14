@extends('layouts.app')

@section('title', 'Seguimiento GPS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('gps.index') }}" class="text-decoration-none text-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 d-inline-block mb-0"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Seguimiento en Tiempo Real</h1>
            <small class="text-muted d-block">Servicio #{{ $servicio->id }} · {{ $servicio->cliente->nombre_completo }}</small>
        </div>
        <div>
            <span class="badge bg-success">
                <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> En vivo
            </span>
            <span class="badge bg-info ms-1">
                <i class="fas fa-route me-1"></i> {{ $servicio->distancia_km ?? 'N/A' }} km
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Mapa -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body p-0">
                    <div id="mapa" style="height: 450px; border-radius: 12px; overflow: hidden;"></div>
                </div>
            </div>
            
            <!-- Info adicional bajo el mapa -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="text-muted small">Distancia Total</div>
                            <strong>{{ $servicio->distancia_km ?? '0' }} km</strong>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Tiempo Estimado</div>
                            <strong id="tiempo-estimado">25 min</strong>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Progreso</div>
                            <div class="d-flex align-items-center justify-content-center">
                                <strong id="progreso">45%</strong>
                                <div class="progress ms-2" style="width:60px; height:6px;">
                                    <div id="barra-progreso" class="progress-bar bg-primary" style="width:45%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información -->
        <div class="col-lg-4">
            <!-- Estado del Servicio -->
            <div class="card shadow-sm mb-3" style="border-left: 4px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Estado del Servicio
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        <span class="badge bg-warning text-dark">
                            {{ $servicio->estado_label }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Origen</span>
                        <strong class="text-success" id="origen-text">
                            <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> 
                            {{ Str::limit($servicio->origen, 25) }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Destino</span>
                        <strong class="text-danger" id="destino-text">
                            <i class="fas fa-flag-checkered me-1"></i> 
                            {{ Str::limit($servicio->destino, 25) }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Distancia</span>
                        <strong>{{ $servicio->distancia_km ?? '0' }} km</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Chofer</span>
                        <strong>{{ $servicio->chofer->nombre_completo ?? 'No asignado' }}</strong>
                    </div>
                    <hr>
                    <div class="text-center">
                        <span class="badge bg-warning text-dark" style="padding: 8px 20px;">
                            <i class="fas fa-clock me-1"></i> Tiempo estimado: <span id="eta">15 min</span>
                        </span>
                        <p class="text-muted small mt-2 mb-0">
                            Distancia restante: <span id="distancia-restante">8.5 km</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Datos del Chofer -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-user-circle me-2 text-primary"></i>Chofer
                    </h6>
                    @if($servicio->chofer)
                    <div class="d-flex align-items-center">
                        <div class="avatar-placeholder me-3" 
                             style="width:48px;height:48px;background:#0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:1.2rem;">
                            {{ substr($servicio->chofer->nombre_completo, 0, 2) }}
                        </div>
                        <div>
                            <strong>{{ $servicio->chofer->nombre_completo }}</strong>
                            <p class="small text-muted mb-0">Lic. {{ $servicio->chofer->licencia }}</p>
                            <span class="badge bg-success">
                                <i class="fas fa-circle me-1" style="font-size:0.4rem;"></i> En ruta
                            </span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="fas fa-phone-alt me-1"></i> Llamar
                        </button>
                        <button class="btn btn-sm btn-outline-secondary flex-grow-1">
                            <i class="fas fa-comment me-1"></i> Mensaje
                        </button>
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                        <p class="mb-0">No hay chofer asignado</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 OBTENER COORDENADAS REALES DE LA BASE DE DATOS
    // Si tienes coordenadas guardadas en el servicio, úsalas
    // Por ahora usamos coordenadas de ejemplo para La Paz - El Alto
    
    const origen = [-16.500, -68.145];
    const destino = [-16.490, -68.125];
    const actual = [-16.495, -68.135];
    
    // Distancia real (si está en la base de datos)
    const distanciaTotal = {{ $servicio->distancia_km ?? 15 }};
    const distanciaRestante = distanciaTotal * 0.55; // Simulación

    // Inicializar mapa
    const map = L.map('mapa').setView([-16.498, -68.135], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Marcador Origen
    L.marker(origen, {
        icon: L.divIcon({
            html: '<i class="fas fa-circle" style="color:#198754;font-size:1.8rem;text-shadow:0 0 10px rgba(25,135,84,0.5);"></i>',
            className: '',
            iconSize: [20, 20]
        })
    }).addTo(map).bindPopup(`
        <strong>📍 Origen</strong><br>
        {{ $servicio->origen }}
    `);

    // Marcador Destino
    L.marker(destino, {
        icon: L.divIcon({
            html: '<i class="fas fa-flag-checkered" style="color:#dc3545;font-size:1.8rem;text-shadow:0 0 10px rgba(220,53,69,0.5);"></i>',
            className: '',
            iconSize: [20, 20]
        })
    }).addTo(map).bindPopup(`
        <strong>🏁 Destino</strong><br>
        {{ $servicio->destino }}
    `);

    // Marcador Vehículo (animado)
    const vehicleIcon = L.divIcon({
        html: '<i class="fas fa-truck" style="font-size:2.8rem;color:#0d6efd;text-shadow:0 0 30px rgba(13,110,253,0.6);"></i>',
        className: '',
        iconSize: [30, 30]
    });
    const vehicleMarker = L.marker(actual, { icon: vehicleIcon }).addTo(map)
        .bindPopup(`
            <strong>🚚 Vehículo</strong><br>
            Ubicación actual: Av. Montes, La Paz
        `);

    // Ruta simulada con más puntos para que se vea realista
    const routePoints = [
        [-16.500, -68.145],
        [-16.499, -68.142],
        [-16.498, -68.140],
        [-16.497, -68.138],
        [-16.496, -68.136],
        [-16.495, -68.135],
        [-16.494, -68.133],
        [-16.493, -68.131],
        [-16.492, -68.129],
        [-16.491, -68.127],
        [-16.490, -68.125]
    ];
    
    // Dibujar la ruta
    const routeLine = L.polyline(routePoints, { 
        color: '#0d6efd', 
        weight: 4, 
        opacity: 0.8,
        dashArray: '5, 10',
        lineJoin: 'round'
    }).addTo(map);

    // Dibujar ruta recorrida (hasta la posición actual)
    const puntosRecorridos = routePoints.slice(0, 5);
    L.polyline(puntosRecorridos, { 
        color: '#198754', 
        weight: 5, 
        opacity: 0.9,
        lineJoin: 'round'
    }).addTo(map);

    // Ajustar zoom
    map.fitBounds(routePoints);

    // Simular movimiento del vehículo
    let index = 4;
    const totalPuntos = routePoints.length;
    
    setInterval(() => {
        if (index < totalPuntos) {
            const pos = routePoints[index];
            vehicleMarker.setLatLng(pos);
            
            // Actualizar progreso
            const progreso = Math.round((index / totalPuntos) * 100);
            document.getElementById('progreso').textContent = progreso + '%';
            document.getElementById('barra-progreso').style.width = progreso + '%';
            
            // Actualizar distancia restante
            const distanciaRest = distanciaTotal * (1 - (index / totalPuntos));
            document.getElementById('distancia-restante').textContent = distanciaRest.toFixed(1) + ' km';
            
            // Actualizar ETA
            const etaMin = Math.round(distanciaRest * 1.8);
            document.getElementById('eta').textContent = etaMin + ' min';
            document.getElementById('tiempo-estimado').textContent = etaMin + ' min';
            
            index++;
        }
    }, 4000);
});
</script>
@endpush
@endsection