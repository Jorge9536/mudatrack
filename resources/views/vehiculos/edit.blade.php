@extends('layouts.app')

@section('title', 'Editar Vehículo')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('vehiculos.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Vehículo</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Placa</label>
                        <input type="text" name="placa" class="form-control @error('placa') is-invalid @enderror" 
                               value="{{ old('placa', $vehiculo->placa) }}" required>
                        @error('placa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Marca</label>
                        <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                               value="{{ old('marca', $vehiculo->marca) }}" required>
                        @error('marca')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required">Modelo</label>
                        <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                               value="{{ old('modelo', $vehiculo->modelo) }}" required>
                        @error('modelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Tipo</label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            <option value="3ton" {{ old('tipo', $vehiculo->tipo) == '3ton' ? 'selected' : '' }}>3 Toneladas</option>
                            <option value="6ton" {{ old('tipo', $vehiculo->tipo) == '6ton' ? 'selected' : '' }}>6 Toneladas</option>
                            <option value="chata" {{ old('tipo', $vehiculo->tipo) == 'chata' ? 'selected' : '' }}>Chata</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Capacidad (kg)</label>
                        <input type="number" name="capacidad_kg" class="form-control @error('capacidad_kg') is-invalid @enderror" 
                               value="{{ old('capacidad_kg', $vehiculo->capacidad_kg) }}" min="1" required>
                        @error('capacidad_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Disponible</label>
                    <select name="disponible" class="form-select">
                        <option value="1" {{ old('disponible', $vehiculo->disponible) ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ old('disponible', $vehiculo->disponible) ? '' : 'selected' }}>No</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $vehiculo->observaciones) }}</textarea>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar Vehículo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection