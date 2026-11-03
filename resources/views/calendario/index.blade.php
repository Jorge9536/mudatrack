@extends('layouts.app')

@section('title', 'Calendario de Mudanzas')

@push('styles')
<style>
    #calendar {
        max-width: 100% !important;
        margin: 0 auto !important;
        background: white !important;
        border-radius: 12px !important;
        padding: 16px !important;
        min-height: 700px !important;
    }
    .fc-event {
        border: none !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
        margin: 2px 3px !important;
        line-height: 1.4 !important;
        min-height: 40px !important;
        transition: all 0.2s ease !important;
        overflow: hidden !important;
        max-width: calc(100% - 6px) !important;
        display: block !important;
        color: #ffffff !important;
    }

    .fc-event:hover {
        transform: scale(1.03) !important;
        box-shadow: 0 8px 20px rgba(0,0,0,0.25) !important;
        z-index: 10 !important;
    }

    .fc-event,
    .fc-event *,
    .fc-event .fc-event-main,
    .fc-event .fc-event-main-frame,
    .fc-event .fc-event-title-container,
    .fc-event .fc-event-title {
        color: #ffffff !important;
    }

    .fc-event .fc-event-title {
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        display: block !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.25) !important;
    }

    .fc-event .fc-event-time {
        font-weight: 600 !important;
        opacity: 1 !important;
        font-size: 0.8rem !important;
        margin-right: 6px !important;
    }
    .fc-daygrid-event .event-personal,
    .fc-daygrid-event .event-ayudantes,
    .fc-timegrid-event .event-personal,
    .fc-timegrid-event .event-ayudantes {
        display: none !important;
    }

    .fc-dayGridDay-view .fc-event .event-personal,
    .fc-dayGridDay-view .fc-event .event-ayudantes,
    .fc-timeGridDay-view .fc-event .event-personal,
    .fc-timeGridDay-view .fc-event .event-ayudantes {
        display: block !important;
    }

    .fc-dayGridDay-view .fc-event .event-personal,
    .fc-timeGridDay-view .fc-event .event-personal {
        font-size: 0.9rem !important;
        display: block !important;
        margin-top: 4px !important;
        font-weight: 600 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .fc-dayGridDay-view .fc-event .event-personal i,
    .fc-timeGridDay-view .fc-event .event-personal i {
        margin-right: 6px !important;
        font-size: 0.85rem !important;
        width: 16px !important;
    }

    .fc-dayGridDay-view .fc-event .event-vehiculo,
    .fc-timeGridDay-view .fc-event .event-vehiculo {
        font-size: 0.85rem !important;
        display: block !important;
        margin-top: 4px !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .fc-dayGridDay-view .fc-event .event-vehiculo i,
    .fc-timeGridDay-view .fc-event .event-vehiculo i {
        margin-right: 6px !important;
        font-size: 0.8rem !important;
        width: 16px !important;
    }

    .fc-dayGridDay-view .fc-event .event-ayudantes,
    .fc-timeGridDay-view .fc-event .event-ayudantes {
        font-size: 0.8rem !important;
        display: block !important;
        margin-top: 4px !important;
        font-weight: 500 !important;
        white-space: normal !important;
        line-height: 1.4 !important;
    }

    .fc-dayGridDay-view .fc-event .event-ayudantes i,
    .fc-timeGridDay-view .fc-event .event-ayudantes i {
        margin-right: 6px !important;
        font-size: 0.75rem !important;
        width: 16px !important;
    }

    .fc-daygrid-day-frame {
        min-height: 130px !important;
        padding: 6px !important;
        overflow: hidden !important;
    }

    .fc-daygrid-day-events {
        min-height: 30px !important;
        max-height: 180px !important;
        overflow-y: auto !important;
        margin-top: 4px !important;
    }

    .fc-daygrid-day-events::-webkit-scrollbar {
        width: 5px !important;
    }

    .fc-daygrid-day-events::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 10px !important;
    }

    .fc-daygrid-day-number {
        font-weight: 700 !important;
        font-size: 1rem !important;
        color: #1e293b !important;
        padding: 6px 10px !important;
    }

    .fc-daygrid-day.fc-day-today {
        background: rgba(79, 70, 229, 0.06) !important;
    }

    .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: #4f46e5 !important;
        color: white !important;
        border-radius: 50% !important;
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 4px !important;
        font-size: 0.9rem !important;
    }

    .fc-timegrid-slot {
        height: 45px !important;
    }

    .fc-timegrid-event {
        border-radius: 6px !important;
        padding: 6px 8px !important;
        font-size: 0.85rem !important;
        margin: 2px 3px !important;
    }

    .fc-timegrid-event .fc-event-title {
        font-size: 0.85rem !important;
        line-height: 1.3 !important;
    }

    .fc-col-header-cell {
        padding: 12px 4px !important;
        background: #f8fafc !important;
        font-weight: 700 !important;
    }

    .fc-col-header-cell-cushion {
        font-size: 0.95rem !important;
        color: #1e293b !important;
        text-transform: uppercase !important;
        text-decoration: none !important;
        padding: 4px !important;
        letter-spacing: 0.5px !important;
    }

    .fc .fc-button {
        background: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        color: #1e293b !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
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
        font-size: 1.4rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        text-transform: capitalize !important;
    }

    .fc-daygrid-more-link {
        font-size: 0.8rem !important;
        font-weight: 700 !important;
        color: #4f46e5 !important;
        padding: 2px 6px !important;
        background: rgba(79, 70, 229, 0.1) !important;
        border-radius: 4px !important;
    }

    @media (max-width: 768px) {
        .fc-event {
            font-size: 0.75rem !important;
            padding: 4px 6px !important;
            margin: 2px !important;
            min-height: 30px !important;
        }
        .fc-event .fc-event-title {
            font-size: 0.75rem !important;
        }
        .fc-event .fc-event-time {
            font-size: 0.7rem !important;
        }
        .fc-daygrid-day-frame {
            min-height: 90px !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.9rem !important;
        }
        .fc .fc-toolbar {
            flex-direction: column !important;
            gap: 10px !important;
        }
        .fc .fc-toolbar-title {
            font-size: 1.1rem !important;
        }
        .fc .fc-button {
            font-size: 0.8rem !important;
            padding: 6px 12px !important;
        }
        .fc-timegrid-slot {
            height: 35px !important;
        }
    }

    @media (max-width: 480px) {
        .fc-event .fc-event-title {
            font-size: 0.7rem !important;
        }
        .fc-event .fc-event-time {
            font-size: 0.65rem !important;
        }
        .fc-daygrid-day-frame {
            min-height: 70px !important;
        }
        .fc .fc-toolbar-title {
            font-size: 1rem !important;
        }
        .fc .fc-button {
            font-size: 0.7rem !important;
            padding: 4px 8px !important;
        }
    }

    .fc-daygrid-day {
        cursor: pointer !important;
    }
    .fc-daygrid-day:hover {
        background: rgba(79, 70, 229, 0.04) !important;
    }

    .servicio-card-dia {
        border-left: 4px solid;
        padding: 14px 16px;
        margin-bottom: 10px;
        background: #f8fafc;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .servicio-card-dia:hover {
        background: #f1f5f9;
        transform: translateX(3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .servicio-card-dia .servicio-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .servicio-card-dia .servicio-horario {
        background: #1e293b;
        color: #fff;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .servicio-card-dia .servicio-cliente {
        font-weight: 700;
        color: #0f172a;
        font-size: 1.05rem;
    }
    .servicio-card-dia .servicio-info {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }
    .servicio-card-dia .servicio-info-item {
        background: #fff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .servicio-card-dia .servicio-info-item i {
        color: #64748b;
        margin-right: 4px;
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

            
                    <div id="calendar"></div>

        
                    <div class="d-flex flex-wrap gap-3 justify-content-center mt-3 p-2 bg-light rounded">
                        <span class="d-flex align-items-center gap-1 small">
                            <span style="background:#f59e0b; width:16px; height:16px; border-radius:4px; display:inline-block;"></span> Pendiente
                        </span>
                        <span class="d-flex align-items-center gap-1 small">
                            <span style="background:#3b82f6; width:16px; height:16px; border-radius:4px; display:inline-block;"></span> Confirmado
                        </span>
                        <span class="d-flex align-items-center gap-1 small">
                            <span style="background:#8b5cf6; width:16px; height:16px; border-radius:4px; display:inline-block;"></span> En Progreso
                        </span>
                        <span class="d-flex align-items-center gap-1 small">
                            <span style="background:#10b981; width:16px; height:16px; border-radius:4px; display:inline-block;"></span> Finalizado
                        </span>
                        <span class="d-flex align-items-center gap-1 small">
                            <span style="background:#ef4444; width:16px; height:16px; border-radius:4px; display:inline-block;"></span> Cancelado
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<div class="modal fade" id="diaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
    
    if (!calendarEl) return;

    if (typeof FullCalendar === 'undefined') {
        calendarEl.innerHTML = `<div class="alert alert-danger">Error al cargar FullCalendar</div>`;
        return;
    }

    function getColorEstado(estado) {
        const colores = {
            'pendiente':   '#f59e0b',
            'confirmado':  '#3b82f6',
            'en_progreso': '#8b5cf6',
            'finalizado':  '#10b981',
            'cancelado':   '#ef4444',
        };
        return colores[estado] || '#6c757d';
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
        eventDidMount: function(info) {
            const color = getColorEstado(info.event.extendedProps.estado);
            info.el.style.setProperty('background-color', color, 'important');
            info.el.style.setProperty('border-color', color, 'important');
            info.el.style.setProperty('color', '#ffffff', 'important');

            const titleEl = info.el.querySelector('.fc-event-title');
            const timeEl  = info.el.querySelector('.fc-event-time');
            if (titleEl) titleEl.style.setProperty('color', '#ffffff', 'important');
            if (timeEl)  timeEl.style.setProperty('color', '#ffffff', 'important');
        },
        eventContent: function(info) {
            const props = info.event.extendedProps;
            const tiempo = info.event.start ? info.event.start.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit'}) : '';
            const viewType = info.view.type;
            if (viewType === 'dayGridDay' || viewType === 'timeGridDay') {
                let html = `
                    <div class="fc-event-main" style="width:100%;">
                        <div class="fc-event-title">
                            <span class="fc-event-time">${tiempo}</span>
                            <span>${info.event.title}</span>
                        </div>
                `;
                
                if (props.chofer && props.chofer !== 'Sin asignar') {
                    html += `<div class="event-personal"><i class="fas fa-user-circle"></i> ${props.chofer}</div>`;
                }
                if (props.vehiculo && props.vehiculo !== 'Sin asignar') {
                    let v = props.vehiculo;
                    if (v.length > 20) v = v.split(' - ')[0] || v;
                    html += `<div class="event-vehiculo"><i class="fas fa-truck"></i> ${v}</div>`;
                }
                if (props.ayudantes && props.ayudantes.length > 0) {
                    html += `<div class="event-ayudantes"><i class="fas fa-users"></i> ${props.ayudantes.join(', ')}</div>`;
                }
                
                html += `</div>`;
                return { html: html };
            }
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
        eventClick: function(info) {
            const props = info.event.extendedProps;
            
            const estadoBadge = {
                'pendiente': 'warning',
                'confirmado': 'primary',
                'en_progreso': 'secondary',
                'finalizado': 'success',
                'cancelado': 'danger'
            };

            const estadoIcon = {
                'pendiente': 'fa-clock',
                'confirmado': 'fa-check-circle',
                'en_progreso': 'fa-spinner fa-spin',
                'finalizado': 'fa-flag-checkered',
                'cancelado': 'fa-ban'
            };

            let ayudantesHtml = '';
            if (props.ayudantes && props.ayudantes.length > 0) {
                props.ayudantes.forEach(function(nombre) {
                    ayudantesHtml += `<span class="badge bg-info me-1 mb-1">👷 ${nombre}</span>`;
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
                        </div>
                        <div class="mb-3 pb-2 border-bottom">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">👨‍✈️ Chofer</h6>
                            <p class="fw-bold mb-0">${props.chofer || 'Sin asignar'}</p>
                            <small class="text-muted">📞 ${props.chofer_telefono || 'N/A'}</small>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">👷 Ayudantes</h6>
                            <div class="d-flex flex-wrap gap-1">${ayudantesHtml}</div>
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
            
            new bootstrap.Modal(document.getElementById('eventoModal')).show();
        },
        dateClick: function(info) {
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
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-check fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay servicios programados para este día</h5>
                        @if(auth()->user()->isAdmin() || auth()->user()->isRecepcionista())
                            <a href="{{ route('servicios.create') }}?fecha=${fecha}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-1"></i> Crear Servicio para este día
                            </a>
                        @endif
                    </div>
                `;
            } else {
                let html = `<p class="text-muted mb-3">
                    <i class="fas fa-list me-1"></i>
                    <strong>${eventosDelDia.length}</strong> servicio(s) programado(s) para este día:
                </p>`;
                eventosDelDia.sort((a, b) => a.start - b.start);
                
                const estadoInfo = {
                    'pendiente':   { color: '#f59e0b', label: 'Pendiente',   icon: 'fa-clock' },
                    'confirmado':  { color: '#3b82f6', label: 'Confirmado',  icon: 'fa-check-circle' },
                    'en_progreso': { color: '#8b5cf6', label: 'En Progreso', icon: 'fa-spinner' },
                    'finalizado':  { color: '#10b981', label: 'Finalizado',  icon: 'fa-flag-checkered' },
                    'cancelado':   { color: '#ef4444', label: 'Cancelado',   icon: 'fa-ban' }
                };
                
                eventosDelDia.forEach(evento => {
                    const props = evento.extendedProps;
                    const horaInicio = evento.start ? evento.start.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit'}) : 'N/A';
                    const horaFin = evento.end ? evento.end.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit'}) : 'N/A';
                    const info = estadoInfo[props.estado] || estadoInfo.pendiente;
                    
                    html += `
                        <div class="servicio-card-dia" style="border-left-color: ${info.color};" onclick="window.location.href='${props.url}'">
                            <div class="servicio-header">
                                <span class="servicio-horario">
                                    <i class="far fa-clock me-1"></i>${horaInicio} - ${horaFin}
                                </span>
                                <span class="badge" style="background:${info.color}; padding:6px 12px; font-size:0.8rem;">
                                    <i class="fas ${info.icon} me-1"></i>${info.label}
                                </span>
                            </div>
                            
                            <div class="servicio-cliente">
                                <i class="fas fa-user me-2 text-muted"></i>#${evento.id} · ${props.cliente || 'N/A'}
                            </div>
                            
                            <div class="servicio-info">
                                ${props.vehiculo && props.vehiculo !== 'Sin asignar' ? `
                                    <span class="servicio-info-item">
                                        <i class="fas fa-truck"></i>${props.vehiculo}
                                    </span>
                                ` : ''}
                                ${props.chofer && props.chofer !== 'Sin asignar' ? `
                                    <span class="servicio-info-item">
                                        <i class="fas fa-user-circle"></i>${props.chofer}
                                    </span>
                                ` : ''}
                                ${props.ayudantes && props.ayudantes.length > 0 ? `
                                    <span class="servicio-info-item">
                                        <i class="fas fa-users"></i>${props.ayudantes.join(', ')}
                                    </span>
                                ` : ''}
                            </div>
                            
                            <div class="mt-2 text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                ${props.origen} → ${props.destino}
                            </div>
                        </div>
                    `;
                });
                
                html += `
                    <div class="text-center mt-3 pt-3 border-top">
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
            
            new bootstrap.Modal(document.getElementById('diaModal')).show();
        },
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
                    new bootstrap.Modal(document.getElementById('disponibilidadModal')).show();
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
        contentHeight: 750,
        dayMaxEvents: 3,
        moreLinkText: function(n) {
            return `+ ${n} más`;
        }
    });
    
    calendar.render();
    console.log('✅ Calendario renderizado');

    document.getElementById('btn-filtrar').addEventListener('click', function() {
        calendar.refetchEvents();
    });

    setInterval(function() {
        calendar.refetchEvents();
    }, 30000);
});
</script>
@endpush