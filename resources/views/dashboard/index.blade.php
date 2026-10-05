@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .chart-container {
        position: relative;
        height: 280px;
    }
    .table-compact td, .table-compact th {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    .estado-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    .card-header-clean {
        background: white;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem;
    }
    .bg-soft-primary { background: rgba(13, 110, 253, 0.1); }
    .bg-soft-success { background: rgba(25, 135, 84, 0.1); }
    .bg-soft-warning { background: rgba(255, 193, 7, 0.15); }
    .bg-soft-danger { background: rgba(220, 53, 69, 0.1); }
    .bg-soft-info { background: rgba(13, 202, 240, 0.1); }
    .bg-soft-secondary { background: rgba(108, 117, 125, 0.1); }
</style>
@endpush

@section('content')
<div class="container-fluid">
    
    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-pie me-2 text-primary"></i>Dashboard
        </h1>
        <div class="text-muted small">
            <i class="fas fa-clock me-1"></i>
            {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TARJETAS PRINCIPALES --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        
        {{-- Total Servicios --}}
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Total Servicios</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalServicios) }}</h2>
                            <small class="text-muted">todos los tiempos</small>
                        </div>
                        <div class="stat-icon bg-soft-primary">
                            <i class="fas fa-truck text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ingresos Totales --}}
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Ingresos Totales</p>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalIngresos, 2) }} <small>Bs</small></h2>
                            <small class="text-muted">servicios pagados</small>
                        </div>
                        <div class="stat-icon bg-soft-success">
                            <i class="fas fa-coins text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clientes Activos --}}
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Clientes Activos</p>
                            <h2 class="mb-0 fw-bold text-info">{{ number_format($clientesActivos) }}</h2>
                            <small class="text-muted">sin bloqueos</small>
                        </div>
                        <div class="stat-icon bg-soft-info">
                            <i class="fas fa-users text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendientes Pago --}}
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Pendientes Pago</p>
                            <h2 class="mb-0 fw-bold text-danger">{{ number_format($pendientesPago) }}</h2>
                            <small class="text-muted">{{ number_format($montoDeudas, 2) }} Bs en deuda</small>
                        </div>
                        <div class="stat-icon bg-soft-danger">
                            <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TARJETAS SECUNDARIAS --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-warning me-3" style="width:45px;height:45px;">
                            <i class="fas fa-spinner text-warning"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">En Progreso Ahora</p>
                            <h4 class="mb-0 fw-bold">{{ $serviciosEnProgreso }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-success me-3" style="width:45px;height:45px;">
                            <i class="fas fa-flag-checkered text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Finalizados Hoy</p>
                            <h4 class="mb-0 fw-bold">{{ $serviciosFinalizadosHoy }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-primary me-3" style="width:45px;height:45px;">
                            <i class="fas fa-calendar-check text-primary"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">En Camino Hoy</p>
                            <h4 class="mb-0 fw-bold">{{ $serviciosEnCamino }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-soft-success me-3" style="width:45px;height:45px;">
                            <i class="fas fa-money-bill-wave text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Ingresos del Mes</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($ingresosMes, 0) }} <small>Bs</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRÁFICAS --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        
        {{-- Gráfica 1: Servicios por Estado Operativo --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header-clean">
                    <h6 class="mb-0">
                        <i class="fas fa-tasks me-2 text-primary"></i>
                        Servicios por Estado
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="graficaEstados"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfica 2: Servicios por Estado de Pago --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header-clean">
                    <h6 class="mb-0">
                        <i class="fas fa-money-bill me-2 text-success"></i>
                        Estado de Pagos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="graficaPagos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfica 3: Ingresos por Mes --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header-clean">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2 text-success"></i>
                        Ingresos de los últimos 6 meses
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="graficaIngresos"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABLAS --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        
        {{-- Servicios en Curso --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-truck-moving me-2 text-warning"></i>
                        Servicios en Curso
                        <span class="badge bg-warning text-dark ms-2">{{ $serviciosEnCurso->count() }}</span>
                    </h6>
                    <a href="{{ route('servicios.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    @if($serviciosEnCurso->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-compact table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Chofer</th>
                                        <th>Destino</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviciosEnCurso as $servicio)
                                    <tr>
                                        <td><strong>#{{ $servicio->id }}</strong></td>
                                        <td>{{ Str::limit($servicio->cliente->nombre_completo ?? 'N/A', 20) }}</td>
                                        <td>
                                            @if($servicio->chofer)
                                                <i class="fas fa-user-circle text-primary me-1"></i>
                                                {{ Str::limit($servicio->chofer->nombre_completo, 15) }}
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($servicio->destino, 25) }}</td>
                                        <td>
                                            <a href="{{ route('gps.seguimiento', $servicio) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Ver seguimiento">
                                                <i class="fas fa-map-marked-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-truck fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0">No hay servicios en curso ahora</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Próximos Servicios --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        Próximos Servicios
                        <span class="badge bg-primary ms-2">{{ $proximosServicios->count() }}</span>
                    </h6>
                    <a href="{{ route('calendario.index') }}" class="btn btn-sm btn-outline-primary">Ver calendario</a>
                </div>
                <div class="card-body p-0">
                    @if($proximosServicios->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-compact table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proximosServicios as $servicio)
                                    <tr>
                                        <td><strong>#{{ $servicio->id }}</strong></td>
                                        <td>{{ Str::limit($servicio->cliente->nombre_completo ?? 'N/A', 20) }}</td>
                                        <td>{{ $servicio->fecha_servicio->format('d/m') }}</td>
                                        <td>
                                            @if($servicio->hora_inicio)
                                                <span class="badge bg-secondary">
                                                    {{ \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badge = [
                                                    'pendiente' => 'secondary',
                                                    'confirmado' => 'primary',
                                                ][$servicio->estado] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badge }} estado-badge">
                                                {{ $servicio->estado_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-check fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0">No hay servicios próximos</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TOP CLIENTES Y RANKING CHOFERES --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        
        {{-- Top Clientes --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header-clean">
                    <h6 class="mb-0">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        Top 5 Clientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($topClientes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topClientes as $index => $cliente)
                                <div class="list-group-item d-flex align-items-center">
                                    <div class="me-3">
                                        @if($index === 0)
                                            <span class="badge bg-warning text-dark" style="font-size:1rem; padding:8px 12px;">🥇</span>
                                        @elseif($index === 1)
                                            <span class="badge bg-secondary" style="font-size:1rem; padding:8px 12px;">🥈</span>
                                        @elseif($index === 2)
                                            <span class="badge bg-danger" style="font-size:1rem; padding:8px 12px;">🥉</span>
                                        @else
                                            <span class="badge bg-light text-dark" style="font-size:1rem; padding:8px 12px;">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $cliente->nombre_completo }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>{{ $cliente->telefono }}
                                        </small>
                                    </div>
                                    <span class="badge bg-primary">
                                        {{ $cliente->servicios_count }} servicios
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0">No hay datos de clientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ranking Choferes --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header-clean">
                    <h6 class="mb-0">
                        <i class="fas fa-medal me-2 text-primary"></i>
                        Ranking de Choferes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($rankingChoferes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($rankingChoferes as $index => $chofer)
                                <div class="list-group-item d-flex align-items-center">
                                    <div class="me-3">
                                        @if($index === 0)
                                            <span class="badge bg-warning text-dark" style="font-size:1rem; padding:8px 12px;">🥇</span>
                                        @elseif($index === 1)
                                            <span class="badge bg-secondary" style="font-size:1rem; padding:8px 12px;">🥈</span>
                                        @elseif($index === 2)
                                            <span class="badge bg-danger" style="font-size:1rem; padding:8px 12px;">🥉</span>
                                        @else
                                            <span class="badge bg-light text-dark" style="font-size:1rem; padding:8px 12px;">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $chofer->nombre_completo }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            Lic. {{ $chofer->licencia }}
                                        </small>
                                    </div>
                                    <span class="badge bg-success">
                                        {{ $chofer->servicios_count }} completados
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-circle fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0">No hay datos de choferes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACCIONES RÁPIDAS --}}
    {{-- ============================================ --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header-clean">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
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
                        <div class="col-md-3">
                            <a href="{{ route('calendario.index') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-calendar-alt me-1"></i> Ver Calendario
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('gps.admin.mapa') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-map-marked-alt me-1"></i> Ver Mapa GPS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // GRÁFICA 1: Servicios por Estado (Barras)
    // ============================================
    const ctxEstados = document.getElementById('graficaEstados').getContext('2d');
    new Chart(ctxEstados, {
        type: 'bar',
        data: {
            labels: ['Pendiente', 'Confirmado', 'En Progreso', 'Finalizado', 'Cancelado'],
            datasets: [{
                label: 'Servicios',
                data: [
                    {{ $serviciosPorEstado['pendiente'] }},
                    {{ $serviciosPorEstado['confirmado'] }},
                    {{ $serviciosPorEstado['en_progreso'] }},
                    {{ $serviciosPorEstado['finalizado'] }},
                    {{ $serviciosPorEstado['cancelado'] }}
                ],
                backgroundColor: [
                    'rgba(108, 117, 125, 0.7)',
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgb(108, 117, 125)',
                    'rgb(13, 110, 253)',
                    'rgb(255, 193, 7)',
                    'rgb(25, 135, 84)',
                    'rgb(220, 53, 69)'
                ],
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // ============================================
    // GRÁFICA 2: Estado de Pagos (Dona)
    // ============================================
    const ctxPagos = document.getElementById('graficaPagos').getContext('2d');
    new Chart(ctxPagos, {
        type: 'doughnut',
        data: {
            labels: ['Pendiente de Pago', 'Pagado'],
            datasets: [{
                data: [
                    {{ $serviciosPorPago['pendiente'] }},
                    {{ $serviciosPorPago['pagado'] }}
                ],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(25, 135, 84, 0.8)'
                ],
                borderColor: [
                    'rgb(220, 53, 69)',
                    'rgb(25, 135, 84)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 13 }
                    }
                }
            }
        }
    });

    // ============================================
    // GRÁFICA 3: Ingresos por Mes (Línea)
    // ============================================
    const ctxIngresos = document.getElementById('graficaIngresos').getContext('2d');
    new Chart(ctxIngresos, {
        type: 'line',
        data: {
            labels: [
                @foreach($ingresosPorMes as $item)
                    '{{ $item->mes }}',
                @endforeach
            ],
            datasets: [{
                label: 'Ingresos (Bs)',
                data: [
                    @foreach($ingresosPorMes as $item)
                        {{ $item->total }},
                    @endforeach
                ],
                borderColor: 'rgb(25, 135, 84)',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(25, 135, 84)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ingresos: ' + new Intl.NumberFormat('es-BO').format(context.parsed.y) + ' Bs';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('es-BO').format(value) + ' Bs';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush