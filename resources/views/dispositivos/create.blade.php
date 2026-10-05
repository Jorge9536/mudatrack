@extends('layouts.app')

@section('title', 'Asignar Dispositivo')

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

@push('scripts')
<script>
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
                                        onclick="event.stopPropagation(); eliminarDispositivoFirebase('${item.dispositivoId}')">
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
}

function eliminarDispositivoFirebase(dispositivoId) {
    if (!confirm(`⚠️ ¿Eliminar el dispositivo "${dispositivoId}" de Firebase?\n\nEsta acción es PERMANENTE y no se puede deshacer.`)) {
        return;
    }
    
    fetch(`/dispositivos/firestore/${dispositivoId}/eliminar`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarDispositivos();
            mostrarNotificacion('success', '✅ ' + data.message);
        } else {
            mostrarNotificacion('error', '❌ ' + (data.message || 'Error al eliminar'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('error', '❌ Error de conexión');
    });
}

function mostrarNotificacion(tipo, mensaje) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    const bgColor = tipo === 'success' ? '#198754' : '#dc3545';
    const icono = tipo === 'success' ? 'check-circle' : 'exclamation-circle';
    
    toast.style.cssText = `
        background: ${bgColor};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 300px;
        animation: slideIn 0.3s ease;
        font-family: sans-serif;
    `;
    
    toast.innerHTML = `
        <i class="fas fa-${icono}" style="font-size: 20px;"></i>
        <span style="flex: 1;">${mensaje}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; cursor: pointer; font-size: 18px;">&times;</button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
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

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', cargarDispositivos);
</script>
@endpush
@endsection