@extends('layouts.app')

@section('title', 'Asignar Dispositivo')

@push('styles')
<style>
    
    .dispositivo-item {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        border-radius: 8px !important;
        margin-bottom: 6px;
    }
    .dispositivo-item:hover {
        background: #f5f1e8;
        border-left-color: #b87333;
    }
    .dispositivo-item.active,
    .dispositivo-item.list-group-item-primary {
        background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%) !important;
        color: #fff !important;
        border-left-color: #b87333 !important;
        box-shadow: 0 4px 12px rgba(30, 58, 95, 0.25);
    }
    .dispositivo-item.active *,
    .dispositivo-item.list-group-item-primary * {
        color: #fff !important;
    }
    .dispositivo-item.active .badge,
    .dispositivo-item.list-group-item-primary .badge {
        color: #fff !important;
    }

    .toast-container-custom {
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

    .modal-icon-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 15px;
    }
    .modal-icon-large.bg-danger-soft {
        background: #fee2e2;
        color: #dc2626;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dispositivos.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Asignar Dispositivo</h1>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('dispositivos.store') }}" method="POST" id="formDispositivo">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label required">ID del Dispositivo</label>
                            <input type="text"
                                   name="dispositivo_id"
                                   id="dispositivo_id"
                                   class="form-control @error('dispositivo_id') is-invalid @enderror"
                                   placeholder="Haz clic en un dispositivo de la lista →"
                                   value="{{ old('dispositivo_id') }}"
                                   readonly
                                   required>
                            <small class="text-muted">Selecciona un dispositivo de la lista de Firebase</small>
                            @error('dispositivo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Chofer</label>
                            <select name="chofer_id" class="form-select @error('chofer_id') is-invalid @enderror" required>
                                <option value="">Seleccione un chofer...</option>
                                @foreach($choferes as $chofer)
                                    <option value="{{ $chofer->id }}" {{ old('chofer_id') == $chofer->id ? 'selected' : '' }}>
                                        {{ $chofer->nombre_completo }} - Lic. {{ $chofer->licencia }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chofer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vehículo (Opcional)</label>
                            <select name="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror">
                                <option value="">Sin vehículo asignado</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 border-top pt-3">
                            <a href="{{ route('dispositivos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                                <i class="fas fa-save me-1"></i> Asignar Dispositivo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Dispositivos disponibles en Firebase
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="cargarDispositivos()">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
                <div class="card-body">
                    <div id="dispositivosFirebase">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 text-muted">Cargando dispositivos...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container-custom" id="toastContainer"></div>

<div class="modal fade" id="modalEliminarDispositivo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Eliminar Dispositivo
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="modal-icon-large bg-danger-soft">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="mb-2">¿Estás seguro de que quieres eliminar este dispositivo de Firebase?</p>
                <div class="alert alert-light border mb-3">
                    <i class="fas fa-satellite-dish me-2 text-muted"></i>
                    <strong id="nombreDispositivoEliminar">--</strong>
                </div>
                <div class="alert alert-warning mb-0 small text-start">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta acción es <strong>PERMANENTE</strong> y no se puede deshacer. El dispositivo dejará de enviar ubicaciones al sistema.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar" onclick="confirmarEliminar()">
                    <i class="fas fa-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mostrarNotificacion(tipo, mensaje) {
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

function cargarDispositivos() {
    const container = document.getElementById('dispositivosFirebase');

    container.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <span class="ms-2 text-muted">Cargando dispositivos...</span>
        </div>
    `;

    fetch('{{ route("dispositivos.firestore.lista") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                let html = '<div class="list-group">';
                data.data.forEach(item => {
                    const hace = item.actualizado ? timeAgo(item.actualizado) : 'Nunca';

                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center dispositivo-item"
                             data-id="${item.dispositivoId}">
                            <div class="d-flex align-items-center flex-grow-1"
                                 style="cursor: pointer;"
                                 onclick="seleccionarDispositivo('${item.dispositivoId}')">
                                <i class="fas fa-satellite-dish text-primary me-2"></i>
                                <div>
                                    <strong>${item.dispositivoId}</strong>
                                    <span class="badge bg-secondary ms-2">${item.plataforma || 'N/A'}</span>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> ${hace}
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Activo
                                </span>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        title="Eliminar de Firebase"
                                        onclick="event.stopPropagation(); abrirModalEliminar('${item.dispositivoId}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-satellite-dish fa-2x mb-2 d-block"></i>
                        No hay dispositivos activos en Firebase
                    </div>
                `;
            }
        })
        .catch(error => {
            container.innerHTML = `
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No se pudieron cargar los dispositivos: ${error.message}
                </div>
            `;
        });
}

function seleccionarDispositivo(dispositivoId) {
    document.getElementById('dispositivo_id').value = dispositivoId;
    document.getElementById('btnSubmit').disabled = false;

    document.querySelectorAll('.dispositivo-item').forEach(el => {
        el.classList.remove('active', 'list-group-item-primary');
    });

    const selected = document.querySelector(`.dispositivo-item[data-id="${dispositivoId}"]`);
    if (selected) {
        selected.classList.add('active', 'list-group-item-primary');
    }

    const input = document.getElementById('dispositivo_id');
    input.classList.add('is-valid');
    setTimeout(() => input.classList.remove('is-valid'), 1000);

    mostrarNotificacion('info', 'Dispositivo "' + dispositivoId + '" seleccionado');
}

let dispositivoAEliminar = null;

function abrirModalEliminar(dispositivoId) {
    dispositivoAEliminar = dispositivoId;
    document.getElementById('nombreDispositivoEliminar').textContent = dispositivoId;

    const btn = document.getElementById('btnConfirmarEliminar');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-trash me-1"></i> Sí, eliminar';

    new bootstrap.Modal(document.getElementById('modalEliminarDispositivo')).show();
}

function confirmarEliminar() {
    if (!dispositivoAEliminar) return;

    const btn = document.getElementById('btnConfirmarEliminar');
    const originalHTML = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Eliminando...';

    fetch(`/dispositivos/firestore/${dispositivoAEliminar}/eliminar`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalEliminarDispositivo')).hide();

        if (data.success) {
            mostrarNotificacion('success', 'Dispositivo eliminado correctamente');
            cargarDispositivos();
        } else {
            mostrarNotificacion('error', data.message || 'Error al eliminar');
        }

        btn.disabled = false;
        btn.innerHTML = originalHTML;
    })
    .catch(error => {
        console.error('Error:', error);
        bootstrap.Modal.getInstance(document.getElementById('modalEliminarDispositivo')).hide();
        mostrarNotificacion('error', 'Error de conexión');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}

function timeAgo(fecha) {
    if (!fecha) return 'Nunca';
    const ahora = new Date();
    const fechaDate = new Date(fecha);
    const diff = Math.floor((ahora - fechaDate) / 1000);

    if (diff < 60) return 'Hace unos segundos';
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
    if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
    return `Hace ${Math.floor(diff / 86400)} días`;
}

document.addEventListener('DOMContentLoaded', cargarDispositivos);
</script>
@endpush
@endsection