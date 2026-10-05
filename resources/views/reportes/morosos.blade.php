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
                        {{ number_format($morosos->sum(function($cliente) { 
                            return $cliente->deudas->where('estado', 'pendiente')->sum('monto'); 
                        }), 2) }} Bs
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
                    <p class="small mb-0 opacity-75">Bloqueados</p>
                    <h4 class="mb-0">{{ $morosos->where('bloqueado', true)->count() }}</h4>
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
                            <td>
                                @if($cliente->telefono)
                                    <a href="https://wa.me/591{{ preg_replace('/[^0-9]/', '', $cliente->telefono) }}" 
                                       target="_blank" 
                                       class="text-decoration-none">
                                        <i class="fab fa-whatsapp text-success me-1"></i>
                                        {{ $cliente->telefono }}
                                    </a>
                                @else
                                    <span class="text-muted">Sin teléfono</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $deudasPendientes->count() }}</span>
                            </td>
                            <td>
                                <strong class="text-danger">{{ number_format($totalDeuda, 2) }} Bs</strong>
                            </td>
                            <td>
                                @if($cliente->bloqueado)
                                    <span class="badge bg-danger">🔒 Bloqueado</span>
                                @else
                                    <span class="badge bg-warning text-dark">⚠️ Activo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('clientes.show', $cliente) }}" 
                                       class="btn btn-info" 
                                       title="Ver cliente">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" 
                                       class="btn btn-warning" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- 🔥 BOTÓN QUE ABRE EL MODAL CON DATOS REALES --}}
                                    <button class="btn btn-danger" 
                                            onclick="verDeudas({{ $cliente->id }}, '{{ addslashes($cliente->nombre_completo) }}')"
                                            title="Ver deudas">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
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
                <span class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Total: {{ $morosos->count() }} clientes con deudas · 
                    Total adeudado: {{ number_format($morosos->sum(function($c) { return $c->deudas->where('estado', 'pendiente')->sum('monto'); }), 2) }} Bs
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODAL DE DEUDAS (se llena con AJAX) -->
<!-- ============================================ -->
<div class="modal fade" id="modalDeudas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">
                    <i class="fas fa-list me-2"></i>
                    Deudas de <span id="nombreClienteModal"></span>
                </h6>
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
@endsection

@push('scripts')
<script>
// ============================================
// 🔥 CARGAR DEUDAS REALES POR AJAX
// ============================================
function verDeudas(clienteId, nombreCliente) {
    const modal = new bootstrap.Modal(document.getElementById('modalDeudas'));
    const body = document.getElementById('modalDeudasBody');
    const nombreSpan = document.getElementById('nombreClienteModal');
    
    // Poner el nombre en el título
    nombreSpan.textContent = nombreCliente;
    
    // Mostrar loading
    body.innerHTML = `
        <div class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin me-2"></i> Cargando deudas...
        </div>
    `;
    
    modal.show();

    // 🔥 LLAMADA AJAX AL SERVIDOR
    fetch(`/reportes/morosos/${clienteId}/deudas`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.deudas.length > 0) {
            let html = `
                <div class="alert alert-info mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-user me-2"></i>
                            <strong>${data.cliente.nombre_completo}</strong>
                            <br>
                            <small>
                                <i class="fas fa-phone me-1"></i>${data.cliente.telefono || 'Sin teléfono'}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Total adeudado</div>
                            <div class="h4 mb-0 text-danger">${data.total.toFixed(2)} Bs</div>
                            <div class="small">${data.cantidad} deuda(s)</div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Servicio</th>
                                <th>Monto</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            data.deudas.forEach(deuda => {
                let estadoBadge = '';
                let diasRestantes = '';
                
                if (deuda.vencida) {
                    estadoBadge = '<span class="badge bg-danger">🔴 Vencida</span>';
                    diasRestantes = `<small class="text-danger">Vencida hace ${Math.abs(deuda.dias_restantes)} día(s)</small>`;
                } else if (deuda.dias_restantes <= 3) {
                    estadoBadge = '<span class="badge bg-warning text-dark">⚠️ Por vencer</span>';
                    diasRestantes = `<small class="text-warning">Vence en ${deuda.dias_restantes} día(s)</small>`;
                } else {
                    estadoBadge = '<span class="badge bg-warning text-dark">⏳ Pendiente</span>';
                    diasRestantes = `<small class="text-muted">Vence en ${deuda.dias_restantes} día(s)</small>`;
                }
                
                html += `
                    <tr>
                        <td>
                            <a href="/servicios/${deuda.servicio_id}" target="_blank" class="text-decoration-none">
                                #${deuda.servicio_id}
                            </a>
                        </td>
                        <td><strong>${deuda.monto} Bs</strong></td>
                        <td>
                            ${deuda.fecha_vencimiento}
                            <br>
                            ${diasRestantes}
                        </td>
                        <td>${estadoBadge}</td>
                        <td>
                            <a href="/servicios/${deuda.servicio_id}" 
                               class="btn btn-sm btn-success" 
                               title="Ver servicio y registrar pago"
                               target="_blank">
                                <i class="fas fa-hand-holding-usd"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            body.innerHTML = html;
        } else {
            body.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-2 d-block"></i>
                    <p>Este cliente no tiene deudas pendientes</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        body.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error al cargar las deudas: ${error.message}
            </div>
        `;
    });
}
</script>
@endpush