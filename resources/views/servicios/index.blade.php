@extends('layouts.app')

@section('title', 'Servicios')

@push('styles')
<style>
    .pagination-sm .page-link {
        padding: 0.15rem 0.5rem;
        font-size: 0.8rem;
    }
    .pagination { margin-bottom: 0; }
    .pagination .page-link { padding: 0.15rem 0.6rem; font-size: 0.8rem; }
    @media (max-width: 576px) {
        .pagination .page-item:not(.active):not(.prev):not(.next) .page-link { display: none; }
        .pagination .page-item.active .page-link,
        .pagination .page-item.prev .page-link,
        .pagination .page-item.next .page-link { display: block; }
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

    {{-- ============================================ --}}
    {{-- TARJETAS DE ESTADOS OPERATIVOS --}}
    {{-- ============================================ --}}
    <h6 class="text-muted mb-2"><i class="fas fa-tasks me-1"></i> Estados del Servicio</h6>
    <div class="row g-3 mb-3">
        <div class="col-md">
            <div class="card text-white bg-secondary">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Pendientes</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'pendiente')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-primary">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Confirmados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'confirmado')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-warning">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">En Progreso</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'en_progreso')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-success">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Finalizados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'finalizado')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card text-white bg-info">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Cancelados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado', 'cancelado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TARJETAS DE ESTADOS DE PAGO --}}
    {{-- ============================================ --}}
    <h6 class="text-muted mb-2"><i class="fas fa-money-bill me-1"></i> Estados de Pago</h6>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Pendiente de Pago</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado_pago', 'pendiente')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body py-2">
                    <h6 class="card-title mb-1">Pagados</h6>
                    <p class="display-6 mb-0">{{ $servicios->where('estado_pago', 'pagado')->count() }}</p>
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
                            <th>Estado Servicio</th>
                            <th>Pago</th>
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
                            
                            {{-- ESTADO OPERATIVO --}}
                            <td>
                                @php
                                    $badgeClass = [
                                        'pendiente' => 'secondary',
                                        'confirmado' => 'primary',
                                        'en_progreso' => 'warning',
                                        'finalizado' => 'success',
                                        'cancelado' => 'danger',
                                    ][$servicio->estado] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ $servicio->estado_label }}
                                </span>
                            </td>
                            
                            {{-- ESTADO DE PAGO --}}
                            <td>
                                @if($servicio->estado_pago === 'pagado')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Pagado
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-clock me-1"></i> Pendiente
                                    </span>
                                @endif
                            </td>
                            
                            <td>
                                <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-warning" onclick="cambiarEstado({{ $servicio->id }})" title="Cambiar estado">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <a href="{{ route('servicios.comprobante', $servicio) }}" class="btn btn-sm btn-danger" target="_blank" title="Ver PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <button class="btn btn-sm btn-success" 
                                        title="Enviar por WhatsApp" 
                                        onclick="enviarWhatsApp({{ $servicio->id }}, '{{ $servicio->cliente->nombre_completo }}')"
                                        id="btn-whatsapp-{{ $servicio->id }}">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay servicios registrados</td>
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

function enviarWhatsApp(servicioId, nombreCliente) {
    if (!confirm(`¿Enviar el comprobante por WhatsApp a ${nombreCliente}?`)) {
        return;
    }
    
    const btn = document.getElementById(`btn-whatsapp-${servicioId}`);
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch(`/servicios/${servicioId}/enviar-whatsapp`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
            
            mostrarNotificacion('success', data.message);
            
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
            }, 3000);
        } else {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            mostrarNotificacion('error', data.message || 'Error al enviar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        mostrarNotificacion('error', 'Error de conexión');
    });
}

function mostrarNotificacion(tipo, mensaje) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    const bgColor = tipo === 'success' ? '#25D366' : '#dc3545';
    const icono = tipo === 'success' ? 'check-circle' : 'exclamation-circle';
    
    toast.style.cssText = `
        background: ${bgColor}; color: white; padding: 15px 20px;
        border-radius: 8px; margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex; align-items: center; gap: 10px;
        min-width: 300px; animation: slideIn 0.3s ease; font-family: sans-serif;
    `;
    
    toast.innerHTML = `
        <i class="fas fa-${icono}" style="font-size: 20px;"></i>
        <span style="flex: 1;">${mensaje}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; cursor: pointer; font-size: 18px;">&times;</button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection