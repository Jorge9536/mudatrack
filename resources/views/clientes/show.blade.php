@extends('layouts.app')

@section('title', 'Ver Cliente')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('clientes.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-user me-2 text-primary"></i>Cliente: {{ $cliente->nombre_completo }}</h1>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Datos Personales</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nombre</span>
                        <strong>{{ $cliente->nombre_completo }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Teléfono</span>
                        <strong>{{ $cliente->telefono }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Dirección</span>
                        <strong>{{ $cliente->direccion ?? 'No registrada' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        @if($cliente->bloqueado)
                            <span class="badge bg-danger">Bloqueado</span>
                        @else
                            <span class="badge bg-success">Activo</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Moroso</span>
                        @if($cliente->estaBloqueado())
                            <span class="badge bg-warning text-dark">Sí</span>
                        @else
                            <span class="badge bg-success">No</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Historial de Servicios</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Origen → Destino</th>
                                    <th>Fecha</th>
                                    <th>Costo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cliente->servicios as $servicio)
                                <tr>
                                    <td>{{ $servicio->id }}</td>
                                    <td>{{ $servicio->origen }} → {{ $servicio->destino }}</td>
                                    <td>{{ $servicio->fecha_servicio->format('d/m/Y') }}</td>
                                    <td>{{ number_format($servicio->costo_total, 2) }} Bs</td>
                                    <td>
                                        <span class="badge bg-{{ $servicio->estado === 'pagado' ? 'success' : 'warning' }}">
                                            {{ ucfirst($servicio->estado) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No tiene servicios</td>
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