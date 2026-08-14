@extends('layouts.app')

@section('title', 'Asignar Personal')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('servicios.show', $servicio) }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-users me-2 text-primary"></i>Asignación de Personal</h1>
        <span class="badge bg-secondary ms-2">Servicio #{{ $servicio->id }}</span>
    </div>

    <div class="row">
        <!-- Datos del Servicio -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Servicio #{{ $servicio->id }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cliente</span>
                        <strong>{{ $servicio->cliente->nombre_completo }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Origen</span>
                        <strong>{{ $servicio->origen }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Destino</span>
                        <strong>{{ $servicio->destino }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Fecha</span>
                        <strong>{{ $servicio->fecha_servicio->format('d/m/Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estado</span>
                        <span class="badge bg-warning text-dark">{{ $servicio->estado_label }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Costo Total</span>
                        <strong class="text-primary">{{ number_format($servicio->costo_total, 2) }} Bs</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Ayudantes requeridos</span>
                        <strong>{{ $servicio->cantidad_ayudantes }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asignación -->
        <div class="col-lg-8">
            <form action="{{ route('servicios.asignar', $servicio) }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Vehículo -->
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-truck me-2 text-primary"></i>Vehículo
                        </h6>
                        <div class="row g-2 mb-3">
                            <select name="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror" required>
                                <option value="">Seleccione un vehículo...</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id', $servicio->vehiculo_id) == $vehiculo->id ? 'selected' : '' }}>
                                        {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }} 
                                        <span class="badge bg-secondary">{{ $vehiculo->tipo }}</span>
                                        @if($vehiculo->disponible) ✅ Disponible @else ❌ Ocupado @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Chofer -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-user-circle me-2 text-primary"></i>Chofer
                        </h6>
                        <div class="row g-2 mb-3">
                            <select name="chofer_id" class="form-select @error('chofer_id') is-invalid @enderror" required>
                                <option value="">Seleccione un chofer...</option>
                                @foreach($choferes as $chofer)
                                    <option value="{{ $chofer->id }}" {{ old('chofer_id', $servicio->chofer_id) == $chofer->id ? 'selected' : '' }}>
                                        {{ $chofer->nombre_completo }} - Lic. {{ $chofer->licencia }}
                                        @if($chofer->disponible) ✅ Disponible @else ❌ Ocupado @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('chofer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ayudantes (si hay) -->
                        @if($ayudantes->count() > 0)
                        <h6 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-user-friends me-2 text-primary"></i>Ayudantes (Opcional)
                        </h6>
                        <div class="row g-2 mb-3">
                            @foreach($ayudantes as $ayudante)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="ayudantes[]" value="{{ $ayudante->id }}" 
                                           class="form-check-input" id="ayudante_{{ $ayudante->id }}">
                                    <label class="form-check-label" for="ayudante_{{ $ayudante->id }}">
                                        {{ $ayudante->nombre_completo }}
                                        <span class="badge bg-success">Disponible</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="d-flex gap-2 border-top pt-3">
                            <a href="{{ route('servicios.show', $servicio) }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i> Confirmar Asignación
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection