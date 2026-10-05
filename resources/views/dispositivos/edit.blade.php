@extends('layouts.app')

@section('title', 'Editar Dispositivo')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dispositivos.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Dispositivo</h1>
        <span class="badge bg-primary ms-2">{{ $dispositivo->dispositivo_id }}</span>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('dispositivos.update', $dispositivo) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label required">ID del Dispositivo</label>
                            <input type="text" name="dispositivo_id" class="form-control @error('dispositivo_id') is-invalid @enderror" 
                                   value="{{ old('dispositivo_id', $dispositivo->dispositivo_id) }}" required>
                            @error('dispositivo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Chofer</label>
                            <select name="chofer_id" class="form-select @error('chofer_id') is-invalid @enderror" required>
                                <option value="">Seleccione un chofer...</option>
                                @foreach($choferes as $chofer)
                                    <option value="{{ $chofer->id }}" {{ old('chofer_id', $dispositivo->chofer_id) == $chofer->id ? 'selected' : '' }}>
                                        {{ $chofer->nombre_completo }} - Lic. {{ $chofer->licencia }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chofer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vehículo (Opcional)</label>
                            <select name="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror">
                                <option value="">Sin vehículo asignado</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id', $dispositivo->vehiculo_id) == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo"
                                       {{ old('activo', $dispositivo->activo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Dispositivo activo</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 border-top pt-3">
                            <a href="{{ route('dispositivos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar Dispositivo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection