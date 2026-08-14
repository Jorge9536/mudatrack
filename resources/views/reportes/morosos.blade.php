@extends('layouts.app')

@section('title', 'Clientes Morosos')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('reportes.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Clientes Morosos</h1>
        <span class="badge bg-danger ms-2">{{ $morosos->count() }} clientes</span>
    </div>

    <!-- Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Total Morosos</p>
                    <h4 class="mb-0">{{ $morosos->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Deudas Pendientes</p>
                    <h4 class="mb-0">
                        {{ $morosos->sum(function($cliente) { return $cliente->deudas->where('estado', 'pendiente')->sum('monto'); }) }} Bs
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Total Deudas</p>
                    <h4 class="mb-0">{{ $morosos->sum(function($cliente) { return $cliente->deudas->count(); }) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Clientes Activos</p>
                    <h4 class="mb-0">{{ $morosos->where('bloqueado', false)->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de morosos -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-danger"></i>Lista de Clientes con Deudas</h6>
            <span class="badge bg-danger">{{ $morosos->count() }} morosos</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Deudas</th>
                            <th>Total Adeudado</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($morosos as $cliente)
                        @php
                            $deudasPendientes = $cliente->deudas->where('estado', 'pendiente');
                            $totalDeuda = $deudasPendientes->sum('monto');
                        @endphp
                        <tr>
                            <td>#{{ $cliente->id }}</td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $cliente->nombre_completo }}
                            </td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>
                                <span class="badge bg-danger">{{ $deudasPendientes->count() }}</span>
                            </td>
                            <td>
                                <strong class="text-danger">{{ number_format($totalDeuda, 2) }} Bs</strong>
                            </td>
                            <td>
                                @if($cliente->bloqueado)
                                    <span class="badge bg-danger">Bloqueado</span>
                                @else
                                    <span class="badge bg-warning text-dark">Activo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-success" onclick="verDeudas({{ $cliente->id }})">
                                    <i class="fas fa-list"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-3x d-block mb-2 text-success"></i>
                                <p>No hay clientes morosos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Total: {{ $morosos->count() }} clientes con deudas</span>
            </div>
        </div>
    </div>

    <!-- Modal de Deudas -->
    <div class="modal fade" id="modalDeudas" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title"><i class="fas fa-list me-2"></i>Detalle de Deudas</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalDeudasBody">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-spinner fa-spin me-2"></i> Cargando...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verDeudas(clienteId) {
    const modal = new bootstrap.Modal(document.getElementById('modalDeudas'));
    const body = document.getElementById('modalDeudasBody');
    
    // Mostrar loading
    body.innerHTML = `
        <div class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin me-2"></i> Cargando deudas...
        </div>
    `;
    modal.show();

    // Cargar deudas del cliente (simulado con datos del servidor)
    // Como los datos ya están en la vista, usamos los que tenemos
    // Esta es una simulación, en producción se haría una llamada AJAX
    setTimeout(() => {
        body.innerHTML = `
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Monto</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($morosos as $cliente)
                            @foreach($cliente->deudas->where('estado', 'pendiente') as $deuda)
                                <tr>
                                    <td>#{{ $deuda->servicio_id }}</td>
                                    <td>{{ number_format($deuda->monto, 2) }} Bs</td>
                                    <td>{{ $deuda->fecha_vencimiento->format('d/m/Y') }}</td>
                                    <td>
                                        @if($deuda->estaVencida())
                                            <span class="badge bg-danger">Vencida</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info mt-2">
                <i class="fas fa-info-circle me-2"></i>
                Total de deudas pendientes: {{ $morosos->sum(function($c) { return $c->deudas->where('estado', 'pendiente')->sum('monto'); }) }} Bs
            </div>
        `;
    }, 500);
}
</script>
@endpush
@endsection