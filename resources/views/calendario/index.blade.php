@extends('layouts.app')

@section('title', 'Calendario de Mudanzas')

@push('styles')
<style>
    /* ============================================ */
    /* ESTILOS PARA EL CALENDARIO - CORREGIDO */
    /* ============================================ */
    
    #calendar {
        max-width: 100% !important;
        margin: 0 auto !important;
        background: white !important;
        border-radius: 12px !important;
        padding: 16px !important;
        min-height: 500px !important;
    }

    /* ============================================ */
    /* ESTILOS PARA EVENTOS - MES Y SEMANA COMPACTOS */
    /* ============================================ */
    
    .fc-event {
        border: none !important;
        border-radius: 3px !important;
        padding: 1px 4px !important;
        font-size: 0.5rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
        margin: 1px 2px !important;
        line-height: 1.2 !important;
        min-width: 20px !important;
        transition: all 0.2s ease !important;
        overflow: hidden !important;
        max-width: 100% !important;
        display: block !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }

    .fc-event:hover {
        transform: scale(1.02) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        z-index: 10 !important;
    }

    .fc-event .fc-event-title {
        font-weight: 600 !important;
        font-size: 0.5rem !important;
        display: block !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .fc-event .fc-event-time {
        font-weight: 400 !important;
        opacity: 0.85 !important;
        font-size: 0.45rem !important;
        margin-right: 2px !important;
    }

    /* ============================================ */
    /* OCULTAR PERSONAL EN MES Y SEMANA */
    /* ============================================ */
    
    .fc-daygrid-event .event-personal,
    .fc-daygrid-event .event-ayudantes,
    .fc-timegrid-event .event-personal,
    .fc-timegrid-event .event-ayudantes {
        display: none !important;
    }

    /* ============================================ */
    /* MOSTRAR PERSONAL SOLO EN VISTA DÍA - TAMAÑOS MEJORADOS */
    /* ============================================ */
    
    .fc-dayGridDay-view .fc-event .event-personal,
    .fc-dayGridDay-view .fc-event .event-ayudantes,
    .fc-timeGridDay-view .fc-event .event-personal,
    .fc-timeGridDay-view .fc-event .event-ayudantes {
        display: block !important;
    }

    /* 🟢 CHOFER - TAMAÑO MEJORADO */
    .fc-dayGridDay-view .fc-event .event-personal,
    .fc-timeGridDay-view .fc-event .event-personal {
        font-size: 0.55rem !important;
        opacity: 0.9 !important;
        display: block !important;
        margin-top: 1px !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        color: rgba(255,255,255,0.95) !important;
    }

    .fc-dayGridDay-view .fc-event .event-personal i,
    .fc-timeGridDay-view .fc-event .event-personal i {
        margin-right: 2px !important;
        font-size: 0.5rem !important;
        width: 12px !important;
    }

    /* 🟢 VEHÍCULO - TAMAÑO MEJORADO (MÁS GRANDE) */
    .fc-dayGridDay-view .fc-event .event-vehiculo,
    .fc-timeGridDay-view .fc-event .event-vehiculo {
        font-size: 0.5rem !important;
        opacity: 0.75 !important;
        display: block !important;
        margin-top: 1px !important;
        font-weight: 400 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        color: rgba(255,255,255,0.85) !important;
    }

    .fc-dayGridDay-view .fc-event .event-vehiculo i,
    .fc-timeGridDay-view .fc-event .event-vehiculo i {
        margin-right: 2px !important;
        font-size: 0.45rem !important;
        width: 12px !important;
    }

    /* 🟢 AYUDANTES - TAMAÑO MEJORADO */
    .fc-dayGridDay-view .fc-event .event-ayudantes,
    .fc-timeGridDay-view .fc-event .event-ayudantes {
        font-size: 0.45rem !important;
        opacity: 0.65 !important;
        display: block !important;
        margin-top: 1px !important;
        font-weight: 300 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        color: rgba(255,255,255,0.8) !important;
    }

    .fc-dayGridDay-view .fc-event .event-ayudantes i,
    .fc-timeGridDay-view .fc-event .event-ayudantes i {
        margin-right: 2px !important;
        font-size: 0.4rem !important;
        width: 12px !important;
    }

    /* ============================================ */
    /* CELDAS DE DÍAS - MÁS COMPACTAS */
    /* ============================================ */
    
    .fc-daygrid-day-frame {
        min-height: 55px !important;
        padding: 2px !important;
        overflow: hidden !important;
    }

    .fc-daygrid-day-events {
        min-height: 15px !important;
        max-height: 80px !important;
        overflow-y: auto !important;
    }

    .fc-daygrid-day-events::-webkit-scrollbar {
        width: 2px !important;
    }

    .fc-daygrid-day-events::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 10px !important;
    }

    .fc-daygrid-day-number {
        font-weight: 600 !important;
        font-size: 0.65rem !important;
        color: #1e293b !important;
        padding: 2px 4px !important;
    }

    .fc-daygrid-day.fc-day-today {
        background: rgba(79, 70, 229, 0.06) !important;
    }

    .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: #4f46e5 !important;
        color: white !important;
        border-radius: 50% !important;
        width: 22px !important;
        height: 22px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 2px !important;
        font-size: 0.6rem !important;
    }

    /* ============================================ */
    /* VISTA SEMANA - MÁS COMPACTA */
    /* ============================================ */
    
    .fc-timegrid-slot {
        height: 25px !important;
    }

    .fc-timegrid-event {
        border-radius: 3px !important;
        padding: 1px 3px !important;
        font-size: 0.5rem !important;
        margin: 1px 2px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .fc-timegrid-event .fc-event-title {
        font-size: 0.45rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* ============================================ */
    /* BOTONES DE VISTA */
    /* ============================================ */
    
    .fc .fc-button {
        background: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        color: #1e293b !important;
        font-weight: 600 !important;
        font-size: 0.7rem !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        box-shadow: none !important;
        height: auto !important;
        text-transform: capitalize !important;
        transition: all 0.2s ease !important;
    }

    .fc .fc-button:hover {
        background: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: white !important;
        transform: translateY(-1px) !important;
    }

    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: white !important;
    }

    .fc .fc-toolbar-title {
        font-size: 1rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
    }

    /* ============================================ */
    /* RESPONSIVE */
    /* ============================================ */
    
    @media (max-width: 768px) {
        .fc-event {
            font-size: 0.4rem !important;
            padding: 1px 2px !important;
            margin: 1px 1px !important;
            min-width: 15px !important;
        }
        .fc-event .fc-event-title {
            font-size: 0.35rem !important;
        }
        .fc-event .fc-event-time {
            font-size: 0.3rem !important;
        }
        .fc-daygrid-day-frame {
            min-height: 35px !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.55rem !important;
            padding: 1px 2px !important;
        }
        .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            width: 18px !important;
            height: 18px !important;
            font-size: 0.55rem !important;
        }
        .fc .fc-toolbar {
            flex-direction: column !important;
            align-items: center !important;
            gap: 6px !important;
        }
        .fc .fc-toolbar-chunk {
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 3px !important;
        }
        .fc .fc-toolbar-title {
            font-size: 0.75rem !important;
        }
        .fc .fc-button {
            font-size: 0.55rem !important;
            padding: 2px 6px !important;
        }
        .fc-timegrid-slot {
            height: 20px !important;
        }
        .fc-timegrid-event {
            font-size: 0.4rem !important;
            padding: 1px 2px !important;
        }
        .fc-timegrid-event .fc-event-title {
            font-size: 0.35rem !important;
        }
        /* Vista día en móvil - tamaños ajustados */
        .fc-dayGridDay-view .fc-event .event-personal,
        .fc-timeGridDay-view .fc-event .event-personal {
            font-size: 0.45rem !important;
        }
        .fc-dayGridDay-view .fc-event .event-vehiculo,
        .fc-timeGridDay-view .fc-event .event-vehiculo {
            font-size: 0.4rem !important;
        }
        .fc-dayGridDay-view .fc-event .event-ayudantes,
        .fc-timeGridDay-view .fc-event .event-ayudantes {
            font-size: 0.35rem !important;
        }
    }

    @media (max-width: 480px) {
        .fc-event .fc-event-title {
            font-size: 0.3rem !important;
        }
        .fc-event .fc-event-time {
            font-size: 0.25rem !important;
        }
        .fc-daygrid-day-frame {
            min-height: 28px !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.45rem !important;
        }
        .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            width: 14px !important;
            height: 14px !important;
            font-size: 0.45rem !important;
        }
        .fc .fc-toolbar-title {
            font-size: 0.65rem !important;
        }
        .fc .fc-button {
            font-size: 0.45rem !important;
            padding: 1px 4px !important;
        }
        .fc-timegrid-slot {
            height: 18px !important;
        }
        /* Vista día en móvil muy pequeño - ocultar detalles */
        .fc-dayGridDay-view .fc-event .event-vehiculo,
        .fc-timeGridDay-view .fc-event .event-vehiculo {
            display: none !important;
        }
        .fc-dayGridDay-view .fc-event .event-ayudantes,
        .fc-timeGridDay-view .fc-event .event-ayudantes {
            display: none !important;
        }
        .fc-dayGridDay-view .fc-event .event-personal,
        .fc-timeGridDay-view .fc-event .event-personal {
            font-size: 0.4rem !important;
        }
    }

    /* Estilo para el cursor pointer en días */
    .fc-daygrid-day {
        cursor: pointer !important;
    }
    .fc-daygrid-day:hover {
        background: rgba(79, 70, 229, 0.03) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Calendario de Mudanzas
                    </h5>
                    <div>
                        @if(auth()->user()->isAdmin() || auth()->user()->isRecepcionista())
                        <a href="{{ route('servicios.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Nueva Mudanza
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <select id="filtro-vehiculo" class="form-select form-select-sm">
                                <option value="">🚛 Todos los vehículos</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->placa }} - {{ $vehiculo->marca }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filtro-chofer" class="form-select form-select-sm">
                                <option value="">👨‍✈️ Todos los choferes</option>
                                @foreach($choferes as $chofer)
                                    <option value="{{ $chofer->id }}">{{ $chofer->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filtro-estado" class="form-select form-select-sm">
                                <option value="">📌 Todos los estados</option>
                                @foreach(\App\Models\Servicio::ESTADOS_LABEL as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button id="btn-filtrar" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Calendario -->
                    <div id="calendar"></div>

                    <!-- Leyenda -->
                    <div class="d-flex flex-wrap gap-3 justify-content-center mt-3 p-2 bg-light rounded">
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge" style="background:#f59e0b; width:14px; height:14px;">&nbsp;</span> Pendiente
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge" style="background:#3b82f6; width:14px; height:14px;">&nbsp;</span> Confirmado
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge" style="background:#8b5cf6; width:14px; height:14px;">&nbsp;</span> En Progreso
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge" style="background:#10b981; width:14px; height:14px;">&nbsp;</span> Finalizado
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge" style="background:#ef4444; width:14px; height:14px;">&nbsp;</span> Cancelado
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge" style="background:#f97316; width:14px; height:14px;">&nbsp;</span> Pendiente Pago
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge" style="background:#06b6d4; width:14px; height:14px;">&nbsp;</span> Pagado
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL DE DETALLES DEL EVENTO -->
<!-- ============================================ -->
<div class="modal fade" id="eventoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-list me-2"></i>Detalles del Servicio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventoDetalles"></div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL DE DÍA - TODOS LOS SERVICIOS DEL DÍA -->
<!-- ============================================ -->
<div class="modal fade" id="diaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #059669, #047857); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-day me-2"></i>Servicios del <span id="fechaSeleccionada"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="diaDetalles"></div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL DE DISPONIBILIDAD -->
<!-- ============================================ -->
<div class="modal fade" id="disponibilidadModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Recursos Disponibles
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="disponibilidadDetalles"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Iniciando calendario...');

    var calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) {
        console.error('❌ No se encontró #calendar');
        return;
    }

    if (typeof FullCalendar === 'undefined') {
        console.error('❌ FullCalendar no está cargado');
        document.getElementById('calendar').innerHTML = `
            <div class="alert alert-danger text-center py-4">
                <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                <h5>Error al cargar el calendario</h5>
                <p>FullCalendar no se cargó correctamente.</p>
                <button onclick="location.reload()" class="btn btn-primary mt-2">
                    <i class="fas fa-sync-alt me-1"></i> Recargar
                </button>
            </div>
        `;
        return;
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        // ============================================
        // EVENTOS - MES Y SEMANA: SOLO TÍTULO
        // DÍA: MUESTRA TODO CON TAMAÑOS MEJORADOS
        // ============================================
        eventContent: function(info) {
            const props = info.event.extendedProps;
            const tiempo = info.event.start ? info.event.start.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit'}) : '';
            
            // Obtener la vista actual
            const viewType = info.view.type;
            
            // En vista DÍA mostrar todo
            if (viewType === 'dayGridDay' || viewType === 'timeGridDay') {
                let html = `
                    <div class="fc-event-main" style="width:100%;">
                        <div class="fc-event-title">
                            <span class="fc-event-time">${tiempo}</span>
                            <span>${info.event.title}</span>
                        </div>
                `;
                
                // 🟢 CHOFER - TAMAÑO MEJORADO
                if (props.chofer && props.chofer !== 'Sin asignar') {
                    html += `
                        <div class="event-personal">
                            <i class="fas fa-user-circle"></i> ${props.chofer}
                        </div>
                    `;
                }
                
                // 🟢 VEHÍCULO - TAMAÑO MEJORADO (MÁS GRANDE)
                if (props.vehiculo && props.vehiculo !== 'Sin asignar') {
                    // Mostrar solo la placa si es muy largo
                    let vehiculoMostrar = props.vehiculo;
                    if (vehiculoMostrar.length > 20) {
                        vehiculoMostrar = vehiculoMostrar.split(' - ')[0] || vehiculoMostrar;
                    }
                    html += `
                        <div class="event-vehiculo">
                            <i class="fas fa-truck"></i> ${vehiculoMostrar}
                        </div>
                    `;
                }

// 🟢 AYUDANTES - TODOS COMPLETOS (SIN LÍMITE)
if (props.ayudantes && props.ayudantes.length > 0) {
    let ayudantesTexto = props.ayudantes.join(', ');
    html += `
        <div class="event-ayudantes">
            <i class="fas fa-users"></i> ${ayudantesTexto}
        </div>
    `;
}
                
                html += `</div>`;
                return { html: html };
            }
            
            // En MES y SEMANA: SOLO TÍTULO (sin personal)
            return {
                html: `
                    <div class="fc-event-main" style="width:100%;">
                        <div class="fc-event-title">
                            <span class="fc-event-time">${tiempo}</span>
                            <span>${info.event.title}</span>
                        </div>
                    </div>
                `
            };
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            let url = "{{ route('api.eventos') }}?start=" + fetchInfo.startStr + "&end=" + fetchInfo.endStr;
            
            const vehiculo = document.getElementById('filtro-vehiculo')?.value || '';
            const chofer = document.getElementById('filtro-chofer')?.value || '';
            const estado = document.getElementById('filtro-estado')?.value || '';
            
            if (vehiculo) url += "&vehiculo=" + vehiculo;
            if (chofer) url += "&chofer=" + chofer;
            if (estado) url += "&estado=" + estado;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Eventos cargados:', data.length);
                    successCallback(data);
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    failureCallback(error);
                });
        },
        // ============================================
        // CLICK EN EVENTO - MUESTRA DETALLES
        // ============================================
        eventClick: function(info) {
            const props = info.event.extendedProps;
            
            const estadoBadge = {
                'pendiente': 'warning',
                'confirmado': 'primary',
                'en_progreso': 'secondary',
                'finalizado': 'success',
                'cancelado': 'danger',
                'pendiente_pago': 'danger',
                'pagado': 'info'
            };

            const estadoIcon = {
                'pendiente': 'fa-clock',
                'confirmado': 'fa-check-circle',
                'en_progreso': 'fa-spinner fa-spin',
                'finalizado': 'fa-flag-checkered',
                'cancelado': 'fa-ban',
                'pendiente_pago': 'fa-hand-holding-usd',
                'pagado': 'fa-check-double'
            };

            let ayudantesHtml = '';
            if (props.ayudantes && props.ayudantes.length > 0) {
                props.ayudantes.forEach(function(nombre) {
                    ayudantesHtml += `<span class="badge bg-info me-1">👷 ${nombre}</span>`;
                });
            } else {
                ayudantesHtml = `<span class="text-muted">No asignados</span>`;
            }

            document.getElementById('eventoDetalles').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 pb-2 border-bottom">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">👤 Cliente</h6>
                            <p class="fw-bold mb-0">${props.cliente || 'N/A'}</p>
                            <small class="text-muted">📞 ${props.cliente_telefono || 'N/A'}</small>
                        </div>
                        <div class="mb-3 pb-2 border-bottom">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">🚛 Vehículo</h6>
                            <p class="fw-bold mb-0">${props.vehiculo || 'Sin asignar'}</p>
                            <small class="text-muted">Tipo: ${props.vehiculo_tipo || 'N/A'}</small>
                        </div>
                        <div class="mb-3 pb-2 border-bottom">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">👨‍✈️ Chofer</h6>
                            <p class="fw-bold mb-0">${props.chofer || 'Sin asignar'}</p>
                            <small class="text-muted">📞 ${props.chofer_telefono || 'N/A'}</small>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">👷 Ayudantes</h6>
                            <div class="d-flex flex-wrap gap-1">
                                ${ayudantesHtml}
                            </div>
                            <small class="text-muted">Requeridos: ${props.cantidad_ayudantes || 0}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 pb-2 border-bottom">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">📌 Estado</h6>
                            <span class="badge bg-${estadoBadge[props.estado] || 'secondary'}" style="font-size:0.9rem; padding:6px 16px;">
                                <i class="fas ${estadoIcon[props.estado] || 'fa-tasks'} me-1"></i>
                                ${props.estado_label || props.estado}
                            </span>
                        </div>
                        <div class="mb-3 pb-2 border-bottom">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">📍 Ubicación</h6>
                            <p class="mb-0"><strong>Origen:</strong> ${props.origen || 'N/A'}</p>
                            <p class="mb-0"><strong>Destino:</strong> ${props.destino || 'N/A'}</p>
                        </div>
                        <div class="mb-3 pb-2 border-bottom">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">📅 Fecha y Hora</h6>
                            <p class="mb-0"><strong>Fecha:</strong> ${props.fecha || 'N/A'}</p>
                            <p class="mb-0"><strong>Horario:</strong> ${props.hora_inicio} - ${props.hora_fin}</p>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3 text-center">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">💰 Costo Total</h6>
                            <span class="fw-bold text-primary h4">Bs. ${props.costo ? props.costo.toFixed(2) : '0.00'}</span>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="${props.url}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i> Ver Detalles Completos
                    </a>
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cerrar
                    </button>
                </div>
            `;
            
            $('#eventoModal').modal('show');
        },
        // ============================================
        // CLICK EN DÍA - MUESTRA TODOS LOS SERVICIOS
        // ============================================
        dateClick: function(info) {
            console.log('📅 Click en día:', info.dateStr);
            
            const fecha = info.dateStr;
            const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            const eventosDelDia = calendar.getEvents().filter(evento => {
                if (!evento.start) return false;
                const fechaEvento = evento.start.toISOString().split('T')[0];
                return fechaEvento === fecha;
            });
            
            document.getElementById('fechaSeleccionada').textContent = fechaFormateada;
            
            if (eventosDelDia.length === 0) {
                document.getElementById('diaDetalles').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay servicios programados para este día.</p>
                        @if(auth()->user()->isAdmin() || auth()->user()->isRecepcionista())
                            <a href="{{ route('servicios.create') }}?fecha=${fecha}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Crear Servicio
                            </a>
                        @endif
                    </div>
                `;
            } else {
                let html = `<div class="list-group">`;
                
                eventosDelDia.sort((a, b) => a.start - b.start);
                
                eventosDelDia.forEach(evento => {
                    const props = evento.extendedProps;
                    const horaInicio = evento.start ? evento.start.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit'}) : 'N/A';
                    const horaFin = evento.end ? evento.end.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit'}) : 'N/A';
                    
                    const estadoBadge = {
                        'pendiente': 'warning',
                        'confirmado': 'primary',
                        'en_progreso': 'secondary',
                        'finalizado': 'success',
                        'cancelado': 'danger',
                        'pendiente_pago': 'danger',
                        'pagado': 'info'
                    };
                    
                    let ayudantesLista = '';
                    if (props.ayudantes && props.ayudantes.length > 0) {
                        props.ayudantes.forEach(function(nombre) {
                            ayudantesLista += `<span class="badge bg-info me-1">${nombre}</span>`;
                        });
                    } else {
                        ayudantesLista = `<span class="text-muted">Sin ayudantes</span>`;
                    }
                    
                    html += `
                        <a href="${props.url}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <span class="badge bg-secondary">${horaInicio} - ${horaFin}</span>
                                        <span class="fw-bold">#${evento.id} - ${props.cliente || 'N/A'}</span>
                                    </h6>
                                    <small class="text-muted d-block">
                                        🚛 ${props.vehiculo || 'Sin asignar'} 
                                        👨‍✈️ ${props.chofer || 'Sin asignar'}
                                    </small>
                                    <div class="mt-1">${ayudantesLista}</div>
                                </div>
                                <span class="badge bg-${estadoBadge[props.estado] || 'secondary'}">
                                    ${props.estado_label || props.estado}
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                📍 ${props.origen} → ${props.destino}
                            </small>
                        </a>
                    `;
                });
                
                html += `
                    </div>
                    <div class="text-center mt-3">
                        @if(auth()->user()->isAdmin() || auth()->user()->isRecepcionista())
                            <a href="{{ route('servicios.create') }}?fecha=${fecha}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Crear Servicio en este día
                            </a>
                        @endif
                        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cerrar
                        </button>
                    </div>
                `;
                
                document.getElementById('diaDetalles').innerHTML = html;
            }
            
            $('#diaModal').modal('show');
        },
        // ============================================
        // SELECCIÓN DE RANGO - DISPONIBILIDAD
        // ============================================
        selectable: true,
        select: function(info) {
            const fecha = info.startStr.split('T')[0];
            const horaInicio = info.startStr.split('T')[1] || '07:00:00';
            const horaFin = info.endStr.split('T')[1] || '08:00:00';
            
            document.getElementById('disponibilidadDetalles').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2">Consultando disponibilidad...</p>
                </div>
            `;
            
            fetch(`{{ route('api.recursos-disponibles') }}?fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div class="alert alert-info">
                            <i class="fas fa-calendar-check me-2"></i>
                            <strong>${fecha}</strong> - 🕐 ${horaInicio} a ${horaFin}
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-truck text-primary"></i> Vehículos (${data.vehiculos.length})</h6>
                                    </div>
                                    <div class="card-body" style="max-height:200px; overflow-y:auto;">
                                        <ul class="list-unstyled mb-0">`;
                    
                    if (data.vehiculos.length > 0) {
                        data.vehiculos.forEach(v => {
                            html += `<li class="py-1">✅ ${v.placa} - ${v.marca}</li>`;
                        });
                    } else {
                        html += `<li class="text-danger">❌ No hay vehículos disponibles</li>`;
                    }
                    
                    html += `</ul></div></div></div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-user-circle text-primary"></i> Choferes (${data.choferes.length})</h6>
                                    </div>
                                    <div class="card-body" style="max-height:200px; overflow-y:auto;">
                                        <ul class="list-unstyled mb-0">`;
                    
                    if (data.choferes.length > 0) {
                        data.choferes.forEach(c => {
                            html += `<li class="py-1">✅ ${c.nombre_completo}</li>`;
                        });
                    } else {
                        html += `<li class="text-danger">❌ No hay choferes disponibles</li>`;
                    }
                    
                    html += `</ul></div></div></div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-user-friends text-primary"></i> Ayudantes (${data.ayudantes.length})</h6>
                                    </div>
                                    <div class="card-body" style="max-height:200px; overflow-y:auto;">
                                        <ul class="list-unstyled mb-0">`;
                    
                    if (data.ayudantes.length > 0) {
                        data.ayudantes.forEach(a => {
                            html += `<li class="py-1">✅ ${a.nombre_completo}</li>`;
                        });
                    } else {
                        html += `<li class="text-danger">❌ No hay ayudantes disponibles</li>`;
                    }
                    
                    html += `</ul></div></div></div>
                        </div>
                        <hr class="my-3">
                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('servicios.create') }}?fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}" 
                                   class="btn btn-success w-100">
                                    <i class="fas fa-plus me-1"></i> Crear Mudanza
                                </a>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-secondary w-100" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cerrar
                                </button>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('disponibilidadDetalles').innerHTML = html;
                    $('#disponibilidadModal').modal('show');
                });
        },
        selectAllow: function(info) {
            const now = new Date();
            now.setHours(0, 0, 0, 0);
            const startDate = new Date(info.startStr);
            startDate.setHours(0, 0, 0, 0);
            return startDate >= now;
        },
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6],
            startTime: '07:00',
            endTime: '20:00',
        },
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        nowIndicator: true,
        height: 'auto',
        contentHeight: 600,
        dayMaxEvents: 4,
        moreLinkText: function(n) {
            return `+ ${n} más`;
        }
    });
    
    calendar.render();
    console.log('✅ Calendario renderizado correctamente');

    document.getElementById('btn-filtrar').addEventListener('click', function() {
        calendar.refetchEvents();
    });

    setInterval(function() {
        calendar.refetchEvents();
    }, 30000);
});
</script>
@endpush