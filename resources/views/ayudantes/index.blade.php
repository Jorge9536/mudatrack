@extends('layouts.app')

@section('title', 'Ayudantes')

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
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-user-friends me-2 text-primary"></i>Gestión de Ayudantes</h1>
            <small class="text-muted">Personal de apoyo para mudanzas</small>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('ayudantes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Ayudante
        </a>
        @endif
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Total Ayudantes</p>
                    <h5 class="mb-0">{{ $ayudantes->total() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Disponibles</p>
                    <h5 class="mb-0">{{ $ayudantes->where('disponible', true)->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Ocupados</p>
                    <h5 class="mb-0">{{ $ayudantes->where('disponible', false)->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Disponibilidad</p>
                    <h5 class="mb-0">
                        {{ $ayudantes->total() > 0 ? round(($ayudantes->where('disponible', true)->count() / $ayudantes->total()) * 100) : 0 }}%
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Lista de Ayudantes</h6>
            <span class="badge bg-primary">{{ $ayudantes->total() }} registros</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ayudantes as $ayudante)
                        <tr>
                            <td>#{{ $ayudante->id }}</td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $ayudante->nombre_completo }}
                            </td>
                            <td>{{ $ayudante->telefono }}</td>
                            <td>
                                @if($ayudante->disponible)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Ocupado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ayudantes.show', $ayudante) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('ayudantes.edit', $ayudante) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button class="btn btn-sm {{ $ayudante->disponible ? 'btn-danger' : 'btn-success' }}"
                                            title="{{ $ayudante->disponible ? 'Marcar como ocupado' : 'Marcar como disponible' }}"
                                            onclick="abrirModalDisponibilidad({{ $ayudante->id }}, '{{ $ayudante->nombre_completo }}', {{ $ayudante->disponible ? 'true' : 'false' }})">
                                        <i class="fas {{ $ayudante->disponible ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="abrirModalEliminar({{ $ayudante->id }}, '{{ $ayudante->nombre_completo }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="form-disponibilidad-{{ $ayudante->id }}"
                                          action="{{ route('ayudantes.toggle-disponibilidad', $ayudante) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                    </form>

                                    <form id="form-eliminar-{{ $ayudante->id }}"
                                          action="{{ route('ayudantes.destroy', $ayudante) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-user-friends fa-3x d-block mb-2"></i>
                                <p>No hay ayudantes registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $ayudantes->links() }}
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
                <i class="fas fa-user-circle modal-icon-large text-primary"></i>
                <p class="mb-2">¿Cambiar la disponibilidad del ayudante?</p>
                <div class="alert alert-light border mb-0">
                    <strong id="nombreAyudanteDisponibilidad">--</strong>
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


<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Eliminar Ayudante
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-triangle modal-icon-large text-danger"></i>
                <p class="mb-2">¿Estás seguro de que quieres eliminar este ayudante?</p>
                <div class="alert alert-light border mb-3">
                    <strong id="nombreAyudanteEliminar">--</strong>
                </div>
                <div class="alert alert-warning mb-0 small text-start">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta acción <strong>no se puede deshacer</strong>. El ayudante será eliminado permanentemente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminar()">
                    <i class="fas fa-trash me-1"></i> Sí, eliminar
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

let ayudanteDisponibilidadId = null;
let disponibleActual = false;

function abrirModalDisponibilidad(id, nombre, disponible) {
    ayudanteDisponibilidadId = id;
    disponibleActual = disponible;

    document.getElementById('nombreAyudanteDisponibilidad').textContent = nombre;
    document.getElementById('estadoActualDisponibilidad').textContent = disponible ? 'Disponible' : 'Ocupado';


    const header = document.getElementById('headerDisponibilidad');
    const btn = document.getElementById('btnConfirmarDisponibilidad');
    const accion = disponible ? 'ocupado' : 'disponible';

    header.className = 'modal-header ' + (disponible ? 'bg-danger' : 'bg-success');
    btn.className = 'btn ' + (disponible ? 'btn-danger' : 'btn-success');
    btn.innerHTML = disponible
        ? '<i class="fas fa-pause me-1"></i> Sí, marcar como ocupado'
        : '<i class="fas fa-play me-1"></i> Sí, marcar como disponible';

    new bootstrap.Modal(document.getElementById('modalDisponibilidad')).show();
}

function confirmarDisponibilidad() {
    if (!ayudanteDisponibilidadId) return;

    document.getElementById('form-disponibilidad-' + ayudanteDisponibilidadId).submit();
}

let ayudanteEliminarId = null;

function abrirModalEliminar(id, nombre) {
    ayudanteEliminarId = id;
    document.getElementById('nombreAyudanteEliminar').textContent = nombre;

    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

function confirmarEliminar() {
    if (!ayudanteEliminarId) return;

    document.getElementById('form-eliminar-' + ayudanteEliminarId).submit();
}
</script>
@endpush