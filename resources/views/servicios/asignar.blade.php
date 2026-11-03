@extends('layouts.app')

@section('title', 'Asignar Personal')

@push('styles')
<style>
    :root {
        --primary-color: #1e3a5f;
        --accent-bronze: #b87333;
        --bg-soft: #f5f1e8;
    }

    .calendario-mini { font-size: 0.8rem; }
    .calendario-mini table { width: 100%; border-collapse: collapse; }
    .calendario-mini th {
        background: #f5f1e8;
        padding: 6px 8px;
        text-align: center;
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #1e3a5f;
        border-bottom: 2px solid #e5e0d5;
    }
    .calendario-mini td {
        padding: 6px 8px;
        text-align: center;
        border: 1px solid #e5e0d5;
        font-size: 0.75rem;
        min-width: 30px;
    }

    .checkbox-actual {
        border: 2px solid #1e3a5f !important;
        box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.2) !important;
    }

    .card-header-corporativo {
        background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
        color: #ffffff !important;
        padding: 14px 22px;
        border-bottom: none;
    }

    .card-header-corporativo h6,
    .card-header-corporativo h5,
    .card-header-corporativo h4,
    .card-header-corporativo span,
    .card-header-corporativo i,
    .card-header-corporativo * {
        color: #ffffff !important;
    }

    .badge-actual {
        background: #1e3a5f;
        color: #fff;
    }

    .ayudante-card {
        background: #f8f6f1;
        border: 1px solid #e5e0d5 !important;
        border-radius: 8px;
        padding: 10px;
        transition: all 0.2s ease;
    }
    .ayudante-card:hover {
        background: #f5f1e8;
        border-color: #b87333 !important;
    }
    .ayudante-card.card-ocupado {
        background: #fef2f2;
        border-color: #fecaca !important;
    }
    .ayudante-card.card-actual {
        background: #eff6ff;
        border: 2px solid #1e3a5f !important;
    }

    #btnAsignar {
        background: linear-gradient(135deg, #1e3a5f, #2c5282);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    #btnAsignar:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(30, 58, 95, 0.3);
        filter: brightness(1.05);
    }

    .alert-no-ayudantes {
        background: #fef3c7;
        border-left: 4px solid #b87333;
        color: #78350f;
    }

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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('servicios.show', $servicio) }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-users me-2 text-primary"></i>Asignación de Personal</h1>
        <span class="badge bg-primary ms-2">Servicio #{{ $servicio->id }}</span>
        @if($servicio->chofer_id || $servicio->vehiculo_id || $servicio->ayudantes->count() > 0)
            <span class="badge bg-warning text-white ms-2">
                <i class="fas fa-edit me-1"></i> Modificando
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header card-header-corporativo">
                    <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Servicio #{{ $servicio->id }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cliente</span>
                        <strong>{{ $servicio->cliente->nombre_completo }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Origen</span>
                        <strong class="text-truncate" style="max-width:150px;">{{ $servicio->origen }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Destino</span>
                        <strong class="text-truncate" style="max-width:150px;">{{ $servicio->destino }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Fecha</span>
                        <strong>{{ $servicio->fecha_servicio->format('d/m/Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hora</span>
                        <strong>
                            @if($servicio->hora_inicio && $servicio->hora_fin)
                                {{ \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') }}
                            @else
                                No definida
                            @endif
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        <span class="badge bg-warning text-white">{{ $servicio->estado_label }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Costo Total</span>
                        <strong class="text-primary h5">{{ number_format($servicio->costo_total, 2) }} Bs</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Ayudantes requeridos</span>
                        <strong>
                            <span class="badge bg-info">{{ $servicio->cantidad_ayudantes }}</span>
                        </strong>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Disponibilidad en este horario
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="calendario-mini" id="miniCalendario">
                        <div class="text-center py-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="ms-2 text-muted">Cargando disponibilidad...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form action="{{ route('servicios.asignar', $servicio) }}" method="POST" id="formAsignar">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-truck me-2 text-primary"></i>Vehículo
                            <small class="text-muted">({{ $servicio->fecha_servicio->format('d/m/Y') }} {{ $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : 'N/A' }} - {{ $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : 'N/A' }})</small>
                        </h6>
                        <div class="row g-2 mb-3">
                            <select name="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror" required>
                                <option value="">Seleccione un vehículo...</option>
                                @foreach($vehiculos as $vehiculo)
                                    @php
                                        $disponible = $vehiculo->isAvailable(
                                            $servicio->fecha_servicio->format('Y-m-d'),
                                            $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : '00:00',
                                            $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : '23:59'
                                        );
                                        $esActual = ($servicio->vehiculo_id == $vehiculo->id);
                                    @endphp
                                    <option value="{{ $vehiculo->id }}"
                                        {{ old('vehiculo_id', $servicio->vehiculo_id) == $vehiculo->id ? 'selected' : '' }}
                                        {{ (!$disponible && !$esActual) ? 'disabled' : '' }}>
                                        {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                        @if($esActual)
                                            [Actual]
                                        @elseif($disponible)
                                            [Disponible]
                                        @else
                                            [Ocupado]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-user-circle me-2 text-primary"></i>Chofer
                            <small class="text-muted">({{ $servicio->fecha_servicio->format('d/m/Y') }} {{ $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : 'N/A' }} - {{ $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : 'N/A' }})</small>
                        </h6>
                        <div class="row g-2 mb-3">
                            <select name="chofer_id" class="form-select @error('chofer_id') is-invalid @enderror" required>
                                <option value="">Seleccione un chofer...</option>
                                @foreach($choferes as $chofer)
                                    @php
                                        $disponible = $chofer->isAvailable(
                                            $servicio->fecha_servicio->format('Y-m-d'),
                                            $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : '00:00',
                                            $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : '23:59'
                                        );
                                        $esActual = ($servicio->chofer_id == $chofer->id);
                                    @endphp
                                    <option value="{{ $chofer->id }}"
                                        {{ old('chofer_id', $servicio->chofer_id) == $chofer->id ? 'selected' : '' }}
                                        {{ (!$disponible && !$esActual) ? 'disabled' : '' }}>
                                        {{ $chofer->nombre_completo }} - Lic. {{ $chofer->licencia }}
                                        @if($esActual)
                                            [Actual]
                                        @elseif($disponible)
                                            [Disponible]
                                        @else
                                            [Ocupado]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('chofer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-user-friends me-2 text-primary"></i>Ayudantes
                            <span class="badge bg-info ms-2">Requeridos: {{ $servicio->cantidad_ayudantes }}</span>
                            <span class="badge bg-warning text-white ms-1" id="contadorAyudantes">Seleccionados: 0</span>
                            <small class="text-muted d-block mt-1">({{ $servicio->fecha_servicio->format('d/m/Y') }} {{ $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : 'N/A' }} - {{ $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : 'N/A' }})</small>
                        </h6>

                        @if($servicio->cantidad_ayudantes == 0)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>Este servicio no requiere ayudantes.
                            </div>
                            <div class="row g-2 mb-3">
                                @foreach($ayudantes as $ayudante)
                                <div class="col-md-4">
                                    <div class="ayudante-card" style="opacity: 0.6;">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" disabled>
                                            <label class="form-check-label small">
                                                {{ $ayudante->nombre_completo }}
                                                <br><span class="badge bg-secondary" style="font-size:0.65rem;">Deshabilitado</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @elseif($ayudantes->count() > 0)
                            <div class="row g-2 mb-3">
                                @foreach($ayudantes as $ayudante)
                                    @php
                                        $disponible = $ayudante->isAvailable(
                                            $servicio->fecha_servicio->format('Y-m-d'),
                                            $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : '00:00',
                                            $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : '23:59'
                                        );
                                        $yaAsignado = in_array($ayudante->id, $servicio->ayudantes->pluck('id')->toArray());
                                        $claseCard = $yaAsignado ? 'card-actual' : (!$disponible ? 'card-ocupado' : '');
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="ayudante-card {{ $claseCard }}">
                                            <div class="form-check">
                                                <input type="checkbox" name="ayudantes[]" value="{{ $ayudante->id }}"
                                                       class="form-check-input ayudante-checkbox {{ $yaAsignado ? 'checkbox-actual' : '' }}"
                                                       id="ayudante_{{ $ayudante->id }}"
                                                       data-id="{{ $ayudante->id }}"
                                                       {{ $yaAsignado ? 'checked' : '' }}
                                                       {{ (!$disponible && !$yaAsignado) ? 'disabled' : '' }}>
                                                <label class="form-check-label small" for="ayudante_{{ $ayudante->id }}">
                                                    {{ $ayudante->nombre_completo }}
                                                    <br>
                                                    @if($yaAsignado)
                                                        <span class="badge badge-actual" style="font-size:0.65rem;">🔵 Actual</span>
                                                    @elseif($disponible)
                                                        <span class="badge bg-success" style="font-size:0.65rem;">✅ Disponible</span>
                                                    @else
                                                        <span class="badge bg-danger" style="font-size:0.65rem;">❌ Ocupado</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="alertaAyudantes" class="alert alert-warning d-none">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span id="mensajeAyudantes"></span>
                            </div>
                        @else
                            <div class="alert alert-no-ayudantes">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No hay ayudantes disponibles en el sistema.
                                <a href="{{ route('ayudantes.create') }}" class="alert-link">Crear ayudante</a>
                            </div>
                        @endif

                        <div class="d-flex gap-2 border-top pt-3">
                            <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn" id="btnAsignar">
                                <i class="fas fa-check me-1"></i>
                                {{ $servicio->chofer_id || $servicio->vehiculo_id || $servicio->ayudantes->count() > 0 ? 'Actualizar Asignación' : 'Confirmar Asignación' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

@push('scripts')
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
    function cargarMiniCalendario() {
        const fecha = '{{ $servicio->fecha_servicio->format('Y-m-d') }}';
        const horaInicio = '{{ $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : '00:00' }}';
        const horaFin = '{{ $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : '23:59' }}';

        const vehiculoActual = '{{ $servicio->vehiculo_id }}';
        const choferActual = '{{ $servicio->chofer_id }}';
        const ayudantesActuales = @json($servicio->ayudantes->pluck('id')->toArray());

        const url = '{{ route('api.recursos-disponibles') }}?fecha=' + fecha + '&hora_inicio=' + horaInicio + '&hora_fin=' + horaFin;

        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            let html = '<table><thead><tr><th>Recurso</th><th>Estado</th></tr></thead><tbody>';

            const idsDisponibles = data.vehiculos ? data.vehiculos.map(function(v) { return v.id; }) : [];
            @foreach($vehiculos as $vehiculo)
                (function() {
                    var esActual = {{ $vehiculo->id }} == vehiculoActual;
                    var estaDisponible = idsDisponibles.includes({{ $vehiculo->id }});
                    var estado = '';
                    var icono = '';
                    if (esActual) {
                        estado = '<span class="badge badge-actual">🔵 Actual</span>';
                        icono = 'text-primary';
                    } else if (estaDisponible) {
                        estado = '<span class="badge bg-success">✅ Libre</span>';
                        icono = 'text-success';
                    } else {
                        estado = '<span class="badge bg-danger">🔴 Ocupado</span>';
                        icono = 'text-danger';
                    }
                    html += `<tr><td style="text-align:left;"><i class="fas fa-truck ${icono}"></i> {{ $vehiculo->placa }}</td><td>${estado}</td></tr>`;
                })();
            @endforeach

            const idsChoferesDisponibles = data.choferes ? data.choferes.map(function(c) { return c.id; }) : [];
            @foreach($choferes as $chofer)
                (function() {
                    var esActualChofer = {{ $chofer->id }} == choferActual;
                    var estaDisponibleChofer = idsChoferesDisponibles.includes({{ $chofer->id }});
                    var estadoChofer = '';
                    var iconoChofer = '';
                    if (esActualChofer) {
                        estadoChofer = '<span class="badge badge-actual">🔵 Actual</span>';
                        iconoChofer = 'text-primary';
                    } else if (estaDisponibleChofer) {
                        estadoChofer = '<span class="badge bg-success">✅ Libre</span>';
                        iconoChofer = 'text-success';
                    } else {
                        estadoChofer = '<span class="badge bg-danger">🔴 Ocupado</span>';
                        iconoChofer = 'text-danger';
                    }
                    html += `<tr><td style="text-align:left;"><i class="fas fa-user-circle ${iconoChofer}"></i> {{ $chofer->nombre_completo }}</td><td>${estadoChofer}</td></tr>`;
                })();
            @endforeach

            const idsAyudantesDisponibles = data.ayudantes ? data.ayudantes.map(function(a) { return a.id; }) : [];
            @foreach($ayudantes as $ayudante)
                (function() {
                    var esActualAyudante = ayudantesActuales.includes({{ $ayudante->id }});
                    var estaDisponibleAyudante = idsAyudantesDisponibles.includes({{ $ayudante->id }});
                    var estadoAyudante = '';
                    var iconoAyudante = '';
                    if (esActualAyudante) {
                        estadoAyudante = '<span class="badge badge-actual">🔵 Actual</span>';
                        iconoAyudante = 'text-primary';
                    } else if (estaDisponibleAyudante) {
                        estadoAyudante = '<span class="badge bg-success">✅ Libre</span>';
                        iconoAyudante = 'text-success';
                    } else {
                        estadoAyudante = '<span class="badge bg-danger">🔴 Ocupado</span>';
                        iconoAyudante = 'text-danger';
                    }
                    html += `<tr><td style="text-align:left;"><i class="fas fa-user-friends ${iconoAyudante}"></i> {{ $ayudante->nombre_completo }}</td><td>${estadoAyudante}</td></tr>`;
                })();
            @endforeach

            html += `</tbody></table>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        📅 {{ $servicio->fecha_servicio->format('d/m/Y') }} -
                        🕐 {{ $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : 'N/A' }} a {{ $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : 'N/A' }}
                    </small>
                </div>`;

            document.getElementById('miniCalendario').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('miniCalendario').innerHTML = `
                <div class="alert alert-danger text-center py-2 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar disponibilidad: ${error.message}
                </div>
            `;
        });
    }

    cargarMiniCalendario();

    var checkboxes = document.querySelectorAll('.ayudante-checkbox:not([disabled])');
    var contador = document.getElementById('contadorAyudantes');
    var alerta = document.getElementById('alertaAyudantes');
    var mensaje = document.getElementById('mensajeAyudantes');
    var ayudantesRequeridos = {{ $servicio->cantidad_ayudantes }};
    var form = document.getElementById('formAsignar');

    function actualizarContador() {
        var seleccionados = document.querySelectorAll('.ayudante-checkbox:checked').length;
        if (contador) contador.textContent = 'Seleccionados: ' + seleccionados;

        if (ayudantesRequeridos === 0) {
            if (contador) contador.className = seleccionados === 0 ? 'badge bg-success ms-1' : 'badge bg-danger ms-1';
        } else if (seleccionados === ayudantesRequeridos) {
            if (contador) contador.className = 'badge bg-success ms-1';
        } else if (seleccionados > ayudantesRequeridos) {
            if (contador) contador.className = 'badge bg-danger ms-1';
        } else {
            if (contador) contador.className = 'badge bg-warning text-white ms-1';
        }
    }

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var seleccionados = document.querySelectorAll('.ayudante-checkbox:checked').length;
            if (ayudantesRequeridos > 0 && seleccionados > ayudantesRequeridos && this.checked) {
                this.checked = false;
                mostrarToast('warning', 'Solo puedes seleccionar ' + ayudantesRequeridos + ' ayudante(s).');
            }
            actualizarContador();
        });
    });

    if (form) {
        form.addEventListener('submit', function(e) {
            var seleccionados = document.querySelectorAll('.ayudante-checkbox:checked').length;
            if (ayudantesRequeridos > 0 && seleccionados !== ayudantesRequeridos) {
                e.preventDefault();
                mostrarToast('error', 'Debes seleccionar exactamente ' + ayudantesRequeridos + ' ayudante(s).');
                return false;
            }
        });
    }

    actualizarContador();
});
</script>
@endpush
@endsection