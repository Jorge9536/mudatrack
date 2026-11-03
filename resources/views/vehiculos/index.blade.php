@extends('layouts.app')

@section('title', 'Vehículos')

@push('styles')
<style>
    .pagination { margin-bottom: 0; }
    .pagination .page-link { padding: 0.25rem 0.6rem; font-size: 0.85rem; }
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
    .toast-custom.toast-info    { background: linear-gradient(135deg, #0dcaf0, #3b82f6); }
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
        font-size: 4rem;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-truck me-2 text-primary"></i>Gestión de Vehículos</h1>
        <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Vehículo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Placa</th>
                            <th>Marca/Modelo</th>
                            <th>Tipo</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiculos as $vehiculo)
                        <tr>
                            <td><strong>{{ $vehiculo->placa }}</strong></td>
                            <td>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                            <td><span class="badge bg-secondary">{{ $vehiculo->tipo }}</span></td>
                            <td>{{ $vehiculo->capacidad_kg }} kg</td>
                            <td>
                                @if($vehiculo->disponible)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Ocupado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button class="btn btn-sm {{ $vehiculo->disponible ? 'btn-danger' : 'btn-success' }}"
                                        title="{{ $vehiculo->disponible ? 'Marcar como ocupado' : 'Marcar como disponible' }}"
                                        onclick="abrirModalDisponibilidad({{ $vehiculo->id }}, '{{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}', {{ $vehiculo->disponible ? 'true' : 'false' }})">
                                    <i class="fas {{ $vehiculo->disponible ? 'fa-pause' : 'fa-play' }}"></i>
                                </button>

                                <form id="form-disponibilidad-{{ $vehiculo->id }}"
                                      action="{{ route('vehiculos.toggle-disponibilidad', $vehiculo) }}"
                                      method="POST" class="d-none">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-truck fa-3x d-block mb-2 opacity-50"></i>
                                <p class="mb-0">No hay vehículos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $vehiculos->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="modalDisponibilidad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" id="headerDisponibilidad">
                <h6 class="modal-title text-white">
                    <i class="fas fa-power-off me-2"></i>Cambiar Disponibilidad
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-truck modal-icon-large text-primary"></i>
                <p class="mb-2">¿Cambiar la disponibilidad del vehículo?</p>
                <div class="alert alert-light border mb-0">
                    <strong id="nombreVehiculoDisponibilidad">--</strong>
                    <br>
                    <small class="text-muted">
                        Estado actual:
                        <span id="estadoActualDisponibilidad" class="fw-bold">--</span>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn" id="btnConfirmarDisponibilidad" onclick="confirmarDisponibilidad()">
                    <i class="fas fa-check me-1"></i> Sí, cambiar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

@endsection

@push('scripts')
<script>
function mostrarToast(tipo, mensaje) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast-custom toast-' + tipo;

    const iconos = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'info': 'fa-info-circle'
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
let vehiculoDisponibilidadId = null;

function abrirModalDisponibilidad(id, nombre, disponible) {
    vehiculoDisponibilidadId = id;

    document.getElementById('nombreVehiculoDisponibilidad').textContent = nombre;
    document.getElementById('estadoActualDisponibilidad').textContent = disponible ? 'Disponible' : 'Ocupado';

    const header = document.getElementById('headerDisponibilidad');
    const btn = document.getElementById('btnConfirmarDisponibilidad');

    header.className = 'modal-header ' + (disponible ? 'bg-danger' : 'bg-success');
    btn.className = 'btn ' + (disponible ? 'btn-danger' : 'btn-success');
    btn.innerHTML = disponible
        ? '<i class="fas fa-pause me-1"></i> Sí, marcar como ocupado'
        : '<i class="fas fa-play me-1"></i> Sí, marcar como disponible';

    new bootstrap.Modal(document.getElementById('modalDisponibilidad')).show();
}

function confirmarDisponibilidad() {
    if (!vehiculoDisponibilidadId) return;

    document.getElementById('form-disponibilidad-' + vehiculoDisponibilidadId).submit();
}
</script>
@endpush