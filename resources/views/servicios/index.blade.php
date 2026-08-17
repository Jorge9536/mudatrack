@extends('layouts.app')

@section('title', 'Servicios')

@push('styles')
<style>
    /* Reducir el tamaño de la paginación */
    .pagination-sm .page-link {
        padding: 0.15rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    .pagination .page-link {
        padding: 0.15rem 0.6rem;
        font-size: 0.8rem;
    }
    
    /* Ocultar elementos en móviles si es necesario */
    @media (max-width: 576px) {
        .pagination .page-item:not(.active):not(.prev):not(.next) .page-link {
            display: none;
        }
        .pagination .page-item.active .page-link,
        .pagination .page-item.prev .page-link,
        .pagination .page-item.next .page-link {
            display: block;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Gestión de Servicios</h1>
        <a href="{{ route('servicios.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Servicio
        </a>
    </div>

    <!-- Tarjetas resumen -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-secondary">
                <div class="card-body py-2">
                    <h6 class="card-title">Pendientes</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'pendiente')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body py-2">
                    <h6 class="card-title">Confirmados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'confirmado')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body py-2">
                    <h6 class="card-title">En Progreso</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'en_progreso')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body py-2">
                    <h6 class="card-title">Finalizados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'finalizado')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body py-2">
                    <h6 class="card-title">Pendiente Pago</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'pendiente_pago')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body py-2">
                    <h6 class="card-title">Cancelados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'cancelado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Origen → Destino</th>
                            <th>Fecha</th>
                            <th>Costo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servicios as $servicio)
                        <tr>
                            <td><strong>#{{ $servicio->id }}</strong></td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $servicio->cliente->nombre_completo }}
                            </td>
                            <td>{{ $servicio->origen }} → {{ $servicio->destino }}</td>
                            <td>{{ $servicio->fecha_servicio->format('d/m/Y') }}</td>
                            <td><strong>{{ number_format($servicio->costo_total, 2) }} Bs</strong></td>
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
                                    {{ ucfirst(str_replace('_', ' ', $servicio->estado)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-warning" onclick="cambiarEstado({{ $servicio->id }})">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <a href="{{ route('servicios.comprobante', $servicio) }}" class="btn btn-sm btn-success" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No hay servicios registrados
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
                    <i class="fas fa-info-circle me-1"></i>
                    Mostrando {{ $servicios->firstItem() ?? 0 }} - {{ $servicios->lastItem() ?? 0 }} de {{ $servicios->total() }} servicios
                </span>
                <div>
                    {{ $servicios->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cambiarEstado(servicioId) {
    const estados = ['pendiente', 'confirmado', 'en_progreso', 'finalizado', 'cancelado'];
    let options = estados.map((e, i) => `${i+1}. ${e.replace('_', ' ')}`).join('\n');
    const nuevoEstado = prompt('Seleccione nuevo estado:\n' + options);
    
    if (nuevoEstado) {
        const idx = parseInt(nuevoEstado) - 1;
        if (idx >= 0 && idx < estados.length) {
            fetch(`/servicios/${servicioId}/estado`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ estado: estados[idx] })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al cambiar estado');
                }
            })
            .catch(() => alert('Error de conexión'));
        }
    }
}
</script>
@endpush
@endsection