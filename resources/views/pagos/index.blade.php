@extends('layouts.app')

@section('title', 'Gestión de Pagos')

@push('styles')
<style>
    /* === ESTILOS DE MÉTODOS DE PAGO === */
    .payment-method {
        cursor: pointer;
        padding: 15px;
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.2s;
        text-align: center;
        background: white;
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
        padding: 20px;
        transition: all 0.3s;
        min-height: 200px;
    }
    .config-card.active-card {
        border: 2px solid #0d6efd;
        background: #f0f7ff;
    }

    /* === ESTILOS PARA EL QR - CORREGIDOS === */
    .qr-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }

    .qr-container {
        background: white;
        padding: 10px;
        border-radius: 12px;
        text-align: center;
        border: 2px dashed #dee2e6;
        width: 100%;
        max-width: 180px;
    }

    .qr-container .qr-placeholder {
        width: 100%;
        aspect-ratio: 1/1;
        max-width: 160px;
        max-height: 160px;
        background: #f8f9fa;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 2px solid #e9ecef;
        overflow: hidden;
        padding: 6px;
    }

    .qr-container .qr-placeholder img {
        width: 100% !important;
        height: 100% !important;
        max-width: 160px;
        max-height: 160px;
        object-fit: contain;
        display: block;
    }

    .qr-container .qr-placeholder .no-qr {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6c757d;
        padding: 10px;
    }

    .qr-container .qr-placeholder .no-qr i {
        font-size: 2.5rem;
        margin-bottom: 5px;
    }

    .qr-label {
        font-size: 0.6rem;
        color: #6c757d;
        margin-top: 6px;
        text-align: center;
    }

    /* === ACCIONES DEL QR === */
    .qr-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .qr-actions .btn {
        font-size: 0.7rem;
        padding: 4px 10px;
        white-space: nowrap;
    }

    /* === INFO DEL QR === */
    .qr-info {
        margin-top: 10px;
        padding: 10px 12px;
        background: #f8f9fa;
        border-radius: 6px;
        border-left: 3px solid #28a745;
        width: 100%;
    }

    .qr-info small {
        font-size: 0.7rem;
        display: block;
        line-height: 1.6;
    }

    .qr-info .text-muted {
        font-size: 0.65rem;
    }

    .qr-info .badge-qr {
        font-size: 0.6rem;
        padding: 2px 8px;
        background: #d4edda;
        color: #155724;
        border-radius: 12px;
        display: inline-block;
    }

    /* === AJUSTES DE FILAS === */
    .qr-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 15px;
    }

    .qr-col-left {
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 180px;
    }

    .qr-col-right {
        flex: 1;
        min-width: 200px;
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
        .qr-row {
            flex-direction: column;
            align-items: center;
        }
        .qr-col-left {
            min-width: unset;
            width: 100%;
            max-width: 200px;
        }
        .qr-col-right {
            width: 100%;
            text-align: center;
        }
        .qr-actions {
            justify-content: center;
        }
        .qr-container .qr-placeholder {
            max-width: 130px;
            max-height: 130px;
        }
        .qr-container .qr-placeholder img {
            max-width: 130px;
            max-height: 130px;
        }
        .config-card {
            padding: 15px;
        }
    }

    @media (max-width: 576px) {
        .qr-container {
            max-width: 150px;
            padding: 8px;
        }
        .qr-container .qr-placeholder {
            max-width: 110px;
            max-height: 110px;
        }
        .qr-container .qr-placeholder img {
            max-width: 110px;
            max-height: 110px;
        }
        .qr-actions .btn {
            font-size: 0.6rem;
            padding: 3px 6px;
        }
        .qr-info small {
            font-size: 0.6rem;
        }
    }
</style>
@endpush

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
        <!-- === COLUMNA IZQUIERDA === -->
        <div class="col-lg-4">
            <!-- Resumen del Servicio -->
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

        <!-- === COLUMNA DERECHA === -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-hand-holding-usd me-2 text-primary"></i>Seleccionar Método de Pago
                    </h6>

                    <!-- Botones de selección -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="payment-method active" onclick="selectPayment('efectivo')">
                                <i class="fas fa-money-bill-wave icon-efectivo"></i>
                                <h6 class="mb-0">Efectivo</h6>
                                <small class="text-muted">Pago en físico</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-method" onclick="selectPayment('qr')">
                                <i class="fas fa-qrcode icon-qr"></i>
                                <h6 class="mb-0">Código QR</h6>
                                <small class="text-muted">Escanea y paga</small>
                            </div>
                        </div>
                    </div>

                    <!-- === PANEL DE EFECTIVO === -->
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

                    <!-- === PANEL DE QR === -->
                    <div id="panel-qr" class="config-card" style="display: none;">
                        <div class="qr-row">
                            <!-- QR -->
                            <div class="qr-col-left">
                                <div class="qr-wrapper">
                                    <div class="qr-container">
                                        <div class="qr-placeholder">
                                            @php
                                                $tieneImagen = false;
                                                $imagenUrl = null;
                                                $urlQr = null;
                                                $rutaFisica = null;
                                                
                                                if (isset($qr) && $qr->imagen_qr) {
                                                    $rutaFisica = storage_path('app/public/' . $qr->imagen_qr);
                                                    if (file_exists($rutaFisica)) {
                                                        $tieneImagen = true;
                                                        $imagenUrl = asset('storage/' . $qr->imagen_qr);
                                                    }
                                                }
                                                
                                                if (isset($qr) && $qr->url_qr) {
                                                    $urlQr = $qr->url_qr;
                                                }
                                            @endphp

                                            @if($tieneImagen && $imagenUrl)
                                                <img src="{{ $imagenUrl }}" alt="Código QR de pago">
                                            @elseif($urlQr)
                                                <img src="{{ $urlQr }}" alt="Código QR de pago" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'no-qr\'><i class=\'fas fa-exclamation-triangle\'></i><small>Error al cargar</small></div>'">
                                            @else
                                                <div class="no-qr">
                                                    <i class="fas fa-qrcode"></i>
                                                    <small>No hay QR configurado</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="qr-label">Código QR de pago</span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="qr-col-right">
                                <h6 class="mb-1">
                                    <i class="fas fa-qrcode me-2 text-primary"></i>Pago con Código QR
                                </h6>
                                <p class="text-muted small mb-2">
                                    El cliente escanea el código QR con su aplicación bancaria y realiza el pago desde su celular.
                                </p>
                                
                                <div class="qr-actions">
                                    @if($tieneImagen && $imagenUrl)
                                        <a href="{{ $imagenUrl }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> Ver QR
                                        </a>
                                        <a href="{{ $imagenUrl }}" download="qr-pago.png" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-download me-1"></i> Descargar
                                        </a>
                                    @elseif($urlQr)
                                        <a href="{{ $urlQr }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> Ver QR
                                        </a>
                                    @endif
                                    <a href="{{ route('pagos.configuracion-qr') }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-sync me-1"></i> Actualizar QR
                                    </a>
                                </div>

                                @if(isset($qr) && ($tieneImagen || $urlQr))
                                    <div class="qr-info">
                                        <small>
                                            <span class="badge-qr">
                                                <i class="fas fa-check-circle text-success me-1"></i> QR activo
                                            </span>
                                            <span class="text-muted d-block mt-1">
                                                <i class="far fa-calendar-alt me-1"></i> Vencimiento: {{ now()->addDays(7)->format('d/m/Y') }}
                                            </span>
                                            <span class="text-muted d-block">
                                                Banco Nacional · Cuenta: 123-4567890
                                            </span>
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- === HISTORIAL DE PAGOS === -->
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
        .then(response => response.json())
        .then(data => {
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
@endsection