@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card { transition: transform 0.2s; border: none; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .stat-icon { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    .bg-soft-primary { background: rgba(13, 110, 253, 0.1); }
    .bg-soft-success { background: rgba(25, 135, 84, 0.1); }
    .bg-soft-warning { background: rgba(255, 193, 7, 0.15); }
    .bg-soft-danger { background: rgba(220, 53, 69, 0.1); }
    .bg-soft-info { background: rgba(13, 202, 240, 0.1); }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-pie me-2 text-primary"></i>
            Hola, {{ auth()->user()->name }}
        </h1>
        <div class="text-muted small">
            <i class="fas fa-clock me-1"></i>
            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </div>
    </div>

    <div class="row g-3 mb-4">

        @if($esAdmin)
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Total Servicios</p>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalServiciosGlobal) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Ingresos</p>
                        <h2 class="mb-0 fw-bold text-success">{{ number_format($totalIngresos, 2) }} <small>Bs</small></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Clientes</p>
                        <h2 class="mb-0 fw-bold text-info">{{ number_format($clientesActivos) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Pendientes Pago</p>
                        <h2 class="mb-0 fw-bold text-danger">{{ number_format($pendientesPago) }}</h2>
                        <small class="text-muted">{{ number_format($montoDeudas, 2) }} Bs</small>
                    </div>
                </div>
            </div>
        @endif

        @if($esRecepcionista)
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Servicios Hoy</p>
                        <h2 class="mb-0 fw-bold text-primary">{{ $serviciosEnCamino + $serviciosEnProgreso }}</h2>
                        <small class="text-muted">en curso o por empezar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Servicios del Mes</p>
                        <h2 class="mb-0 fw-bold text-info">{{ $serviciosMes }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">En Progreso</p>
                        <h2 class="mb-0 fw-bold text-warning">{{ $serviciosEnProgreso }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Clientes Registrados</p>
                        <h2 class="mb-0 fw-bold text-success">{{ $clientesRegistrados }}</h2>
                    </div>
                </div>
            </div>
        @endif

        @if($esChofer || $esAyudante)
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Hoy</p>
                        <h2 class="mb-0 fw-bold text-primary">{{ $serviciosEnCamino + $serviciosEnProgreso }}</h2>
                        <small class="text-muted">tareas asignadas hoy</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">En Progreso</p>
                        <h2 class="mb-0 fw-bold text-warning">{{ $serviciosEnProgreso }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Finalizados Hoy</p>
                        <h2 class="mb-0 fw-bold text-success">{{ $serviciosFinalizadosHoy }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Este Mes</p>
                        <h2 class="mb-0 fw-bold text-info">{{ $serviciosMes }}</h2>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($esAdmin)
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><h6 class="mb-0">Servicios por Estado</h6></div>
                <div class="card-body"><canvas id="graficaEstados" style="max-height:280px;"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><h6 class="mb-0">Estado de Pagos</h6></div>
                <div class="card-body"><canvas id="graficaPagos" style="max-height:280px;"></canvas></div>
            </div>
        </div>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Ingresos últimos 6 meses</h6></div>
                <div class="card-body"><canvas id="graficaIngresos" style="max-height:280px;"></canvas></div>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-truck-moving me-2 text-warning"></i>Servicios en Curso</h6>
                    <span class="badge bg-warning text-dark">{{ $serviciosEnCurso->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($serviciosEnCurso->count() > 0)
                        <table class="table table-hover mb-0" style="font-size:0.875rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th><th>Cliente</th><th>Destino</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serviciosEnCurso as $s)
                                <tr>
                                    <td><strong>#{{ $s->id }}</strong></td>
                                    <td>{{ Str::limit($s->cliente->nombre_completo ?? 'N/A', 20) }}</td>
                                    <td>{{ Str::limit($s->destino, 25) }}</td>
                                    <td>
                                        <a href="{{ route('gps.seguimiento', $s) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-muted py-4">No hay servicios en curso</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Próximos Servicios</h6>
                    <span class="badge bg-primary">{{ $proximosServicios->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($proximosServicios->count() > 0)
                        <table class="table table-hover mb-0" style="font-size:0.875rem;">
                            <thead class="table-light">
                                <tr><th>#</th><th>Cliente</th><th>Fecha</th><th>Hora</th></tr>
                            </thead>
                            <tbody>
                                @foreach($proximosServicios as $s)
                                <tr>
                                    <td><strong>#{{ $s->id }}</strong></td>
                                    <td>{{ Str::limit($s->cliente->nombre_completo ?? 'N/A', 20) }}</td>
                                    <td>{{ $s->fecha_servicio->format('d/m') }}</td>
                                    <td>
                                        @if($s->hora_inicio)
                                            <span class="badge bg-secondary">
                                                {{ \Carbon\Carbon::parse($s->hora_inicio)->format('H:i') }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-muted py-4">No hay próximos servicios</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($esAdmin)
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Clientes</h6></div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($topClientes as $i => $c)
                            <div class="list-group-item d-flex align-items-center">
                                <span class="badge bg-light text-dark me-3">#{{ $i+1 }}</span>
                                <div class="flex-grow-1">
                                    <strong>{{ $c->nombre_completo }}</strong>
                                    <br><small class="text-muted">{{ $c->telefono }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $c->servicios_count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="fas fa-medal me-2 text-primary"></i>Ranking Choferes</h6></div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($rankingChoferes as $i => $c)
                            <div class="list-group-item d-flex align-items-center">
                                <span class="badge bg-light text-dark me-3">#{{ $i+1 }}</span>
                                <div class="flex-grow-1">
                                    <strong>{{ $c->nombre_completo }}</strong>
                                    <br><small class="text-muted">Lic. {{ $c->licencia }}</small>
                                </div>
                                <span class="badge bg-success">{{ $c->servicios_count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white"><h6 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Acciones Rápidas</h6></div>
        <div class="card-body">
            <div class="row g-2">
                @if($esAdmin || $esRecepcionista)
                    <div class="col-md-3">
                        <a href="{{ route('servicios.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-1"></i> Nuevo Servicio
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('clientes.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-plus me-1"></i> Nuevo Cliente
                        </a>
                    </div>
                @endif
                <div class="col-md-3">
                    <a href="{{ route('calendario.index') }}" class="btn btn-outline-info w-100">
                        <i class="fas fa-calendar-alt me-1"></i> Calendario
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('gps.index') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-map-marked-alt me-1"></i> Seguimiento
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($esAdmin)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('graficaEstados'), {
        type: 'bar',
        data: {
            labels: ['Pendiente','Confirmado','En Progreso','Finalizado','Cancelado'],
            datasets: [{
                data: [
                    {{ $serviciosPorEstado['pendiente'] }},
                    {{ $serviciosPorEstado['confirmado'] }},
                    {{ $serviciosPorEstado['en_progreso'] }},
                    {{ $serviciosPorEstado['finalizado'] }},
                    {{ $serviciosPorEstado['cancelado'] }}
                ],
                backgroundColor: ['#6c757d','#0d6efd','#ffc107','#198754','#dc3545'],
                borderRadius: 6
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('graficaPagos'), {
        type: 'doughnut',
        data: {
            labels: ['Pendiente','Pagado'],
            datasets: [{
                data: [{{ $serviciosPorPago['pendiente'] }}, {{ $serviciosPorPago['pagado'] }}],
                backgroundColor: ['#dc3545','#198754']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('graficaIngresos'), {
        type: 'line',
        data: {
            labels: [@foreach($ingresosPorMes as $i) '{{ $i->mes }}', @endforeach],
            datasets: [{
                label: 'Ingresos (Bs)',
                data: [@foreach($ingresosPorMes as $i) {{ $i->total }}, @endforeach],
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
});
</script>
@endpush
@endif