@extends('layouts.app')

@section('title', 'Configuración QR')

@push('styles')
<style>
    .qr-preview {
        width: 200px;
        height: 200px;
        background: white;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        overflow: hidden;
    }
    
    .qr-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
    }
    
    .qr-preview .no-qr {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6c757d;
    }
    
    .qr-preview .no-qr i {
        font-size: 3.5rem;
        margin-bottom: 0.5rem;
    }
    
    .debug-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        font-size: 12px;
        font-family: monospace;
        margin-top: 10px;
        text-align: left;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('servicios.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-qrcode me-2 text-primary"></i>Configuración QR de Pago</h1>
        <span class="badge bg-secondary ms-2">Administrador</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-qrcode me-2 text-primary"></i>QR Actual
                    </h6>
                    
                    <div class="text-center p-4 border rounded bg-light">
                        @php
                            $tieneImagen = false;
                            $imagenUrl = null;
                            $urlQr = null;
                            $rutaFisica = null;
                            
                            if ($qr && $qr->imagen_qr) {
                                $rutaFisica = storage_path('app/public/' . $qr->imagen_qr);
                                if (file_exists($rutaFisica)) {
                                    $tieneImagen = true;
                                    $imagenUrl = asset('storage/' . $qr->imagen_qr);
                                }
                            }
                            
                            if ($qr && $qr->url_qr) {
                                $urlQr = $qr->url_qr;
                            }
                        @endphp

                        <div class="qr-preview">
                            @if($tieneImagen && $imagenUrl)
                                <img src="{{ $imagenUrl }}" alt="Código QR">
                            @elseif($urlQr)
                                <img src="{{ $urlQr }}" alt="Código QR" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'no-qr\'><i class=\'fas fa-exclamation-triangle\'></i><small>Error al cargar la imagen</small></div>'">
                            @else
                                <div class="no-qr">
                                    <i class="fas fa-qrcode"></i>
                                    <small>No hay QR configurado</small>
                                </div>
                            @endif
                        </div>
                        
                        <p class="mt-2 mb-0">
                            <strong>
                                @if($tieneImagen || $urlQr)
                                    Código QR Activo
                                @else
                                    Sin QR Configurado
                                @endif
                            </strong>
                        </p>
                        
                        <small class="text-muted">
                            @if($qr && $qr->fecha_actualizacion)
                                Última actualización: {{ $qr->fecha_actualizacion->format('d/m/Y H:i') }}
                            @else
                                No hay QR configurado
                            @endif
                        </small>
                        
                        <div class="mt-2">
                            @if($tieneImagen || $urlQr)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Activo
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Pendiente
                                </span>
                            @endif
                        </div>

                        <!-- Debug -->
                        <div class="debug-box">
                            <strong>🔍 Información técnica:</strong><br>
                            imagen_qr en BD: {{ $qr->imagen_qr ?? 'NULL' }}<br>
                            url_qr en BD: {{ $qr->url_qr ?? 'NULL' }}<br>
                            @if($qr && $qr->imagen_qr)
                                Ruta física: {{ $rutaFisica ?? 'No definida' }}<br>
                                ¿Existe archivo? {{ file_exists($rutaFisica) ? '✅ Sí' : '❌ No' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-edit me-2 text-primary"></i>Actualizar QR
                    </h6>
                    
                    <form action="{{ route('pagos.configuracion-qr.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Subir nueva imagen QR</label>
                            <input type="file" name="imagen_qr" class="form-control @error('imagen_qr') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Formatos permitidos: PNG, JPG, SVG (máx. 2MB)</small>
                            @error('imagen_qr')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">O ingresa URL del QR</label>
                            <input type="url" name="url_qr" class="form-control @error('url_qr') is-invalid @enderror" 
                                   placeholder="https://ejemplo.com/qr-pago" value="{{ old('url_qr', $qr->url_qr ?? '') }}">
                            @error('url_qr')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Al actualizar el QR, el código anterior dejará de funcionar. Los clientes deberán usar el nuevo código.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar QR
                            </button>
                            <a href="{{ route('pagos.configuracion-qr') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection