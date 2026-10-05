@extends('layouts.app')

@section('title', 'Dispositivos GPS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-satellite-dish me-2 text-primary"></i>Dispositivos GPS
        </h1>
        <a href="{{ route('dispositivos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Asignar Dispositivo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Dispositivo</th>
                            <th>Chofer</th>
                            <th>Vehículo</th>
                            <th>Estado</th>
                            <th>Última conexión</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dispositivos as $dispositivo)
                        <tr>
                            <td>
                                <i class="fas fa-satellite-dish text-primary me-2"></i>
                                <strong>{{ $dispositivo->dispositivo_id }}</strong>
                            </td>
                            <td>{{ $dispositivo->chofer->nombre_completo ?? 'Sin asignar' }}</td>
                            <td>{{ $dispositivo->vehiculo->placa ?? 'Sin asignar' }}</td>
                            <td>
                                @if($dispositivo->activo)
                                    <span class="badge bg-success">🟢 Activo</span>
                                @else
                                    <span class="badge bg-danger">🔴 Inactivo</span>
                                @endif
                            </td>
                            <td class="ultima-conexion" data-dispositivo-id="{{ $dispositivo->dispositivo_id }}">
                                <span class="text-muted">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('dispositivos.edit', $dispositivo) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                {{-- ELIMINAR DE BD --}}
                                <form action="{{ route('dispositivos.destroy', $dispositivo) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('¿Eliminar este dispositivo de la base de datos?')"
                                            title="Eliminar de BD">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-satellite-dish fa-2x mb-2 d-block"></i>
                                No hay dispositivos asignados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// CARGAR ÚLTIMA CONEXIÓN DESDE FIREBASE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const celdas = document.querySelectorAll('.ultima-conexion');
    
    if (celdas.length === 0) return;
    
    // Obtener todas las ubicaciones de Firebase
    fetch('{{ route("gps.firebase.ubicaciones") }}')
        .then(response => response.json())
        .then(data => {
            const ubicaciones = data.data || [];
            
            celdas.forEach(celda => {
                const dispositivoId = celda.dataset.dispositivoId;
                const ubicacion = ubicaciones.find(u => u.dispositivoId === dispositivoId);
                
                if (ubicacion && ubicacion.actualizado) {
                    const hace = timeAgo(ubicacion.actualizado);
                    const fecha = new Date(ubicacion.actualizado).toLocaleString('es-BO', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    celda.innerHTML = `
                        <span title="${fecha}">
                            <i class="fas fa-clock text-success"></i>
                            ${hace}
                        </span>
                    `;
                } else {
                    celda.innerHTML = `
                        <span class="text-muted">
                            <i class="fas fa-clock"></i> Nunca
                        </span>
                    `;
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            celdas.forEach(celda => {
                celda.innerHTML = `
                    <span class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error
                    </span>
                `;
            });
        });
});

// ============================================
// TIEMPO RELATIVO
// ============================================
function timeAgo(fecha) {
    if (!fecha) return 'Nunca';
    const ahora = new Date();
    const fechaDate = new Date(fecha);
    const diff = Math.floor((ahora - fechaDate) / 1000);
    
    if (diff < 0) return 'Ahora';
    if (diff < 60) return 'Hace unos segundos';
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} h`;
    return `Hace ${Math.floor(diff / 86400)} días`;
}
</script>
@endpush
@endsection