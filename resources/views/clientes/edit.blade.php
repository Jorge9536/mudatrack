@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('clientes.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Editar Cliente</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Nombre Completo</label>
                        <input type="text" name="nombre_completo" class="form-control @error('nombre_completo') is-invalid @enderror" 
                               value="{{ old('nombre_completo', $cliente->nombre_completo) }}" required>
                        @error('nombre_completo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                               value="{{ old('telefono', $cliente->telefono) }}" required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion) }}">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitud (GPS)</label>
                        <input type="text" name="latitud" class="form-control" value="{{ old('latitud', $cliente->latitud) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitud (GPS)</label>
                        <input type="text" name="longitud" class="form-control" value="{{ old('longitud', $cliente->longitud) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $cliente->observaciones) }}</textarea>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection