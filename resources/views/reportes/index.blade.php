@extends('layouts.app')

@section('title', 'Reportes')

@push('styles')
<style>
    .pagination { margin-bottom: 0; }
    .pagination .page-link { padding: 0.25rem 0.6rem; font-size: 0.85rem; }
    .stat-card { border-left: 4px solid; }
    .stat-card.primary { border-color: #0d6efd; }
    .stat-card.success { border-color: #198754; }
    .stat-card.danger  { border-color: #dc3545; }
    .stat-card.warning { border-color: #ffc107; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Reportes</h1>
            <small class="text-muted">Análisis de servicios</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('reportes.personal', request()->query()) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-users me-1"></i> Por Personal
            </a>
            <a href="{{ route('reportes.morosos') }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-exclamation-triangle me-1"></i> Morosos
            </a>
            <a href="{{ route('reportes.exportar', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Exportar
            </a>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card stat-card primary shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Total Servicios</p>
                    <h4 class="mb-0">{{ $servicios->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card success shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Ingresos</p>
                    <h4 class="mb-0 text-success">{{ number_format($totalRecaudado, 2) }} Bs</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card danger shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Pendiente</p>
                    <h4 class="mb-0 text-danger">{{ number_format($totalPendiente, 2) }} Bs</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card warning shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Morosos</p>
                    <h4 class="mb-0 text-warning">{{ $clientesMorosos }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.index') }}" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ $fechaFin }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cliente</label>
                    <select name="cliente_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Servicio::ESTADOS_LABEL as $k => $v)
                            <option value="{{ $k }}" {{ request('estado') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Pago</label>
                    <select name="estado_pago" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado_pago') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado" {{ request('estado_pago') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 small"><i class="fas fa-list me-2 text-primary"></i>Detalle</h6>
            <span class="badge bg-primary">{{ $servicios->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.875rem;">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Origen → Destino</th>
                            <th>Fecha</th>
                            <th class="text-end">Monto</th>
                            <th>Estado</th>
                            <th>Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $s)
                        <tr>
                            <td>#{{ $s->id }}</td>
                            <td>{{ $s->cliente->nombre_completo ?? '—' }}</td>
                            <td>{{ Str::limit($s->origen, 20) }} → {{ Str::limit($s->destino, 20) }}</td>
                            <td>{{ optional($s->fecha_servicio)->format('d/m/Y') }}</td>
                            <td class="text-end"><strong>{{ number_format($s->costo_total, 2) }} Bs</strong></td>
                            <td>
                                @php
                                    $bc = [
                                        'pendiente' => 'secondary', 'confirmado' => 'primary',
                                        'en_progreso' => 'warning', 'finalizado' => 'success',
                                        'cancelado' => 'danger',
                                    ][$s->estado] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $bc }}">{{ $s->estado_label ?? $s->estado }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $s->estado_pago === 'pagado' ? 'success' : 'danger' }}">
                                    {{ ucfirst($s->estado_pago) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-2x d-block mb-2 opacity-50"></i>
                                No hay servicios
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
                    {{ $servicios->firstItem() ?? 0 }} - {{ $servicios->lastItem() ?? 0 }} de {{ $servicios->total() }}
                </span>
                {{ $servicios->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection