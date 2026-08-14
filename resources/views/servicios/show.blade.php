@extends('layouts.app')

@section('title', 'Ver Servicio')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('servicios.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-tasks me-2 text-primary"></i>Servicio #{{ $servicio->id }}</h1>
        <span class="badge bg-secondary ms-2">{{ $servicio->estado_label }}</span>
    </div>

    <div class="row">
        <!-- Información del Servicio -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Información del Servicio</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Cliente</div>
                        <div class="col-8"><strong>{{ $servicio->cliente->nombre_completo }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Teléfono</div>
                        <div class="col-8"><strong>{{ $servicio->cliente->telefono }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Origen</div>
                        <div class="col-8"><strong>{{ $servicio->origen }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Destino</div>
                        <div class="col-8"><strong>{{ $servicio->destino }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Fecha</div>
                        <div class="col-8"><strong>{{ $servicio->fecha_servicio->format('d/m/Y') }}</strong></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Estado</div>
                        <div class="col-8">
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
                            <span class="badge bg-{{ $badgeClass }}">{{ $servicio->estado_label }}</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Costo Total</div>
                        <div class="col-8"><strong class="text-primary h5">{{ number_format($servicio->costo_total, 2) }} Bs</strong></div>
                    </div>
                    @if($servicio->observaciones)
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Observaciones</div>
                        <div class="col-8"><strong>{{ $servicio->observaciones }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Lista de Bienes -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Lista de Bienes</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($servicio->bienes as $index => $bien)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bien->nombre }}</td>
                                    <td>{{ $bien->cantidad }}</td>
                                    <td>{{ $bien->descripcion ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay bienes registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asignación y Acciones -->
        <div class="col-lg-6">
            <!-- Asignación de Personal -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Asignación de Personal</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Vehículo</div>
                        <div class="col-8">
                            @if($servicio->vehiculo)
                                <strong>{{ $servicio->vehiculo->placa }}</strong>
                                <span class="badge bg-secondary ms-1">{{ $servicio->vehiculo->tipo }}</span>
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Chofer</div>
                        <div class="col-8">
                            @if($servicio->chofer)
                                <strong>{{ $servicio->chofer->nombre_completo }}</strong>
                                <span class="badge bg-info ms-1">Lic. {{ $servicio->chofer->licencia }}</span>
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Ayudantes</div>
                        <div class="col-8">
                            <strong>{{ $servicio->cantidad_ayudantes }}</strong>
                            <span class="text-muted ms-1">requeridos</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Pisos</div>
                        <div class="col-8">
                            <strong>{{ $servicio->numero_pisos }}</strong>
                            @if($servicio->es_callejon)
                                <span class="badge bg-warning text-dark ms-1">Callejón</span>
                            @endif
                        </div>
                    </div>

                    @if(!$servicio->chofer || !$servicio->vehiculo)
                        <div class="mt-3">
                            <a href="{{ route('servicios.asignar', $servicio) }}" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-1"></i> Asignar Personal
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estado de Pago -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Estado de Pago</h6>
                </div>
                <div class="card-body">
                    @if($servicio->estado === 'pagado')
                        <div class="text-center py-2">
                            <span class="badge bg-success" style="font-size:1rem; padding:8px 20px;">
                                <i class="fas fa-check-circle me-2"></i> Pagado
                            </span>
                            <p class="text-muted small mt-2 mb-0">
                                Método: {{ $servicio->metodo_pago_label ?? 'No especificado' }}
                            </p>
                        </div>
                    @elseif($servicio->estado === 'pendiente_pago')
                        <div class="text-center py-2">
                            <span class="badge bg-danger" style="font-size:1rem; padding:8px 20px;">
                                <i class="fas fa-exclamation-triangle me-2"></i> Pendiente de Pago
                            </span>
                            <p class="text-muted small mt-2 mb-0">
                                <a href="{{ route('pagos.index', $servicio) }}" class="btn btn-warning btn-sm mt-2">
                                    <i class="fas fa-hand-holding-usd me-1"></i> Registrar Pago
                                </a>
                            </p>
                        </div>
                        @if($servicio->deuda)
                            <div class="alert alert-warning mt-2 mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Deuda registrada - Vence: {{ $servicio->deuda->fecha_vencimiento->format('d/m/Y') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-2">
                            <span class="badge bg-secondary" style="font-size:1rem; padding:8px 20px;">
                                <i class="fas fa-hourglass-half me-2"></i> Pendiente
                            </span>
                            <p class="text-muted small mt-2 mb-0">
                                El pago se realizará al finalizar el servicio
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-tools me-2 text-primary"></i>Acciones</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($servicio->estado !== 'finalizado' && $servicio->estado !== 'cancelado' && $servicio->estado !== 'pagado')
                            <button class="btn btn-warning" onclick="cambiarEstado({{ $servicio->id }})">
                                <i class="fas fa-exchange-alt me-1"></i> Cambiar Estado
                            </button>
                        @endif
                        <a href="{{ route('servicios.comprobante', $servicio) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Generar Comprobante
                        </a>
                        <a href="{{ route('pagos.index', $servicio) }}" class="btn btn-info">
                            <i class="fas fa-credit-card me-1"></i> Gestión de Pagos
                        </a>
                        <a href="{{ route('gps.seguimiento', $servicio) }}" class="btn btn-primary">
                            <i class="fas fa-map-marked-alt me-1"></i> Ver Seguimiento GPS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cambiarEstado(servicioId) {
    const estados = ['pendiente', 'confirmado', 'en_progreso', 'finalizado', 'cancelado'];
    let options = estados.map((e, i) => `${i+1}. ${e}`).join('\n');
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