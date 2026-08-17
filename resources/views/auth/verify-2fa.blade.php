@extends('layouts.app')

@section('title', 'Verificación 2FA')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <p class="text-muted">Ingresa el código de 6 dígitos de tu aplicación Google Authenticator.</p>
                    
                    <form action="{{ route('2fa.verify') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="one_time_password" class="form-label">Código de verificación</label>
                            <input type="text" name="one_time_password" id="one_time_password"
                                   class="form-control form-control-lg text-center" 
                                   placeholder="123456" required pattern="[0-9]{6}" maxlength="6" autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check me-2"></i> Verificar
                        </button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <small>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#recoveryModal">
                                ¿Perdiste tu dispositivo? Usa un código de recuperación
                            </a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de códigos de recuperación -->
<div class="modal fade" id="recoveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🔑 Códigos de Recuperación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Ingresa uno de tus códigos de recuperación de 8 caracteres:</p>
                <form action="{{ route('2fa.recovery.verify') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <input type="text" name="recovery_code" class="form-control text-center" 
                               placeholder="Ej: ABC123XY" required maxlength="8">
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-key me-2"></i> Usar código de recuperación
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection