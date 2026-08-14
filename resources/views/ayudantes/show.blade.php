@extends('layouts.app')

@section('title', 'Ver Ayudante')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('ayudantes.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-user me-2 text-primary"></i>Ayudante: {{ $ayudante->nombre_completo }}</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Datos del Ayudante</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nombre</span>
                        <strong>{{ $ayudante->nombre_completo }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Teléfono</span>
                        <strong>{{ $ayudante->telefono }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Estado</span>
                        @if($ayudante->disponible)
                            <span class="badge bg-success">Disponible</span>
                        @else
                            <span class="badge bg-danger">Ocupado</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="border-bottom pb-2 mb-3">Información</h6>
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Este ayudante es utilizado en los servicios de mudanza.
                    </p>
                    <div class="small text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Registrado: {{ $ayudante->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection