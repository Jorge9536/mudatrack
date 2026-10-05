<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de mi mudanza - MudaTrack</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        body {
            background: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
        }
        
        .header-public {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header-public h1 {
            font-size: 24px;
            margin: 0;
        }
        
        .header-public p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .live-badge {
            background: #10b981;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            animation: pulse 2s infinite;
            display: inline-block;
            margin-top: 10px;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        #mapa {
            height: 60vh;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            #mapa { height: 50vh; }
        }
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }
        
        .info-card h6 {
            color: #6c757d;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .info-card .value {
            font-size: 16px;
            font-weight: 600;
            color: #212529;
        }
        
        .footer-public {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 12px;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header-public">
        <h1><i class="fas fa-truck"></i> MudaTrack</h1>
        <p>Seguimiento en tiempo real de su mudanza</p>
        <div class="live-badge">
            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> EN VIVO
        </div>
    </div>

    {{-- CONTENIDO --}}
    <div class="container-fluid p-3">
        <div class="row">
            
            {{-- MAPA --}}
            <div class="col-lg-8">
                <div id="mapa"></div>
                
                <div class="info-card mt-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <h6>Última actualización</h6>
                            <div class="value" id="ultima-actualizacion" style="font-size: 14px;">Cargando...</div>
                        </div>
                        <div class="col-4">
                            <h6>Estado</h6>
                            <div class="value">
                                <span class="badge bg-{{ $servicio->estado == 'en_progreso' ? 'warning' : 'primary' }}" style="font-size: 12px;">
                                    {{ $servicio->estado_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-4">
                            <h6>Conexión</h6>
                            <div class="value" style="font-size: 14px;">
                                <span id="estado-gps" class="text-success">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Activo
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INFORMACIÓN --}}
            <div class="col-lg-4">
                
                {{-- DATOS DEL SERVICIO --}}
                <div class="info-card">
                    <h6><i class="fas fa-info-circle me-2"></i>Mi Servicio</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Cliente</small>
                        <div class="value">{{ $servicio->cliente->nombre_completo }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Origen</small>
                        <div class="value" style="font-size: 14px;">{{ $servicio->origen }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Destino</small>
                        <div class="value" style="font-size: 14px;">{{ $servicio->destino }}</div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Fecha</small>
                        <div class="value">{{ $servicio->fecha_servicio->format('d/m/Y') }}</div>
                    </div>
                </div>

                {{-- CHOFER --}}
                @if($servicio->chofer)
                <div class="info-card">
                    <h6><i class="fas fa-user-circle me-2"></i>Mi Chofer</h6>
                    <div class="d-flex align-items-center">
                        <div class="me-3" 
                             style="width:50px;height:50px;background:#0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:1.2rem;flex-shrink:0;">
                            {{ substr($servicio->chofer->nombre_completo, 0, 2) }}
                        </div>
                        <div>
                            <div class="value">{{ $servicio->chofer->nombre_completo }}</div>
                            <small class="text-muted">Lic. {{ $servicio->chofer->licencia }}</small>
                            <br>
                            <span class="badge bg-success mt-1" style="font-size: 10px;">
                                <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i> En ruta
                            </span>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="footer-public">
        <p class="mb-1">
            <strong>MudaTrack</strong> · Sistema de Seguimiento GPS
        </p>
        <p class="mb-0">
            Si tiene alguna consulta, comuníquese con nosotros.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        const token = '{{ $token }}';
        const servicio = @json($servicio);
        const ubicacionesLocal = @json($ubicaciones);
        
        let map;
        let vehicleMarker = null;
        let rutaLinea = null;
        let updateInterval;

        function initMap() {
            const center = [-16.5, -68.13];
            map = L.map('mapa').setView(center, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
        }

        function cargarUbicacion() {
            fetch(`/seguimiento/${token}/ubicacion`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        actualizarMapa(data.data);
                        actualizarInfo(data.data);
                    } else {
                        document.getElementById('ultima-actualizacion').textContent = 'Sin datos';
                        document.getElementById('estado-gps').innerHTML = 
                            '<i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Sin GPS';
                        document.getElementById('estado-gps').className = 'text-danger';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function actualizarMapa(ubicacion) {
            if (vehicleMarker) {
                map.removeLayer(vehicleMarker);
            }

            const vehicleIcon = L.divIcon({
                html: `
                    <div style="
                        background: #10b981;
                        border-radius: 50%;
                        width: 50px;
                        height: 50px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border: 4px solid white;
                        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.6);
                    ">
                        <i class="fas fa-truck" style="font-size: 24px; color: white;"></i>
                    </div>
                `,
                className: '',
                iconSize: [50, 50],
                iconAnchor: [25, 25]
            });

            vehicleMarker = L.marker([ubicacion.lat, ubicacion.lng], { 
                icon: vehicleIcon,
                zIndexOffset: 1000
            })
            .addTo(map)
            .bindPopup(`
                <div style="text-align: center;">
                    <strong>🚚 Su mudanza</strong><br>
                    <small>${ubicacion.actualizado ? new Date(ubicacion.actualizado).toLocaleString('es-BO') : 'Sin datos'}</small>
                </div>
            `);

            map.setView([ubicacion.lat, ubicacion.lng], 15);
        }

        function actualizarInfo(ubicacion) {
            if (ubicacion.actualizado) {
                document.getElementById('ultima-actualizacion').textContent = timeAgo(ubicacion.actualizado);
            }
            document.getElementById('estado-gps').innerHTML = 
                '<i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Activo';
            document.getElementById('estado-gps').className = 'text-success';
        }

        function timeAgo(fecha) {
            if (!fecha) return 'Nunca';
            const ahora = new Date();
            const fechaDate = new Date(fecha);
            const diff = Math.floor((ahora - fechaDate) / 1000);
            
            if (diff < 60) return 'Hace ' + diff + ' seg';
            if (diff < 3600) return 'Hace ' + Math.floor(diff / 60) + ' min';
            if (diff < 86400) return 'Hace ' + Math.floor(diff / 3600) + ' h';
            return 'Hace ' + Math.floor(diff / 86400) + ' días';
        }

        function mostrarRuta() {
            if (ubicacionesLocal.length < 2) return;
            const points = ubicacionesLocal.map(u => [u.latitud, u.longitud]);
            rutaLinea = L.polyline(points, {
                color: '#ffc107',
                weight: 4,
                opacity: 0.7,
                dashArray: '8, 8'
            }).addTo(map);
        }

        initMap();
        mostrarRuta();
        cargarUbicacion();

        updateInterval = setInterval(cargarUbicacion, 10000);
        
        window.addEventListener('beforeunload', function() {
            if (updateInterval) clearInterval(updateInterval);
        });
    </script>
</body>
</html>