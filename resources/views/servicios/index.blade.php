@extends('layouts.app')

@section('title', 'Servicios')

@php
    $user = auth()->user();
    $puedeCrearEditar = $user->isAdmin() || $user->isRecepcionista();
    $puedeVerDinero   = $user->isAdmin() || $user->isRecepcionista();
    $puedeCambiarEstado = $user->isAdmin() || $user->isRecepcionista() || $user->isChofer();
    $puedeConfirmarPago = $user->isAdmin() || $user->isRecepcionista() || $user->isChofer();

    $hayFiltros = request()->filled('search')
        || request()->filled('estado')
        || request()->filled('estado_pago')
        || request()->filled('fecha_inicio')
        || request()->filled('fecha_fin');
@endphp

@push('styles')
<style>
    .pagination { margin-bottom: 0; }
    .pagination .page-link { padding: 0.15rem 0.6rem; font-size: 0.8rem; }
    .filtros-card .form-label { font-size: 0.75rem; margin-bottom: 3px; color: #6c757d; font-weight: 600; }
    .filtros-card .form-control,
    .filtros-card .form-select { font-size: 0.875rem; }
    .estado-option {
        cursor: pointer;
        padding: 16px;
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.2s;
        text-align: center;
        background: #fff;
        position: relative;
        overflow: hidden;
    }
    .estado-option:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.15);
    }
    .estado-option.selected {
        border-color: #0d6efd !important;
        border-width: 3px !important;
        background: #dbeafe !important;
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.35) !important;
        transform: translateY(-3px);
    }
    .estado-option.selected .check-badge {
        display: flex !important;
    }
    .estado-option .check-badge {
        display: none;
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #0d6efd;
        color: #fff;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.5);
    }
    .estado-option i {
        font-size: 1.8rem;
        margin-bottom: 6px;
        display: block;
    }
    .estado-option .estado-nombre {
        font-weight: 600;
        font-size: 0.85rem;
        color: #212529;
    }
    .estado-option.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
        background: #f8f9fa !important;
    }
    .estado-option[data-estado="pendiente"] i { color: #6c757d; }
    .estado-option[data-estado="confirmado"] i { color: #0d6efd; }
    .estado-option[data-estado="en_progreso"] i { color: #ffc107; }
    .estado-option[data-estado="finalizado"] i { color: #198754; }
    .estado-option[data-estado="cancelado"] i { color: #dc3545; }

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
        background: none;
        border: none;
        color: #fff;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.8;
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

    .whatsapp-icon-large {
        font-size: 4rem;
        color: #25D366;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Servicios</h1>
        @if($puedeCrearEditar)
            <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuevo Servicio
            </a>
        @endif
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="card shadow-sm mb-4 filtros-card">
        <div class="card-body">
            <form method="GET" action="{{ route('servicios.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Cliente, teléfono, origen, destino, #ID"
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Estado Servicio</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Servicio::ESTADOS_LABEL as $key => $label)
                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($puedeVerDinero)
                <div class="col-md-2">
                    <label class="form-label">Estado Pago</label>
                    <select name="estado_pago" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado_pago') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado"    {{ request('estado_pago') == 'pagado'    ? 'selected' : '' }}>Pagado</option>
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ request('fecha_inicio') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ request('fecha_fin') }}">
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1" title="Aplicar filtros">
                        <i class="fas fa-search"></i>
                    </button>
                    @if($hayFiltros)
                        <a href="{{ route('servicios.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>

            @if($hayFiltros)
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted">
                        <i class="fas fa-filter me-1"></i>
                        <strong>Filtros activos:</strong>
                        @if(request('search'))
                            Búsqueda: "{{ request('search') }}" ·
                        @endif
                        @if(request('estado'))
                            Estado: {{ \App\Models\Servicio::ESTADOS_LABEL[request('estado')] ?? request('estado') }} ·
                        @endif
                        @if(request('estado_pago'))
                            Pago: {{ ucfirst(request('estado_pago')) }} ·
                        @endif
                        @if(request('fecha_inicio'))
                            Desde: {{ \Carbon\Carbon::parse(request('fecha_inicio'))->format('d/m/Y') }} ·
                        @endif
                        @if(request('fecha_fin'))
                            Hasta: {{ \Carbon\Carbon::parse(request('fecha_fin'))->format('d/m/Y') }} ·
                        @endif
                        <a href="{{ route('servicios.index') }}" class="ms-2">Quitar todos</a>
                    </small>
                </div>
            @endif
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-md">
            <div class="card text-white bg-secondary">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Pendientes</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'pendiente')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-primary">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Confirmados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'confirmado')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-warning">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">En Progreso</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'en_progreso')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-success">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Finalizados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'finalizado')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-info">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Cancelados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'cancelado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($puedeVerDinero)
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Pendiente de Pago</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado_pago', 'pendiente')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Pagados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado_pago', 'pagado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Origen → Destino</th>
                            <th>Fecha</th>
                            @if($puedeVerDinero)<th>Costo</th>@endif
                            <th>Estado Servicio</th>
                            <th>Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $servicio)
                        <tr>
                            <td><strong>#{{ $servicio->id }}</strong></td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $servicio->cliente->nombre_completo }}
                            </td>
                            <td>{{ $servicio->origen }} → {{ $servicio->destino }}</td>
                            <td>{{ $servicio->fecha_servicio->format('d/m/Y') }}</td>

                            @if($puedeVerDinero)
                                <td><strong>{{ number_format($servicio->costo_total, 2) }} Bs</strong></td>
                            @endif

                            <td>
                                @php
                                    $badgeClass = [
                                        'pendiente'   => 'secondary',
                                        'confirmado'  => 'primary',
                                        'en_progreso' => 'warning',
                                        'finalizado'  => 'success',
                                        'cancelado'   => 'danger',
                                    ][$servicio->estado] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ $servicio->estado_label }}
                                </span>
                            </td>

                            <td>
                                @if($servicio->estado_pago === 'pagado')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Pagado
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-clock me-1"></i> Pendiente
                                    </span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($puedeCambiarEstado && $servicio->estado !== 'finalizado' && $servicio->estado !== 'cancelado')
                                    <button class="btn btn-sm btn-warning"
                                            onclick="abrirModalEstado({{ $servicio->id }}, '{{ $servicio->estado }}', '{{ $servicio->estado_label }}')"
                                            title="Cambiar estado">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                @endif

                                <a href="{{ route('servicios.comprobante', $servicio) }}"
                                   class="btn btn-sm btn-danger" target="_blank" title="Ver PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>

                                @if($puedeCrearEditar)
                                    <button class="btn btn-sm btn-success"
                                            title="Enviar por WhatsApp"
                                            onclick="abrirModalWhatsApp({{ $servicio->id }}, '{{ $servicio->cliente->nombre_completo }}')"
                                            id="btn-whatsapp-{{ $servicio->id }}">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $puedeVerDinero ? 8 : 7 }}" class="text-center text-muted py-4">
                                <i class="fas fa-{{ $hayFiltros ? 'search' : 'truck' }} fa-3x d-block mb-2 opacity-50"></i>
                                <p class="mb-0">
                                    @if($hayFiltros)
                                        No se encontraron servicios con esos filtros
                                        <br>
                                        <a href="{{ route('servicios.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-times me-1"></i> Limpiar filtros
                                        </a>
                                    @else
                                        No hay servicios registrados
                                    @endif
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Mostrando {{ $servicios->firstItem() ?? 0 }} - {{ $servicios->lastItem() ?? 0 }}
                    de {{ $servicios->total() }}
                    @if($hayFiltros)(filtrado)@endif
                </span>
                <div>
                    {{ $servicios->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCambiarEstado" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-warning">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-exchange-alt me-2"></i>Cambiar Estado del Servicio
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2">
                    Servicio <strong id="estadoServicioId">#--</strong> · Estado actual:
                    <span class="badge bg-secondary" id="estadoActualBadge">--</span>
                </p>
                <p class="mb-3"><strong>Selecciona el nuevo estado:</strong></p>

                <div class="row g-3" id="opcionesEstados">
                    <div class="col-md-4 col-6">
                        <div class="estado-option" data-estado="pendiente" onclick="seleccionarEstado('pendiente')">
                            <span class="check-badge"><i class="fas fa-check"></i></span>
                            <i class="fas fa-hourglass-start"></i>
                            <div class="estado-nombre">Pendiente</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="estado-option" data-estado="confirmado" onclick="seleccionarEstado('confirmado')">
                            <span class="check-badge"><i class="fas fa-check"></i></span>
                            <i class="fas fa-check"></i>
                            <div class="estado-nombre">Confirmado</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="estado-option" data-estado="en_progreso" onclick="seleccionarEstado('en_progreso')">
                            <span class="check-badge"><i class="fas fa-check"></i></span>
                            <i class="fas fa-truck-moving"></i>
                            <div class="estado-nombre">En Progreso</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="estado-option" data-estado="finalizado" onclick="seleccionarEstado('finalizado')">
                            <span class="check-badge"><i class="fas fa-check"></i></span>
                            <i class="fas fa-flag-checkered"></i>
                            <div class="estado-nombre">Finalizado</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="estado-option" data-estado="cancelado" onclick="seleccionarEstado('cancelado')">
                            <span class="check-badge"><i class="fas fa-check"></i></span>
                            <i class="fas fa-times-circle"></i>
                            <div class="estado-nombre">Cancelado</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="notaEstadoExtra">Solo se permiten transiciones válidas según el flujo del servicio.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnGuardarEstado" onclick="guardarEstado()" disabled>
                    <i class="fas fa-save me-1"></i> Cambiar Estado
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalWhatsApp" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: #25D366; color: white;">
                <h6 class="modal-title">
                    <i class="fab fa-whatsapp me-2"></i>Enviar Comprobante por WhatsApp
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fab fa-whatsapp whatsapp-icon-large mb-3"></i>
                <p class="mb-2">¿Enviar el comprobante por WhatsApp al cliente?</p>
                <div class="alert alert-light border mb-0">
                    <strong id="nombreClienteWhatsApp">--</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn text-white" id="btnEnviarWhatsApp" style="background: #25D366;" onclick="confirmarEnviarWhatsApp()">
                    <i class="fab fa-whatsapp me-1"></i> Sí, enviar
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
let estadoSeleccionado = null;
let estadoServicioId = null;
let estadoActual = null;

function abrirModalEstado(servicioId, estadoActualStr, estadoLabelActual) {
    estadoServicioId = servicioId;
    estadoActual = estadoActualStr;
    estadoSeleccionado = null;

    document.getElementById('estadoServicioId').textContent = '#' + servicioId;
    document.getElementById('estadoActualBadge').textContent = estadoLabelActual;

    document.querySelectorAll('.estado-option').forEach(el => {
        el.classList.remove('selected');
        el.classList.remove('disabled');
    });

    const opcionActual = document.querySelector(`.estado-option[data-estado="${estadoActualStr}"]`);
    if (opcionActual) {
        opcionActual.classList.add('disabled');
    }

    document.getElementById('btnGuardarEstado').disabled = true;

    new bootstrap.Modal(document.getElementById('modalCambiarEstado')).show();
}

function seleccionarEstado(estado) {
    if (estado === estadoActual) return;

    estadoSeleccionado = estado;

    document.querySelectorAll('.estado-option').forEach(el => {
        el.classList.remove('selected');
    });

    const opcion = document.querySelector(`.estado-option[data-estado="${estado}"]`);
    if (opcion) opcion.classList.add('selected');

    document.getElementById('btnGuardarEstado').disabled = false;
}

function guardarEstado() {
    if (!estadoSeleccionado) return;

    const btn = document.getElementById('btnGuardarEstado');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';

    fetch(`/servicios/${estadoServicioId}/estado`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ estado: estadoSeleccionado })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCambiarEstado')).hide();
            mostrarToast('success', data.message || 'Estado actualizado correctamente');
            setTimeout(() => location.reload(), 1200);
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            mostrarToast('error', data.message || 'Error al cambiar estado');
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        mostrarToast('error', 'Error de conexión');
    });
}
let servicioWhatsAppId = null;

function abrirModalWhatsApp(servicioId, nombreCliente) {
    servicioWhatsAppId = servicioId;

    document.getElementById('nombreClienteWhatsApp').textContent = nombreCliente;

    new bootstrap.Modal(document.getElementById('modalWhatsApp')).show();
}

function confirmarEnviarWhatsApp() {
    if (!servicioWhatsAppId) return;

    const btn = document.getElementById('btnEnviarWhatsApp');
    const originalHTML = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';

    fetch(`/servicios/${servicioWhatsAppId}/enviar-whatsapp`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalWhatsApp')).hide();
        btn.disabled = false;
        btn.innerHTML = originalHTML;

        if (data.success) {
            mostrarToast('success', data.message || 'Comprobante enviado por WhatsApp');
        } else {
            mostrarToast('error', data.message || 'Error al enviar');
        }
    })
    .catch(err => {
        console.error(err);
        bootstrap.Modal.getInstance(document.getElementById('modalWhatsApp')).hide();
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        mostrarToast('error', 'Error de conexión');
    });
}
</script>
@endpush