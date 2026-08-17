@extends('layouts.app')

@section('title', 'Seguimiento GPS - ' . $servicio->cliente->nombre_completo)

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
            <span class="badge bg-success" id="estado-conexion">
                <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> En vivo
            </span>
            <span class="badge bg-info ms-1">
                <i class="fas fa-sync me-1"></i> <span id="segundos">0</span>s
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Mapa -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body p-0">
                    <div id="mapa" style="height: 500px; border-radius: 12px; overflow: hidden;"></div>
                </div>
            </div>
            
            <!-- Info adicional -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="text-muted small">Dispositivos Activos</div>
                            <strong id="total-dispositivos">{{ count($dispositivosFirebase ?? []) }}</strong>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Última Actualización</div>
                            <strong id="ultima-actualizacion">Hace 5 seg</strong>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Ubicaciones BD Local</div>
                            <strong>{{ $ubicaciones->count() }}</strong>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Estado</div>
                            <strong class="text-success">
                                <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> Activo
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del servicio -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3" style="border-left: 4px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Estado del Servicio
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        <span class="badge bg-{{ $servicio->estado == 'en_progreso' ? 'warning' : 'primary' }}">
                            {{ $servicio->estado_label }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Origen</span>
                        <strong class="text-success">{{ Str::limit($servicio->origen, 25) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Destino</span>
                        <strong class="text-danger">{{ Str::limit($servicio->destino, 25) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Chofer</span>
                        <strong>{{ $servicio->chofer->nombre_completo ?? 'No asignado' }}</strong>
                    </div>
                    @if($ubicacionFirebase)
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">📍 Firebase</span>
                        <strong>
                            {{ number_format($ubicacionFirebase['lat'], 6) }}, 
                            {{ number_format($ubicacionFirebase['lng'], 6) }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Dispositivo</span>
                        <strong>{{ $ubicacionFirebase['modelo'] ?? 'Móvil' }}</strong>
                    </div>
                    @endif
                    @if($ultimaUbicacion)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">📍 BD Local</span>
                        <strong>
                            {{ number_format($ultimaUbicacion->latitud, 6) }}, 
                            {{ number_format($ultimaUbicacion->longitud, 6) }}
                        </strong>
                    </div>
                    @endif
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
    // Datos de Firebase
    const dispositivosFirebase = @json($dispositivosFirebase);
    const ubicacionFirebase = @json($ubicacionFirebase);
    const servicio = @json($servicio);
    const ubicacionesLocal = @json($ubicaciones);
    
    let map;
    let markers = {};
    let vehicleMarker = null;
    let updateInterval;
    let contadorSegundos = 0;

    // Inicializar mapa
    function initMap() {
        // Centro en Bolivia
        const center = [-16.5, -68.13];
        map = L.map('mapa').setView(center, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    }

    // Cargar ubicaciones de Firebase
    function cargarUbicacionesFirebase() {
        fetch('{{ route("gps.firebase.ubicaciones") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    actualizarMapa(data.data);
                    actualizarContador(data.data.length);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Actualizar mapa con ubicaciones
    function actualizarMapa(ubicaciones) {
        // Limpiar marcadores anteriores
        Object.keys(markers).forEach(key => {
            if (markers[key]) {
                map.removeLayer(markers[key]);
            }
        });
        markers = {};

        // Mostrar ubicaciones de Firebase
        ubicaciones.forEach(ubicacion => {
            const color = ubicacion.plataforma === 'android' ? '#0d6efd' : '#6c757d';
            
            const icon = L.divIcon({
                className: 'custom-div-icon',
                html: `
                    <div style="background: ${color}; border-radius: 50%; width: 14px; height: 14px; border: 3px solid white; box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);"></div>
                `,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            const marker = L.marker([ubicacion.lat, ubicacion.lng], { icon })
                .addTo(map)
                .bindPopup(`
                    <strong>${ubicacion.nombre}</strong><br>
                    <i class="fab fa-${ubicacion.plataforma}"></i> ${ubicacion.plataforma}<br>
                    <i class="fas fa-id-card"></i> ${ubicacion.dispositivoId}<br>
                    <i class="fas fa-map-pin"></i> ${ubicacion.lat.toFixed(6)}, ${ubicacion.lng.toFixed(6)}<br>
                    <small><i class="fas fa-clock"></i> ${ubicacion.actualizado ? new Date(ubicacion.actualizado).toLocaleString() : 'Sin datos'}</small>
                `);

            markers[ubicacion.dispositivoId] = marker;
        });

        // Mostrar ubicaciones de la BD local (como línea de ruta)
        if (ubicacionesLocal.length > 0) {
            const points = ubicacionesLocal.map(u => [u.latitud, u.longitud]);
            L.polyline(points, {
                color: '#ffc107',
                weight: 3,
                opacity: 0.7,
                dashArray: '5, 10'
            }).addTo(map);
        }

        // Marcar el vehículo del servicio actual
        if (ubicacionFirebase) {
            const vehicleIcon = L.divIcon({
                html: '<i class="fas fa-truck" style="font-size:2.8rem;color:#0d6efd;text-shadow:0 0 30px rgba(13,110,253,0.6);"></i>',
                className: '',
                iconSize: [30, 30]
            });
            vehicleMarker = L.marker([ubicacionFirebase.lat, ubicacionFirebase.lng], { icon: vehicleIcon })
                .addTo(map)
                .bindPopup(`
                    <strong>🚚 Vehículo</strong><br>
                    Chofer: ${servicio.chofer?.nombre_completo || 'N/A'}<br>
                    Lat: ${ubicacionFirebase.lat.toFixed(6)}<br>
                    Lng: ${ubicacionFirebase.lng.toFixed(6)}
                `);
        }

        // Ajustar zoom
        const allMarkers = Object.values(markers);
        if (allMarkers.length > 0) {
            const group = L.featureGroup(allMarkers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Actualizar contador y tiempo
    function actualizarContador(total) {
        document.getElementById('total-dispositivos').textContent = total;
        document.getElementById('ultima-actualizacion').textContent = 'Hace ' + contadorSegundos + ' seg';
    }

    // Actualizar contador de segundos
    function actualizarSegundos() {
        contadorSegundos++;
        document.getElementById('segundos').textContent = contadorSegundos;
        if (contadorSegundos > 10) {
            document.getElementById('estado-conexion').className = 'badge bg-warning';
            document.getElementById('estado-conexion').innerHTML = '<i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> Reconectando...';
        }
    }

    // Inicializar
    initMap();
    cargarUbicacionesFirebase();

    // Actualizar cada 10 segundos
    updateInterval = setInterval(() => {
        cargarUbicacionesFirebase();
        contadorSegundos = 0;
        document.getElementById('estado-conexion').className = 'badge bg-success';
        document.getElementById('estado-conexion').innerHTML = '<i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> En vivo';
    }, 10000);

    // Actualizar contador de segundos cada segundo
    setInterval(actualizarSegundos, 1000);

    // Limpiar al salir
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });
});
</script>

<style>
    .custom-div-icon {
        background: transparent;
        border: none;
    }
</style>
@endpush
@endsection