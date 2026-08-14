@extends('layouts.app')

@section('title', 'Ver Vehículo')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('vehiculos.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-truck me-2 text-primary"></i>Vehículo: {{ $vehiculo->placa }}</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Datos del Vehículo</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Placa</span>
                        <strong>{{ $vehiculo->placa }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Marca</span>
                        <strong>{{ $vehiculo->marca }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Modelo</span>
                        <strong>{{ $vehiculo->modelo }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tipo</span>
                        <strong>{{ $vehiculo->tipo_label }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Capacidad</span>
                        <strong>{{ $vehiculo->capacidad_kg }} kg</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        @if($vehiculo->disponible)
                            <span class="badge bg-success">Disponible</span>
                        @else
                            <span class="badge bg-danger">Ocupado</span>
                        @endif
                    </div>
                    @if($vehiculo->observaciones)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Observaciones</span>
                        <strong>{{ $vehiculo->observaciones }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Historial de Servicios</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehiculo->servicios as $servicio)
                                <tr>
                                    <td>{{ $servicio->id }}</td>
                                    <td>{{ $servicio->cliente->nombre_completo ?? 'N/A' }}</td>
                                    <td>{{ $servicio->fecha_servicio->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $servicio->estado === 'pagado' ? 'success' : 'warning' }}">
                                            {{ $servicio->estado_label }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No tiene servicios</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection