@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-chart-pie me-2 text-primary"></i>Dashboard</h1>
    
    <!-- Tarjetas de estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Total Servicios</p>
                            <h3 class="mb-0">{{ $totalServicios ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-truck text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Ingresos Totales</p>
                            <h3 class="mb-0">{{ isset($totalIngresos) ? number_format($totalIngresos, 2) : '0.00' }} Bs</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-coins text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Clientes Activos</p>
                            <h3 class="mb-0">{{ $clientesActivos ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users text-info fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Pendientes Pago</p>
                            <h3 class="mb-0 text-danger">{{ $pendientesPago ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de servicios y acciones -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Servicios por Estado</h6>
                </div>
                <div class="card-body">
                    @if(isset($serviciosPorEstado) && count($serviciosPorEstado) > 0)
                        <div class="row g-3">
                            @foreach($serviciosPorEstado as $estado => $cantidad)
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                                        <strong>{{ $cantidad }}</strong>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        @php
                                            $max = max($serviciosPorEstado);
                                            $porcentaje = $max > 0 ? ($cantidad / $max) * 100 : 0;
                                            $color = match($estado) {
                                                'pendiente' => 'secondary',
                                                'confirmado' => 'primary',
                                                'en_progreso' => 'warning',
                                                'finalizado' => 'success',
                                                'cancelado' => 'danger',
                                                'pendiente_pago' => 'danger',
                                                'pagado' => 'success',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-simple fa-3x mb-2 d-block"></i>
                            <p>No hay datos de servicios registrados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Nuevo Servicio
                        </a>
                        <a href="{{ route('clientes.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-1"></i> Nuevo Cliente
                        </a>
                        <a href="{{ route('reportes.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-file-alt me-1"></i> Ver Reportes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Servicios Recientes -->
            @if(isset($serviciosRecientes) && $serviciosRecientes->count() > 0)
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Servicios Recientes</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($serviciosRecientes as $servicio)
                            <a href="{{ route('servicios.show', $servicio) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">#{{ $servicio->id }}</small>
                                        <span class="ms-2">{{ $servicio->cliente->nombre_completo ?? 'Sin cliente' }}</span>
                                    </div>
                                    <span class="badge bg-{{ $servicio->estado === 'pagado' ? 'success' : 'warning' }}">
                                        {{ $servicio->estado_label ?? $servicio->estado }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $servicio->origen }} → {{ $servicio->destino }}</small>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection