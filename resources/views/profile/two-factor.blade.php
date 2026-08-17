@extends('layouts.app')

@section('title', 'Configuración 2FA')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">🔐 Autenticación de Dos Factores</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if(auth()->user()->google2fa_enabled)
                        <!-- 2FA Activado -->
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡2FA está activado!</strong> Tu cuenta está más segura.
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <a href="{{ route('2fa.recovery') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-key me-2"></i> Ver códigos de recuperación
                                </a>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('2fa.disable') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="form-group mb-2">
                                        <input type="password" name="password" class="form-control" 
                                               placeholder="Ingresa tu contraseña" required>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-times me-2"></i> Desactivar 2FA
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- 2FA Desactivado -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Activa la autenticación de dos factores para mayor seguridad.
                        </div>
                        
                        @if(isset($qrCode))
                            <div class="text-center my-4">
                                <h6>1. Escanea este código QR con Google Authenticator</h6>
                                <div class="d-flex justify-content-center my-3">
                                    {!! $qrCode !!}
                                </div>
                                <p class="text-muted small">
                                    O introduce el código manualmente: <strong class="bg-light p-1">{{ $secret }}</strong>
                                </p>
                            </div>
                            
                            <form action="{{ route('2fa.enable') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>2. Ingresa el código de 6 dígitos para confirmar</label>
                                    <input type="text" name="code" class="form-control form-control-lg text-center" 
                                           placeholder="123456" required pattern="[0-9]{6}" maxlength="6">
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-2"></i> Activar 2FA
                                </button>
                            </form>
                        @else
                            <div class="text-center">
                                <a href="{{ route('2fa.setup') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> Configurar 2FA
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection