@extends('layouts.app')

@section('title', 'Seguimiento GPS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Seguimiento GPS</h1>
            <small class="text-muted">Servicios en tiempo real</small>
        </div>
        <span class="badge bg-success">
            <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> 
            {{ $servicios->total() }} activos
        </span>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('gps.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Buscar</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cliente o destino..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="en_progreso" {{ request('estado') == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ request('fecha', date('Y-m-d')) }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">En Progreso</p>
                    <h5 class="mb-0 text-primary">
                        {{ $servicios->where('estado', 'en_progreso')->count() }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Confirmados</p>
                    <h5 class="mb-0 text-info">
                        {{ $servicios->where('estado', 'confirmado')->count() }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Pendientes</p>
                    <h5 class="mb-0 text-warning">
                        {{ $servicios->where('estado', 'pendiente')->count() }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Total Activos</p>
                    <h5 class="mb-0 text-success">
                        {{ $servicios->total() }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de servicios -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Servicios Activos</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Origen → Destino</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Chofer</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $servicio)
                        <tr>
                            <td>#{{ $servicio->id }}</td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $servicio->cliente->nombre_completo }}
                            </td>
                            <td>{{ $servicio->origen }} → {{ $servicio->destino }}</td>
                            <td>{{ $servicio->fecha_servicio->format('d/m/Y') }}</td>
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
                                @if($servicio->chofer)
                                    {{ $servicio->chofer->nombre_completo }}
                                @else
                                    <span class="text-muted">No asignado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('gps.seguimiento', $servicio) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-map-marked-alt me-1"></i> Ver Mapa
                                </a>
                                <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-map fa-3x d-block mb-2"></i>
                                <p>No hay servicios activos para seguimiento</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $servicios->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection