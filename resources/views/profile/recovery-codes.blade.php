@extends('layouts.app')

@section('title', 'Códigos de Recuperación')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">🔑 Códigos de Recuperación</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Guarda estos códigos!</strong> Si pierdes tu dispositivo, podrás usarlos para acceder a tu cuenta.
                        Cada código solo se puede usar una vez.
                    </div>
                    
                    <div class="row g-3 mb-4">
                        @foreach($codes as $code)
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <code class="h5">{{ $code }}</code>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Imprimir
                        </button>
                        <a href="{{ route('2fa.setup') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection