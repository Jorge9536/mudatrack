@extends('layouts.app')

@section('title', 'Ver Chofer')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('choferes.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-user-circle me-2 text-primary"></i>Chofer: {{ $chofer->nombre_completo }}</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Datos del Chofer</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nombre</span>
                        <strong>{{ $chofer->nombre_completo }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Teléfono</span>
                        <strong>{{ $chofer->telefono }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Licencia</span>
                        <strong>{{ $chofer->licencia }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        @if($chofer->disponible)
                            <span class="badge bg-success">Disponible</span>
                        @else
                            <span class="badge bg-danger">Ocupado</span>
                        @endif
                    </div>
                    @if($chofer->observaciones)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Observaciones</span>
                        <strong>{{ $chofer->observaciones }}</strong>
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
                                    <th>Origen → Destino</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chofer->servicios as $servicio)
                                <tr>
                                    <td>{{ $servicio->id }}</td>
                                    <td>{{ $servicio->cliente->nombre_completo ?? 'N/A' }}</td>
                                    <td>{{ $servicio->origen }} → {{ $servicio->destino }}</td>
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