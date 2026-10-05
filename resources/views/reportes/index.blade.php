@extends('layouts.app')

@section('title', 'Reportes')

@push('styles')
<style>
    .pagination-sm .page-link {
        padding: 0.15rem 0.5rem;
        font-size: 0.8rem;
    }
    .pagination { margin-bottom: 0; }
    .pagination .page-link { padding: 0.15rem 0.6rem; font-size: 0.8rem; }
    @media (max-width: 576px) {
        .pagination .page-item:not(.active):not(.prev):not(.next) .page-link { display: none; }
        .pagination .page-item.active .page-link,
        .pagination .page-item.prev .page-link,
        .pagination .page-item.next .page-link { display: block; }
    }
    .card-footer .text-muted { font-size: 0.8rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Reportes</h1>
            <small class="text-muted">Análisis y estadísticas de servicios</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reportes.exportar') }}" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-1"></i> Exportar
            </a>
            <a href="{{ route('reportes.morosos') }}" class="btn btn-outline-danger">
                <i class="fas fa-exclamation-triangle me-1"></i> Morosos
            </a>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Total Servicios</p>
                    <h4 class="mb-0">{{ $servicios->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Ingresos Totales</p>
                    <h4 class="mb-0">{{ number_format($totalRecaudado, 2) }} Bs</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Pendientes de Pago</p>
                    <h4 class="mb-0">{{ number_format($totalPendiente, 2) }} Bs</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Clientes Morosos</p>
                    <h4 class="mb-0">{{ $clientesMorosos }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.index') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" 
                           value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" 
                           value="{{ $fechaFin }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Cliente</label>
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Estado Servicio</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Servicio::ESTADOS_LABEL as $key => $label)
                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Estado Pago</label>
                    <select name="estado_pago" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado_pago') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ request('estado_pago') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-grow-1" title="Filtrar">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary" title="Limpiar">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-3 mb-4">
        <!-- Gráfico de Estados Operativos -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Servicios por Estado</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @php
                            $totalEstados = array_sum($estadisticas);
                        @endphp
                        @foreach($estadisticas as $estado => $cantidad)
                            @php
                                $porcentaje = $totalEstados > 0 ? round(($cantidad / $totalEstados) * 100) : 0;
                                $colors = [
                                    'pendiente' => 'secondary',
                                    'confirmado' => 'primary',
                                    'en_progreso' => 'warning',
                                    'finalizado' => 'success',
                                    'cancelado' => 'danger',
                                ];
                                $bgColor = $colors[$estado] ?? 'secondary';
                            @endphp
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between">
                                    <span>{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                                    <span><strong>{{ $cantidad }}</strong> ({{ $porcentaje }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $bgColor }}" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Estados de Pago -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-money-bill me-2 text-success"></i>Estados de Pago</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @php
                            $totalPagos = array_sum($estadisticasPago);
                        @endphp
                        @foreach($estadisticasPago as $estadoPago => $cantidad)
                            @php
                                $porcentaje = $totalPagos > 0 ? round(($cantidad / $totalPagos) * 100) : 0;
                                $bgColor = $estadoPago === 'pagado' ? 'success' : 'danger';
                                $icono = $estadoPago === 'pagado' ? 'check-circle' : 'clock';
                            @endphp
                            <div class="col-md-12 mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>
                                        <i class="fas fa-{{ $icono }} text-{{ $bgColor }} me-2"></i>
                                        {{ ucfirst($estadoPago) }}
                                    </span>
                                    <span><strong>{{ $cantidad }}</strong> ({{ $porcentaje }}%)</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-{{ $bgColor }}" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios por día -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Servicios por Día (últimos 7 días)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($dias as $fecha => $total)
                            <div class="col-md-3 col-6">
                                <div class="p-2 bg-light rounded text-center">
                                    <div class="small text-muted">
                                        {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('ddd D') }}
                                    </div>
                                    <div class="h4 mb-0">{{ $total }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de servicios -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Detalle de Servicios</h6>
            <span class="badge bg-primary">{{ $servicios->total() }} registros</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tabla-reportes">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Origen → Destino</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Estado Servicio</th>
                            <th>Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $servicio)
                        <tr>
                            <td>#{{ $servicio->id }}</td>
                            <td>{{ $servicio->cliente->nombre_completo }}</td>
                            <td>
                                <span title="{{ $servicio->origen }} → {{ $servicio->destino }}">
                                    {{ Str::limit($servicio->origen, 25) }} → {{ Str::limit($servicio->destino, 25) }}
                                </span>
                            </td>
                            <td>{{ $servicio->fecha_servicio->format('d/m/Y') }}</td>
                            <td><strong>{{ number_format($servicio->costo_total, 2) }} Bs</strong></td>
                            
                            {{-- ESTADO OPERATIVO --}}
                            <td>
                                @php
                                    $badgeClass = [
                                        'pendiente' => 'secondary',
                                        'confirmado' => 'primary',
                                        'en_progreso' => 'warning',
                                        'finalizado' => 'success',
                                        'cancelado' => 'danger',
                                    ][$servicio->estado] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ $servicio->estado_label }}
                                </span>
                            </td>
                            
                            {{-- ESTADO DE PAGO --}}
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
                                <a href="{{ route('servicios.comprobante', $servicio) }}" class="btn btn-sm btn-success" target="_blank" title="PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-3x d-block mb-2 opacity-50"></i>
                                <p>No hay servicios en el rango seleccionado</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($servicios->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>TOTAL</strong></td>
                            <td><strong>{{ number_format($servicios->sum('costo_total'), 2) }} Bs</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Mostrando {{ $servicios->firstItem() ?? 0 }} - {{ $servicios->lastItem() ?? 0 }} de {{ $servicios->total() }} registros
                </span>
                <div>
                    {{ $servicios->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection