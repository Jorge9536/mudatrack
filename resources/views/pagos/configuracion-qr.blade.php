@extends('layouts.app')

@section('title', 'Configuración QR')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('servicios.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-qrcode me-2 text-primary"></i>Configuración QR de Pago</h1>
        <span class="badge bg-secondary ms-2">Administrador</span>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-qrcode me-2 text-primary"></i>QR Actual</h6>
                    
                    <div class="text-center p-4 border rounded bg-light">
                        <div class="qr-preview" style="width:200px;height:200px;background:white;border:2px dashed #dee2e6;border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto;overflow:hidden;">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='white'/%3E%3Cg fill='black'%3E%3Crect width='10' height='10'/%3E%3Crect x='12' width='10' height='10'/%3E%3Crect x='24' width='10' height='10'/%3E%3Crect x='48' width='10' height='10'/%3E%3Crect x='60' width='10' height='10'/%3E%3Crect x='72' width='10' height='10'/%3E%3Crect x='84' width='10' height='10'/%3E%3Crect x='0' y='12' width='10' height='10'/%3E%3Crect x='24' y='12' width='10' height='10'/%3E%3Crect x='36' y='12' width='10' height='10'/%3E%3Crect x='60' y='12' width='10' height='10'/%3E%3Crect x='84' y='12' width='10' height='10'/%3E%3Crect x='0' y='24' width='10' height='10'/%3E%3Crect x='12' y='24' width='10' height='10'/%3E%3Crect x='36' y='24' width='10' height='10'/%3E%3Crect x='48' y='24' width='10' height='10'/%3E%3Crect x='60' y='24' width='10' height='10'/%3E%3Crect x='72' y='24' width='10' height='10'/%3E%3Crect x='0' y='36' width='10' height='10'/%3E%3Crect x='12' y='36' width='10' height='10'/%3E%3Crect x='24' y='36' width='10' height='10'/%3E%3Crect x='48' y='36' width='10' height='10'/%3E%3Crect x='72' y='36' width='10' height='10'/%3E%3Crect x='84' y='36' width='10' height='10'/%3E%3Crect x='36' y='48' width='10' height='10'/%3E%3Crect x='48' y='48' width='10' height='10'/%3E%3Crect x='60' y='48' width='10' height='10'/%3E%3Crect x='72' y='48' width='10' height='10'/%3E%3Crect x='0' y='60' width='10' height='10'/%3E%3Crect x='12' y='60' width='10' height='10'/%3E%3Crect x='36' y='60' width='10' height='10'/%3E%3Crect x='60' y='60' width='10' height='10'/%3E%3Crect x='72' y='60' width='10' height='10'/%3E%3Crect x='84' y='60' width='10' height='10'/%3E%3Crect x='0' y='72' width='10' height='10'/%3E%3Crect x='24' y='72' width='10' height='10'/%3E%3Crect x='36' y='72' width='10' height='10'/%3E%3Crect x='48' y='72' width='10' height='10'/%3E%3Crect x='60' y='72' width='10' height='10'/%3E%3Crect x='0' y='84' width='10' height='10'/%3E%3Crect x='12' y='84' width='10' height='10'/%3E%3Crect x='24' y='84' width='10' height='10'/%3E%3Crect x='36' y='84' width='10' height='10'/%3E%3Crect x='60' y='84' width='10' height='10'/%3E%3Crect x='72' y='84' width='10' height='10'/%3E%3Crect x='84' y='84' width='10' height='10'/%3E%3C/g%3E%3C/svg%3E" 
                                 style="width:100%; height:100%; object-fit:contain;">
                        </div>
                        <p class="mt-2 mb-0"><strong>Código QR Activo</strong></p>
                        <small class="text-muted">Última actualización: {{ now()->format('d/m/Y H:i') }}</small>
                        <div class="mt-2">
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Activo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-edit me-2 text-primary"></i>Actualizar QR</h6>
                    
                    <form action="{{ route('pagos.configuracion-qr.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Subir nueva imagen QR</label>
                            <input type="file" name="imagen_qr" class="form-control" accept="image/*">
                            <small class="text-muted">Formatos permitidos: PNG, JPG, SVG</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">O ingresa URL del QR</label>
                            <input type="url" name="url_qr" class="form-control" placeholder="https://ejemplo.com/qr-pago">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vista previa</label>
                            <div class="qr-preview" style="width:150px;height:150px;background:#f8f9fa;border:2px dashed #dee2e6;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-direction:column;">
                                <i class="fas fa-qrcode" style="font-size:3.5rem;color:#6c757d;"></i>
                                <small class="text-muted">Nuevo QR</small>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Al actualizar el QR, el código anterior dejará de funcionar. Los clientes deberán usar el nuevo código.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar QR
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Restaurar anterior
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection