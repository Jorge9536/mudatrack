@extends('layouts.app')

@section('title', 'Ver Usuario')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-user me-2 text-primary"></i>Usuario: {{ $user->name }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Datos del Usuario</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ID</span>
                        <strong>#{{ $user->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nombre</span>
                        <strong>{{ $user->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Email</span>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Rol</span>
                        <strong>
                            @if($user->role === 'admin')
                                <span class="badge bg-danger">Administrador</span>
                            @elseif($user->role === 'recepcionista')
                                <span class="badge bg-primary">Recepcionista</span>
                            @else
                                <span class="badge bg-warning text-dark">Chofer</span>
                            @endif
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Creado</span>
                        <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Información</h6>
                    @if($user->id === 1)
                        <div class="alert alert-danger">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Usuario Principal</strong> - Este es el administrador principal del sistema. No puede ser eliminado.
                        </div>
                    @endif
                    <div class="small text-muted">
                        <p><i class="fas fa-info-circle me-1"></i> Última actualización: {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection