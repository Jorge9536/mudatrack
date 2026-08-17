@extends('layouts.app')

@section('title', 'Mapa de Vehículos - Admin')

@section('content')
<style>
    .vehicle-marker {
        background: transparent;
        border: none;
    }
    .vehicle-marker .vehicle-icon {
        font-size: 2.5rem;
        text-shadow: 0 0 20px rgba(13, 110, 253, 0.6);
        transition: all 0.3s ease;
    }
    .vehicle-marker .vehicle-icon:hover {
        transform: scale(1.2);
    }
    .vehicle-marker .vehicle-icon .badge-placa {
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.5rem;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        white-space: nowrap;
    }
    .estado-indicador {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        animation: pulse 1.5s ease-in-out infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1); }
    }
    .estado-activo { background: #28a745; }
    .estado-inactivo { background: #dc3545; }
    .estado-pendiente { background: #ffc107; }
    
    .sidebar-vehiculos {
        max-height: 600px;
        overflow-y: auto;
    }
    .vehiculo-item {
        cursor: pointer;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .vehiculo-item:hover {
        background: #f8f9fa;
        border-left-color: #0d6efd;
    }
    .vehiculo-item.active {
        background: #e3f2fd;
        border-left-color: #0d6efd;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-truck me-2 text-primary"></i>
                Mapa de Vehículos en Tiempo Real
            </h1>
            <small class="text-muted">
                <i class="fas fa-circle text-success me-1" style="font-size:0.5rem;"></i>
                <span id="total-activos">0</span> vehículos activos
                · 
                <i class="fas fa-sync me-1"></i>
                Actualizando cada <span id="intervalo-segundos">10</span>s
            </small>
        </div>
        <div>
            <button class="btn btn-outline-primary" onclick="centrarMapa()">
                <i class="fas fa-arrows-alt me-1"></i> Centrar
            </button>
            <button class="btn btn-primary" onclick="actualizarUbicaciones()">
                <i class="fas fa-sync me-1"></i> Actualizar
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Mapa -->
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div id="mapaAdmin" style="height: 650px; border-radius: 12px; overflow: hidden;"></div>
                </div>
            </div>
        </div>

        <!-- Panel lateral -->
        <div class="col-lg-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Vehículos
                        <span class="badge bg-primary float-end" id="total-vehiculos">0</span>
                    </h6>
                </div>
                <div class="card-body p-0 sidebar-vehiculos" id="lista-vehiculos">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
                        <p>Cargando vehículos...</p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-muted small">En Ruta</div>
                            <h5 class="text-primary" id="stats-en-ruta">0</h5>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Disponibles</div>
                            <h5 class="text-success" id="stats-disponibles">0</h5>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Inactivos</div>
                            <h5 class="text-danger" id="stats-inactivos">0</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leyenda -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Leyenda</h6>
                    <div class="d-flex align-items-center mb-1">
                        <span class="estado-indicador estado-activo"></span>
                        <span class="small">Vehículo en ruta</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <span class="estado-indicador estado-pendiente"></span>
                        <span class="small">Vehículo disponible</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="estado-indicador estado-inactivo"></span>
                        <span class="small">Vehículo inactivo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let mapAdmin;
    let markers = {};
    let selectedMarker = null;
    let updateInterval;
    let segundos = 0;

    // Iconos personalizados por estado
    function getVehicleIcon(estado, color = '#0d6efd') {
        const colors = {
            'en_progreso': '#28a745',
            'confirmado': '#ffc107',
            'pendiente': '#ffc107',
            'default': '#0d6efd'
        };
        
        const iconColor = colors[estado] || colors.default;
        
        return L.divIcon({
            className: 'vehicle-marker',
            html: `
                <div class="vehicle-icon" style="color: ${iconColor}; position: relative;">
                    <i class="fas fa-truck"></i>
                    <div class="badge-placa" style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); font-size: 0.5rem; background: rgba(0,0,0,0.8); color: white; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">
                        ●
                    </div>
                </div>
            `,
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -15]
        });
    }

    // Inicializar mapa
    function initMap() {
        mapAdmin = L.map('mapaAdmin').setView([-16.5, -68.13], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(mapAdmin);

        // Evento para cerrar popups al hacer clic en el mapa
        mapAdmin.on('click', function() {
            if (selectedMarker) {
                selectedMarker.closePopup();
                selectedMarker = null;
            }
        });
    }

    // Centrar mapa en todos los vehículos
    function centrarMapa() {
        const markersList = Object.values(markers);
        if (markersList.length > 0) {
            const group = L.featureGroup(markersList);
            mapAdmin.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // Actualizar ubicaciones desde API
    function actualizarUbicaciones() {
        fetch('{{ route("api.gps.vehiculos") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    actualizarMapa(data.data);
                    actualizarLista(data.data);
                    actualizarEstadisticas(data.data);
                    document.getElementById('total-activos').textContent = data.total;
                } else {
                    console.error('Error:', data.error);
                    mostrarError(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al cargar ubicaciones');
            });
    }

    // Actualizar mapa
    function actualizarMapa(vehiculos) {
        // Limpiar marcadores anteriores
        Object.keys(markers).forEach(key => {
            if (markers[key]) {
                mapAdmin.removeLayer(markers[key]);
            }
        });
        markers = {};

        vehiculos.forEach(vehiculo => {
            const estado = vehiculo.servicio?.estado || 'pendiente';
            const icon = getVehicleIcon(estado);
            
            const marker = L.marker([vehiculo.lat, vehiculo.lng], { icon })
                .addTo(mapAdmin)
                .bindPopup(`
                    <div style="min-width: 250px;">
                        <h6 class="mb-2">
                            <i class="fas fa-truck text-primary me-2"></i>
                            ${vehiculo.vehiculo ? vehiculo.vehiculo.placa : 'Sin vehículo'}
                        </h6>
                        ${vehiculo.vehiculo ? `
                            <p class="small mb-1">
                                <i class="fas fa-car me-1"></i>
                                ${vehiculo.vehiculo.modelo} ${vehiculo.vehiculo.color ? '(' + vehiculo.vehiculo.color + ')' : ''}
                            </p>
                        ` : ''}
                        ${vehiculo.chofer ? `
                            <p class="small mb-1">
                                <i class="fas fa-user me-1"></i>
                                ${vehiculo.chofer.nombre}
                                <br><i class="fas fa-phone me-1"></i>
                                ${vehiculo.chofer.telefono}
                            </p>
                        ` : ''}
                        ${vehiculo.servicio ? `
                            <hr class="my-1">
                            <p class="small mb-1">
                                <strong>Servicio #${vehiculo.servicio.id}</strong><br>
                                <i class="fas fa-user me-1"></i> ${vehiculo.servicio.cliente}<br>
                                <i class="fas fa-flag-checkered me-1"></i> ${vehiculo.servicio.destino}
                            </p>
                            <span class="badge bg-${vehiculo.servicio.estado === 'en_progreso' ? 'success' : 'warning'}">
                                ${vehiculo.servicio.estado}
                            </span>
                        ` : `
                            <span class="badge bg-secondary">Disponible</span>
                        `}
                        <hr class="my-1">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            ${vehiculo.actualizado ? new Date(vehiculo.actualizado).toLocaleString() : 'Sin datos'}
                            <br>
                            <i class="fab fa-${vehiculo.plataforma} me-1"></i>
                            ${vehiculo.plataforma}
                        </small>
                    </div>
                `);

            // Guardar referencia
            markers[vehiculo.id] = marker;
        });
    }

    // Actualizar lista de vehículos
    function actualizarLista(vehiculos) {
        const lista = document.getElementById('lista-vehiculos');
        document.getElementById('total-vehiculos').textContent = vehiculos.length;

        if (vehiculos.length === 0) {
            lista.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-wifi-slash fa-2x mb-2 d-block"></i>
                    <p>No hay vehículos activos</p>
                    <small>Esperando conexión de dispositivos...</small>
                </div>
            `;
            return;
        }

        let html = '';
        vehiculos.forEach(vehiculo => {
            const estado = vehiculo.servicio?.estado || 'pendiente';
            const estadoClass = estado === 'en_progreso' ? 'activo' : (estado === 'confirmado' ? 'pendiente' : 'inactivo');
            const estadoTexto = estado === 'en_progreso' ? 'En ruta' : (estado === 'confirmado' ? 'En espera' : 'Disponible');
            
            html += `
                <div class="vehiculo-item p-3 border-bottom" onclick="centrarEnVehiculo('${vehiculo.id}')">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center">
                                <span class="estado-indicador estado-${estadoClass}"></span>
                                <strong>${vehiculo.vehiculo ? vehiculo.vehiculo.placa : 'Sin placa'}</strong>
                            </div>
                            ${vehiculo.chofer ? `
                                <div class="small text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    ${vehiculo.chofer.nombre}
                                </div>
                            ` : ''}
                            ${vehiculo.servicio ? `
                                <div class="small text-muted">
                                    <i class="fas fa-flag-checkered me-1"></i>
                                    ${vehiculo.servicio.destino}
                                </div>
                            ` : ''}
                        </div>
                        <div>
                            <span class="badge bg-${estado === 'en_progreso' ? 'success' : (estado === 'confirmado' ? 'warning' : 'secondary')}">
                                ${estadoTexto}
                            </span>
                            <div class="small text-muted">
                                <i class="fab fa-${vehiculo.plataforma}"></i>
                            </div>
                        </div>
                    </div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-map-pin me-1"></i>
                        ${vehiculo.lat.toFixed(6)}, ${vehiculo.lng.toFixed(6)}
                    </div>
                </div>
            `;
        });

        lista.innerHTML = html;
    }

    // Actualizar estadísticas
    function actualizarEstadisticas(vehiculos) {
        let enRuta = 0, disponibles = 0, inactivos = 0;
        
        vehiculos.forEach(v => {
            if (v.servicio?.estado === 'en_progreso') enRuta++;
            else if (v.servicio?.estado === 'confirmado') disponibles++;
            else inactivos++;
        });

        document.getElementById('stats-en-ruta').textContent = enRuta;
        document.getElementById('stats-disponibles').textContent = disponibles;
        document.getElementById('stats-inactivos').textContent = inactivos;
    }

    // Centrar en un vehículo específico
    function centrarEnVehiculo(id) {
        if (markers[id]) {
            markers[id].openPopup();
            mapAdmin.flyTo(markers[id].getLatLng(), 16);
            
            // Resaltar en la lista
            document.querySelectorAll('.vehiculo-item').forEach(el => el.classList.remove('active'));
            const items = document.querySelectorAll('.vehiculo-item');
            // Buscar el elemento correspondiente (por índice)
            const vehiculos = Object.keys(markers);
            const index = vehiculos.indexOf(id);
            if (index >= 0 && items[index]) {
                items[index].classList.add('active');
            }
        }
    }

    // Mostrar error
    function mostrarError(mensaje) {
        const lista = document.getElementById('lista-vehiculos');
        lista.innerHTML = `
            <div class="alert alert-danger m-2">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${mensaje}
            </div>
        `;
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        actualizarUbicaciones();

        // Actualizar cada 10 segundos
        updateInterval = setInterval(() => {
            actualizarUbicaciones();
            segundos = 0;
        }, 10000);

        // Actualizar contador de segundos
        setInterval(() => {
            segundos++;
            document.getElementById('intervalo-segundos').textContent = 10 - (segundos % 10);
        }, 1000);
    });

    // Limpiar al salir
    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });
</script>
@endpush
@endsection