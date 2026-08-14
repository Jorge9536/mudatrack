@extends('layouts.app')

@section('title', 'Editar Servicio')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('servicios.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Servicio #{{ $servicio->id }}</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('servicios.update', $servicio) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Origen</label>
                        <input type="text" name="origen" class="form-control @error('origen') is-invalid @enderror" 
                               value="{{ old('origen', $servicio->origen) }}" required>
                        @error('origen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Destino</label>
                        <input type="text" name="destino" class="form-control @error('destino') is-invalid @enderror" 
                               value="{{ old('destino', $servicio->destino) }}" required>
                        @error('destino')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Fecha del Servicio</label>
                        <input type="date" name="fecha_servicio" class="form-control @error('fecha_servicio') is-invalid @enderror" 
                               value="{{ old('fecha_servicio', $servicio->fecha_servicio->format('Y-m-d')) }}" required>
                        @error('fecha_servicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Cantidad de Ayudantes</label>
                        <input type="number" name="cantidad_ayudantes" class="form-control @error('cantidad_ayudantes') is-invalid @enderror" 
                               value="{{ old('cantidad_ayudantes', $servicio->cantidad_ayudantes) }}" min="0" required>
                        @error('cantidad_ayudantes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Número de Pisos</label>
                        <input type="number" name="numero_pisos" class="form-control @error('numero_pisos') is-invalid @enderror" 
                               value="{{ old('numero_pisos', $servicio->numero_pisos) }}" min="1" required>
                        @error('numero_pisos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $servicio->observaciones) }}</textarea>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('servicios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection