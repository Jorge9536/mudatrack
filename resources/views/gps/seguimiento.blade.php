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
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <span class="badge bg-success" id="estado-conexion">
                <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> En vivo
            </span>
            <span class="badge bg-info">
                <i class="fas fa-sync me-1"></i> <span id="segundos">0</span>s
            </span>
            
            {{-- 🔥 BOTÓN COMPARTIR POR WHATSAPP --}}
            <button class="btn btn-success btn-sm" onclick="compartirWhatsApp()">
                <i class="fab fa-whatsapp me-1"></i> Compartir con el cliente
            </button>
            
            {{-- 🔥 BOTÓN COPIAR URL --}}
            <button class="btn btn-outline-secondary btn-sm" onclick="copiarUrl()">
                <i class="fas fa-copy me-1"></i> Copiar enlace
            </button>
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
                        <div class="col-md-4">
                            <div class="text-muted small">Última Actualización</div>
                            <strong id="ultima-actualizacion">Cargando...</strong>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Ubicaciones Registradas</div>
                            <strong>{{ $ubicaciones->count() }}</strong>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Estado</div>
                            <strong class="text-success" id="estado-gps">
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
                    @if($dispositivoAsignado)
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">📡 Dispositivo</span>
                        <strong>{{ $dispositivoAsignado->dispositivo_id }}</strong>
                    </div>
                    @endif
                    @if($ubicacionFirebase)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">📍 Última posición</span>
                        <strong>
                            {{ number_format($ubicacionFirebase['lat'], 5) }}, 
                            {{ number_format($ubicacionFirebase['lng'], 5) }}
                        </strong>
                    </div>
                    @else
                    <div class="alert alert-warning mt-3 mb-0 py-2 small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Sin ubicación GPS disponible
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
    const servicio = @json($servicio);
    const ubicacionesLocal = @json($ubicaciones);
    const dispositivoId = '{{ $dispositivoAsignado->dispositivo_id ?? "" }}';
    
    let map;
    let vehicleMarker = null;
    let rutaLinea = null;
    let updateInterval;
    let contadorSegundos = 0;

    function initMap() {
        const center = [-16.5, -68.13];
        map = L.map('mapa').setView(center, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    }

    function cargarUbicacionDispositivo() {
        if (!dispositivoId) {
            document.getElementById('ultima-actualizacion').textContent = 'Sin dispositivo';
            document.getElementById('estado-gps').innerHTML = 
                '<i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> Sin GPS';
            document.getElementById('estado-gps').className = 'text-danger';
            return;
        }

        fetch(`/gps/dispositivo/${dispositivoId}/ubicacion`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    actualizarMapa(data.data);
                    actualizarInfo(data.data);
                } else {
                    document.getElementById('ultima-actualizacion').textContent = 'Sin datos';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('ultima-actualizacion').textContent = 'Error de conexión';
            });
    }

    function actualizarMapa(ubicacion) {
        if (vehicleMarker) {
            map.removeLayer(vehicleMarker);
        }

        const vehicleIcon = L.divIcon({
            html: `
                <div style="
                    background: #0d6efd;
                    border-radius: 50%;
                    width: 45px;
                    height: 45px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 3px solid white;
                    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.5);
                ">
                    <i class="fas fa-truck" style="font-size: 22px; color: white;"></i>
                </div>
            `,
            className: '',
            iconSize: [45, 45],
            iconAnchor: [22, 22]
        });

        vehicleMarker = L.marker([ubicacion.lat, ubicacion.lng], { 
            icon: vehicleIcon,
            zIndexOffset: 1000
        })
        .addTo(map)
        .bindPopup(`
            <div style="min-width: 200px;">
                <strong>🚚 ${servicio.chofer ? servicio.chofer.nombre_completo : 'Vehículo'}</strong><br>
                <hr style="margin: 5px 0;">
                <small>
                    <i class="fas fa-map-pin"></i> ${ubicacion.lat.toFixed(6)}, ${ubicacion.lng.toFixed(6)}<br>
                    <i class="fas fa-clock"></i> ${ubicacion.actualizado ? new Date(ubicacion.actualizado).toLocaleString('es-BO') : 'Sin datos'}<br>
                    <i class="fas fa-satellite-dish"></i> ${ubicacion.dispositivoId}
                </small>
            </div>
        `)
        .openPopup();

        map.setView([ubicacion.lat, ubicacion.lng], 15);
    }

    function mostrarRutaHistorica() {
        if (ubicacionesLocal.length < 2) return;

        if (rutaLinea) {
            map.removeLayer(rutaLinea);
        }

        const points = ubicacionesLocal.map(u => [u.latitud, u.longitud]);
        rutaLinea = L.polyline(points, {
            color: '#ffc107',
            weight: 4,
            opacity: 0.7,
            dashArray: '8, 8'
        }).addTo(map);
    }

    function actualizarInfo(ubicacion) {
        if (ubicacion.actualizado) {
            document.getElementById('ultima-actualizacion').textContent = timeAgo(ubicacion.actualizado);
        }
        document.getElementById('estado-gps').innerHTML = 
            '<i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> Activo';
        document.getElementById('estado-gps').className = 'text-success';
    }

    function timeAgo(fecha) {
        if (!fecha) return 'Nunca';
        const ahora = new Date();
        const fechaDate = new Date(fecha);
        const diff = Math.floor((ahora - fechaDate) / 1000);
        
        if (diff < 0) return 'Ahora';
        if (diff < 60) return 'Hace ' + diff + ' seg';
        if (diff < 3600) return 'Hace ' + Math.floor(diff / 60) + ' min';
        if (diff < 86400) return 'Hace ' + Math.floor(diff / 3600) + ' h';
        return 'Hace ' + Math.floor(diff / 86400) + ' días';
    }

    function actualizarSegundos() {
        contadorSegundos++;
        document.getElementById('segundos').textContent = contadorSegundos;
        if (contadorSegundos > 10) {
            document.getElementById('estado-conexion').className = 'badge bg-warning';
            document.getElementById('estado-conexion').innerHTML = '<i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> Reconectando...';
        }
    }

    // 🔥 COMPARTIR POR WHATSAPP
    window.compartirWhatsApp = function() {
        const token = '{{ $servicio->token_seguimiento }}';
        const url = `${window.location.origin}/seguimiento/${token}`;
        const cliente = '{{ $servicio->cliente->nombre_completo }}';
        const telefono = '{{ preg_replace("/[^0-9]/", "", $servicio->cliente->telefono ?? "") }}';
        
        if (!telefono) {
            alert('⚠️ El cliente no tiene teléfono registrado');
            return;
        }
        
        let telefonoFull = telefono;
        if (telefono.length === 8) {
            telefonoFull = '591' + telefono;
        }
        
        const mensaje = `Hola ${cliente}, puede ver el seguimiento en tiempo real de su mudanza aquí:\n\n${url}\n\nMudatrack 🚚`;
        
        const whatsappUrl = `https://wa.me/${telefonoFull}?text=${encodeURIComponent(mensaje)}`;
        window.open(whatsappUrl, '_blank');
    };

    // 🔥 COPIAR URL
    window.copiarUrl = function() {
        const token = '{{ $servicio->token_seguimiento }}';
        const url = `${window.location.origin}/seguimiento/${token}`;
        
        navigator.clipboard.writeText(url).then(() => {
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> ¡Copiado!';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 2000);
        }).catch(err => {
            prompt('Copia el enlace:', url);
        });
    };

    initMap();
    mostrarRutaHistorica();
    cargarUbicacionDispositivo();

    updateInterval = setInterval(() => {
        cargarUbicacionDispositivo();
        contadorSegundos = 0;
        document.getElementById('estado-conexion').className = 'badge bg-success';
        document.getElementById('estado-conexion').innerHTML = '<i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> En vivo';
    }, 10000);

    setInterval(actualizarSegundos, 1000);

    window.addEventListener('beforeunload', function() {
        if (updateInterval) clearInterval(updateInterval);
    });
});
</script>
@endpush
@endsection