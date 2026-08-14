@extends('layouts.app')

@section('title', 'Reportes')

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
                <div class="col-md-3">
                    <label class="form-label small">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" 
                           value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" 
                           value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
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
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráfico de estados -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Distribución por Estado</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($estadisticas as $estado => $cantidad)
                            @php
                                $total = $servicios->total();
                                $porcentaje = $total > 0 ? round(($cantidad / $total) * 100) : 0;
                                $colors = [
                                    'pendiente' => 'secondary',
                                    'confirmado' => 'primary',
                                    'en_progreso' => 'warning',
                                    'finalizado' => 'success',
                                    'cancelado' => 'danger',
                                    'pendiente_pago' => 'danger',
                                    'pagado' => 'success'
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
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Servicios por Día</h6>
                </div>
                <div class="card-body">
                    @php
                        use App\Models\Servicio;
                        $dias = collect();
                        for ($i = 6; $i >= 0; $i--) {
                            $fecha = now()->subDays($i)->format('Y-m-d');
                            $dias->put($fecha, 0);
                        }
                        $serviciosPorDia = Servicio::whereBetween('created_at', [
                            now()->subDays(7)->startOfDay(),
                            now()->endOfDay()
                        ])->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
                          ->groupBy('fecha')
                          ->pluck('total', 'fecha');
                        $dias = $dias->merge($serviciosPorDia);
                    @endphp
                    <div class="row g-2">
                        @foreach($dias as $fecha => $total)
                            <div class="col-md-3 text-center">
                                <div class="p-2 bg-light rounded">
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($fecha)->format('D') }}</div>
                                    <div class="h5 mb-0">{{ $total }}</div>
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
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $servicio)
                        <tr>
                            <td>#{{ $servicio->id }}</td>
                            <td>{{ $servicio->cliente->nombre_completo }}</td>
                            <td>{{ $servicio->origen }} → {{ $servicio->destino }}</td>
                            <td>{{ $servicio->fecha_servicio->format('d/m/Y') }}</td>
                            <td>{{ number_format($servicio->costo_total, 2) }} Bs</td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'pendiente' => 'secondary',
                                        'confirmado' => 'primary',
                                        'en_progreso' => 'warning',
                                        'finalizado' => 'success',
                                        'cancelado' => 'danger',
                                        'pendiente_pago' => 'danger',
                                        'pagado' => 'success'
                                    ][$servicio->estado] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ $servicio->estado_label }}
                                </span>
                            </td>
                            <td>
                                @if($servicio->estado === 'pagado')
                                    <span class="badge bg-success">Pagado</span>
                                @elseif($servicio->estado === 'pendiente_pago')
                                    <span class="badge bg-danger">Deuda</span>
                                @else
                                    <span class="badge bg-secondary">Pendiente</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('servicios.comprobante', $servicio) }}" class="btn btn-sm btn-success" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-3x d-block mb-2"></i>
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
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="text-muted small">Mostrando {{ $servicios->count() }} de {{ $servicios->total() }} registros</span>
            {{ $servicios->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection