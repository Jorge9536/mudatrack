@extends('layouts.app')

@section('title', 'Seguimiento GPS - ' . $servicio->cliente->nombre_completo)

@push('styles')
<style>
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    .toast-custom {
        min-width: 300px;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
        font-family: 'Segoe UI', sans-serif;
        animation: slideInToast 0.3s ease;
        margin-bottom: 10px;
    }
    .toast-custom.toast-success { background: linear-gradient(135deg, #198754, #20c997); }
    .toast-custom.toast-error   { background: linear-gradient(135deg, #dc3545, #f87171); }
    .toast-custom.toast-warning { background: linear-gradient(135deg, #b87333, #d4a574); }
    .toast-custom.toast-info    { background: linear-gradient(135deg, #1e3a5f, #2c5282); }
    .toast-custom i { font-size: 1.5rem; }
    .toast-custom .toast-text { flex: 1; font-size: 0.9rem; }
    .toast-custom .toast-close {
        background: none; border: none; color: #fff;
        font-size: 1.2rem; cursor: pointer; opacity: 0.8;
    }
    .toast-custom .toast-close:hover { opacity: 1; }

    @keyframes slideInToast {
        from { transform: translateX(120%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
    @keyframes slideOutToast {
        from { transform: translateX(0);    opacity: 1; }
        to   { transform: translateX(120%); opacity: 0; }
    }
    .url-modal-box {
        background: #f5f1e8;
        border: 1px solid #e5e0d5;
        border-radius: 8px;
        padding: 12px 16px;
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        word-break: break-all;
        color: #1e3a5f;
        user-select: all;
    }
</style>
@endpush

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
            <button class="btn btn-success btn-sm" id="btn-enviar-seguimiento" onclick="enviarSeguimiento()">
                <i class="fab fa-whatsapp me-1"></i> Enviar seguimiento por WhatsApp
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="compartirWhatsApp()" title="Abrir WhatsApp con mensaje manual">
                <i class="fab fa-whatsapp me-1"></i> Manual
            </button>
            <button class="btn btn-outline-secondary btn-sm" onclick="copiarUrl(this)">
                <i class="fas fa-copy me-1"></i> Copiar enlace
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body p-0">
                    <div id="mapa" style="height: 500px; border-radius: 12px; overflow: hidden;"></div>
                </div>
            </div>
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
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3" style="border-left: 4px solid #1e3a5f;">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Estado del Servicio
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        <span class="badge bg-{{ $servicio->estado == 'en_progreso' ? 'warning' : 'primary' }} {{ $servicio->estado == 'en_progreso' ? 'text-white' : '' }}">
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-user-circle me-2 text-primary"></i>Chofer
                    </h6>
                    @if($servicio->chofer)
                    <div class="d-flex align-items-center">
                        <div class="avatar-placeholder me-3"
                             style="width:48px;height:48px;background:#1e3a5f;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:1.2rem;">
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

<div class="toast-container" id="toastContainer"></div>
<div class="modal fade" id="modalCopiarUrl" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header card-header-corporativo" style="background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); color: #fff;">
                <h6 class="modal-title">
                    <i class="fas fa-link me-2"></i>Copiar Enlace de Seguimiento
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Tu navegador no permite copiar automáticamente. Selecciona el enlace y cópialo manualmente:
                </p>
                <div class="url-modal-box" id="urlModalContent">--</div>
                <button class="btn btn-primary w-100 mt-3" onclick="seleccionarUrlModal()">
                    <i class="fas fa-hand-pointer me-1"></i> Seleccionar todo
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
function mostrarToast(tipo, mensaje) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast-custom toast-' + tipo;

    const iconos = {
        'success': 'fa-check-circle',
        'error':   'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info':    'fa-info-circle'
    };

    toast.innerHTML = `
        <i class="fas ${iconos[tipo] || iconos.info}"></i>
        <div class="toast-text">${mensaje}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutToast 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

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
                    background: #1e3a5f;
                    border-radius: 50%;
                    width: 45px;
                    height: 45px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 3px solid white;
                    box-shadow: 0 4px 15px rgba(30, 58, 95, 0.5);
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
            color: '#b87333',
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
    window.enviarSeguimiento = function() {
        if (!confirm('¿Enviar el link de seguimiento al cliente por WhatsApp?')) {
            return;
        }

        const btn = document.getElementById('btn-enviar-seguimiento');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';

        fetch('{{ route("servicios.enviar.seguimiento", $servicio) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                btn.innerHTML = '<i class="fas fa-check me-1"></i> ¡Enviado!';
                mostrarToast('success', 'Seguimiento enviado a ' + data.cliente_nombre || 'cliente');
                setTimeout(() => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-success');
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }, 3000);
            } else {
                mostrarToast('error', data.message || 'Error al enviar');
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            mostrarToast('error', 'Error al enviar: ' + err.message);
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        });
    };

    window.compartirWhatsApp = function() {
        const token = '{{ $servicio->token_seguimiento }}';
        const url = `${window.location.origin}/seguimiento/${token}`;
        const cliente = '{{ $servicio->cliente->nombre_completo }}';
        const telefono = '{{ preg_replace("/[^0-9]/", "", $servicio->cliente->telefono ?? "") }}';

        if (!telefono) {
            mostrarToast('warning', 'El cliente no tiene teléfono registrado');
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
    window.copiarUrl = function(btn) {
        const token = '{{ $servicio->token_seguimiento }}';
        const url = `${window.location.origin}/seguimiento/${token}`;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(() => {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-1"></i> ¡Copiado!';
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-success');

                mostrarToast('success', 'Enlace copiado al portapapeles');

                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(err => {
                console.warn('Clipboard API falló, usando fallback:', err);
                abrirModalCopiarUrl(url);
            });
        } else {
            abrirModalCopiarUrl(url);
        }
    };
    function abrirModalCopiarUrl(url) {
        document.getElementById('urlModalContent').textContent = url;
        const modal = new bootstrap.Modal(document.getElementById('modalCopiarUrl'));
        modal.show();
    }

    window.seleccionarUrlModal = function() {
        const urlBox = document.getElementById('urlModalContent');
        const range = document.createRange();
        range.selectNodeContents(urlBox);
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
        try {
            const url = urlBox.textContent;
            document.execCommand('copy');
            mostrarToast('success', 'Enlace copiado al portapapeles');
        } catch (err) {
            mostrarToast('info', 'Presiona Ctrl+C para copiar');
        }
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