@extends('layouts.app')

@section('title', 'Gestión de Pagos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Gestión de Pagos</h1>
            <small class="text-muted">Servicio #{{ $servicio->id }} · {{ $servicio->cliente->nombre_completo }}</small>
        </div>
        <span class="badge bg-secondary">Módulo de Pagos</span>
    </div>

    <div class="row">
        <!-- Información del servicio -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>Resumen del Servicio
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cliente</span>
                        <strong>{{ $servicio->cliente->nombre_completo }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Origen</span>
                        <strong>{{ $servicio->origen }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Destino</span>
                        <strong>{{ $servicio->destino }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Fecha</span>
                        <strong>{{ $servicio->fecha_servicio->format('d/m/Y') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        @if($servicio->estado === 'pagado')
                            <span class="badge bg-success">{{ $servicio->estado_label }}</span>
                        @elseif($servicio->estado === 'pendiente_pago')
                            <span class="badge bg-danger">{{ $servicio->estado_label }}</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $servicio->estado_label }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><strong>Total a Pagar</strong></span>
                        <strong class="text-primary h5">{{ number_format($servicio->costo_total, 2) }} Bs</strong>
                    </div>
                </div>
            </div>

            <!-- Estado de Pago -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-clock me-2 text-primary"></i>Estado de Pago
                    </h6>
                    <div class="text-center py-2">
                        @if($servicio->estado === 'pagado')
                            <span class="badge bg-success" style="font-size:1rem; padding:8px 20px;">
                                <i class="fas fa-check-circle me-2"></i> Pagado
                            </span>
                            <p class="text-muted small mt-2 mb-0">
                                Método: {{ $servicio->metodo_pago_label ?? 'No especificado' }}
                            </p>
                        @elseif($servicio->estado === 'pendiente_pago')
                            <span class="badge bg-danger" style="font-size:1rem; padding:8px 20px;">
                                <i class="fas fa-exclamation-triangle me-2"></i> Pendiente de Pago
                            </span>
                            <p class="text-muted small mt-2 mb-0">
                                El pago está pendiente. Registre el pago para finalizar.
                            </p>
                        @else
                            <span class="badge bg-warning text-dark" style="font-size:1rem; padding:8px 20px;">
                                <i class="fas fa-hourglass-half me-2"></i> Pendiente
                            </span>
                            <p class="text-muted small mt-2 mb-0">
                                El pago se realiza al finalizar el servicio
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-hand-holding-usd me-2 text-primary"></i>Seleccionar Método de Pago
                    </h6>

                    <div class="row g-3 mb-4">
                        <!-- Efectivo -->
                        <div class="col-md-6">
                            <div class="payment-method active" onclick="selectPayment('efectivo')">
                                <i class="fas fa-money-bill-wave icon-efectivo"></i>
                                <h6 class="mb-0">Efectivo</h6>
                                <small class="text-muted">Pago en físico</small>
                            </div>
                        </div>
                        <!-- QR -->
                        <div class="col-md-6">
                            <div class="payment-method" onclick="selectPayment('qr')">
                                <i class="fas fa-qrcode icon-qr"></i>
                                <h6 class="mb-0">Código QR</h6>
                                <small class="text-muted">Escanea y paga</small>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Efectivo -->
                    <div id="panel-efectivo" class="config-card active-card">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h6 class="mb-1">
                                    <i class="fas fa-coins me-2 text-success"></i>Pago en Efectivo
                                </h6>
                                <p class="text-muted small mb-0">
                                    El cliente paga al finalizar el servicio. El sistema registra el pago y genera el comprobante.
                                </p>
                                @if($servicio->estado !== 'pagado' && $servicio->estado !== 'cancelado')
                                    <div class="mt-2">
                                        <span class="badge bg-info">
                                            <i class="fas fa-info-circle me-1"></i> 
                                            Estado actual: {{ $servicio->estado_label }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-5 text-end">
                                @if($servicio->estado === 'pagado')
                                    <span class="badge bg-success" style="font-size:1rem; padding:8px 20px;">
                                        <i class="fas fa-check-circle me-2"></i> Ya pagado
                                    </span>
                                @else
                                    <button class="btn btn-success w-100" onclick="registrarPago('efectivo')">
                                        <i class="fas fa-check me-1"></i> Registrar Pago
                                    </button>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        El servicio se marcará como Pagado
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Panel de QR -->
                    <div id="panel-qr" class="config-card" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-md-5 text-center">
                                <div class="qr-container">
                                    <div class="qr-placeholder">
                                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='white'/%3E%3Cg fill='black'%3E%3Crect width='10' height='10'/%3E%3Crect x='12' width='10' height='10'/%3E%3Crect x='24' width='10' height='10'/%3E%3Crect x='48' width='10' height='10'/%3E%3Crect x='60' width='10' height='10'/%3E%3Crect x='72' width='10' height='10'/%3E%3Crect x='84' width='10' height='10'/%3E%3Crect x='0' y='12' width='10' height='10'/%3E%3Crect x='24' y='12' width='10' height='10'/%3E%3Crect x='36' y='12' width='10' height='10'/%3E%3Crect x='60' y='12' width='10' height='10'/%3E%3Crect x='84' y='12' width='10' height='10'/%3E%3Crect x='0' y='24' width='10' height='10'/%3E%3Crect x='12' y='24' width='10' height='10'/%3E%3Crect x='36' y='24' width='10' height='10'/%3E%3Crect x='48' y='24' width='10' height='10'/%3E%3Crect x='60' y='24' width='10' height='10'/%3E%3Crect x='72' y='24' width='10' height='10'/%3E%3Crect x='0' y='36' width='10' height='10'/%3E%3Crect x='12' y='36' width='10' height='10'/%3E%3Crect x='24' y='36' width='10' height='10'/%3E%3Crect x='48' y='36' width='10' height='10'/%3E%3Crect x='72' y='36' width='10' height='10'/%3E%3Crect x='84' y='36' width='10' height='10'/%3E%3Crect x='36' y='48' width='10' height='10'/%3E%3Crect x='48' y='48' width='10' height='10'/%3E%3Crect x='60' y='48' width='10' height='10'/%3E%3Crect x='72' y='48' width='10' height='10'/%3E%3Crect x='0' y='60' width='10' height='10'/%3E%3Crect x='12' y='60' width='10' height='10'/%3E%3Crect x='36' y='60' width='10' height='10'/%3E%3Crect x='60' y='60' width='10' height='10'/%3E%3Crect x='72' y='60' width='10' height='10'/%3E%3Crect x='84' y='60' width='10' height='10'/%3E%3Crect x='0' y='72' width='10' height='10'/%3E%3Crect x='24' y='72' width='10' height='10'/%3E%3Crect x='36' y='72' width='10' height='10'/%3E%3Crect x='48' y='72' width='10' height='10'/%3E%3Crect x='60' y='72' width='10' height='10'/%3E%3Crect x='0' y='84' width='10' height='10'/%3E%3Crect x='12' y='84' width='10' height='10'/%3E%3Crect x='24' y='84' width='10' height='10'/%3E%3Crect x='36' y='84' width='10' height='10'/%3E%3Crect x='60' y='84' width='10' height='10'/%3E%3Crect x='72' y='84' width='10' height='10'/%3E%3Crect x='84' y='84' width='10' height='10'/%3E%3C/g%3E%3C/svg%3E" 
                                             alt="Código QR de pago" 
                                             style="width:100%; height:100%; object-fit:contain;">
                                    </div>
                                    <small class="text-muted d-block mt-2">Código QR de pago</small>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6 class="mb-1">
                                    <i class="fas fa-qrcode me-2 text-primary"></i>Pago con Código QR
                                </h6>
                                <p class="text-muted small">
                                    El cliente escanea el código QR con su aplicación bancaria y realiza el pago desde su celular.
                                </p>
                                <div class="d-flex gap-2 mt-2 flex-wrap">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i> Ver QR
                                    </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="fas fa-download me-1"></i> Descargar
                                    </button>
                                    <a href="{{ route('pagos.configuracion-qr') }}" class="btn btn-warning">
                                        <i class="fas fa-sync me-1"></i> Actualizar QR
                                    </a>
                                </div>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-info-circle me-1"></i> El QR puede ser actualizado por el propietario
                                </div>
                                <div class="mt-2 p-2 bg-light rounded">
                                    <small><i class="fas fa-check-circle text-success me-1"></i> QR activo · Banco Nacional · Cuenta: 123-4567890</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de pagos -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-history me-2 text-primary"></i>Historial de Pagos del Cliente
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Servicio</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historial as $item)
                                <tr>
                                    <td>#{{ $item->id }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td>{{ number_format($item->costo_total, 2) }} Bs</td>
                                    <td>
                                        @if($item->metodo_pago === 'efectivo')
                                            <i class="fas fa-money-bill-wave text-success me-1"></i> Efectivo
                                        @elseif($item->metodo_pago === 'qr')
                                            <i class="fas fa-qrcode text-primary me-1"></i> QR
                                        @else
                                            <i class="fas fa-spinner me-1"></i> Pendiente
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->estado === 'pagado')
                                            <span class="badge bg-success">Pagado</span>
                                        @else
                                            <span class="badge bg-danger">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay pagos registrados</td>
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

@push('scripts')
<script>
function selectPayment(method) {
    document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('active'));
    document.getElementById('panel-efectivo').style.display = 'none';
    document.getElementById('panel-efectivo').classList.remove('active-card');
    document.getElementById('panel-qr').style.display = 'none';
    document.getElementById('panel-qr').classList.remove('active-card');

    if (method === 'efectivo') {
        document.querySelector('.payment-method:first-child').classList.add('active');
        document.getElementById('panel-efectivo').style.display = 'block';
        document.getElementById('panel-efectivo').classList.add('active-card');
    } else if (method === 'qr') {
        document.querySelector('.payment-method:last-child').classList.add('active');
        document.getElementById('panel-qr').style.display = 'block';
        document.getElementById('panel-qr').classList.add('active-card');
    }
}

function registrarPago(metodo) {
    const servicioId = {{ $servicio->id }};
    const monto = {{ $servicio->costo_total }};
    const estadoActual = '{{ $servicio->estado }}';
    
    // 🔥 MOSTRAR EN CONSOLA PARA DEPURAR
    console.log('=== DEPURACIÓN DE PAGO ===');
    console.log('Servicio ID:', servicioId);
    console.log('Monto:', monto);
    console.log('Método:', metodo);
    console.log('Estado actual:', estadoActual);
    console.log('URL:', '{{ route("servicios.pago", $servicio->id) }}');
    
    if (estadoActual === 'pagado') {
        alert('⚠️ Este servicio ya está pagado');
        return;
    }
    
    if (confirm('¿Registrar pago de ' + monto.toFixed(2) + ' Bs por ' + metodo + '?')) {
        fetch('{{ route("servicios.pago", $servicio->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                metodo_pago: metodo, 
                monto: monto 
            })
        })
        .then(response => {
            console.log('Status HTTP:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.success) {
                alert('✅ Pago registrado exitosamente');
                location.reload();
            } else {
                alert('❌ ' + (data.message || 'Error al registrar pago'));
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
            alert('❌ Error de conexión: ' + error.message);
        });
    }
}
</script>
@endpush

<style>
.payment-method {
    cursor: pointer;
    padding: 15px;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.2s;
    text-align: center;
}
.payment-method:hover {
    border-color: #0d6efd;
    background: #f0f7ff;
}
.payment-method.active {
    border-color: #0d6efd;
    background: #dbeafe;
}
.payment-method i {
    font-size: 2rem;
    margin-bottom: 5px;
}
.payment-method .icon-efectivo { color: #198754; }
.payment-method .icon-qr { color: #0d6efd; }
.config-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s;
}
.config-card.active-card {
    border: 2px solid #0d6efd;
    background: #f0f7ff;
}
.qr-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    border: 2px dashed #dee2e6;
}
.qr-container .qr-placeholder {
    width: 180px;
    height: 180px;
    background: #f8f9fa;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    overflow: hidden;
}
</style>
@endsection