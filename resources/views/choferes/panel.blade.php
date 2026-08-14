@extends('layouts.app')

@section('title', 'Panel del Chofer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-user-circle me-2 text-primary"></i>Panel del Chofer</h1>
            <small class="text-muted">{{ auth()->user()->name }} · Lic. #12345</small>
        </div>
        <span class="badge bg-secondary">En línea</span>
    </div>

    <div class="row">
        <!-- Perfil y GPS -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    <div class="avatar-placeholder mx-auto mb-3" style="width:60px;height:60px;background:#0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;font-weight:bold;">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <h5>{{ auth()->user()->name }}</h5>
                    <p class="text-muted small">Licencia: #12345</p>
                    <div>
                        <span class="badge bg-success">Disponible</span>
                        <span class="badge bg-primary">En línea</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Servicios hoy</span>
                        <strong>2</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Estado GPS</span>
                        <span class="text-success"><i class="fas fa-satellite-dish me-1"></i> Activo</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-success w-100" id="btn-gps">
                            <i class="fas fa-satellite me-2"></i> GPS Activo
                        </button>
                        <small class="text-muted d-block mt-1">Actualizando cada 3 segundos</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios Asignados -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Servicios Asignados</h6>
                    <span class="badge bg-primary">{{ $servicios->count() }} activos</span>
                </div>
                <div class="card-body">
                    @forelse($servicios as $servicio)
                    <div class="border rounded p-3 mb-3" style="border-left: 4px solid #0d6efd !important;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-warning text-dark me-2">
                                        {{ ucfirst(str_replace('_', ' ', $servicio->estado)) }}
                                    </span>
                                    <span class="badge bg-secondary">#{{ $servicio->id }}</span>
                                </div>
                                <h6 class="mb-1">{{ $servicio->cliente->nombre_completo }}</h6>
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-map-pin me-1"></i> {{ $servicio->origen }} → {{ $servicio->destino }}
                                </p>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-users me-1"></i> Ayudantes: {{ $servicio->cantidad_ayudantes }}
                                </p>
                            </div>
                            <div class="text-end">
                                @if($servicio->estado === 'en_progreso')
                                    <span class="badge bg-success">En camino</span>
                                @else
                                    <span class="badge bg-secondary">Pendiente</span>
                                @endif
                                <div><small class="text-muted">{{ $servicio->fecha_servicio->format('H:i') }}</small></div>
                                @if($servicio->estado === 'confirmado')
                                    <button class="btn btn-sm btn-outline-primary mt-1" onclick="iniciarServicio({{ $servicio->id }})">
                                        <i class="fas fa-check me-1"></i> Iniciar
                                    </button>
                                @endif
                                @if($servicio->estado === 'en_progreso')
                                    <button class="btn btn-sm btn-outline-success mt-1" onclick="finalizarServicio({{ $servicio->id }})">
                                        <i class="fas fa-flag-checkered me-1"></i> Finalizar
                                    </button>
                                @endif
                                <a href="{{ route('gps.seguimiento', $servicio) }}" class="btn btn-sm btn-outline-primary mt-1" target="_blank">
                                    <i class="fas fa-route me-1"></i> Ver Ruta
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-truck fa-3x mb-3 d-block text-muted"></i>
                        <p>No tienes servicios asignados</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function iniciarServicio(id) {
    if (confirm('¿Iniciar el servicio #' + id + '?')) {
        fetch(`/servicios/${id}/estado`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ estado: 'en_progreso' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al iniciar servicio');
            }
        });
    }
}

function finalizarServicio(id) {
    if (confirm('¿Finalizar el servicio #' + id + '?')) {
        fetch(`/servicios/${id}/estado`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ estado: 'finalizado' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al finalizar servicio');
            }
        });
    }
}

// Simular GPS
document.getElementById('btn-gps').addEventListener('click', function() {
    if (this.classList.contains('btn-success')) {
        this.classList.remove('btn-success');
        this.classList.add('btn-danger');
        this.innerHTML = '<i class="fas fa-satellite me-2"></i> GPS Activo';
        // Simulación de envío de coordenadas
        setInterval(() => {
            const lat = -16.5 + (Math.random() - 0.5) * 0.01;
            const lng = -68.13 + (Math.random() - 0.5) * 0.01;
            fetch('{{ route("gps.actualizar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ latitud: lat, longitud: lng, servicio_id: 1 })
            });
        }, 3000);
    }
});
</script>
@endpush
@endsection