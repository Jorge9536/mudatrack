@extends('layouts.app')

@section('title', 'Editar Chofer')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('choferes.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Editar Chofer</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('choferes.update', $chofer) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Nombre Completo</label>
                        <input type="text" name="nombre_completo" class="form-control @error('nombre_completo') is-invalid @enderror" 
                               value="{{ old('nombre_completo', $chofer->nombre_completo) }}" required>
                        @error('nombre_completo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                               value="{{ old('telefono', $chofer->telefono) }}" required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Licencia</label>
                    <input type="text" name="licencia" class="form-control @error('licencia') is-invalid @enderror" 
                           value="{{ old('licencia', $chofer->licencia) }}" required>
                    @error('licencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="disponible" class="form-select @error('disponible') is-invalid @enderror">
                        <option value="1" {{ old('disponible', $chofer->disponible) ? 'selected' : '' }}>Disponible</option>
                        <option value="0" {{ old('disponible', $chofer->disponible) ? '' : 'selected' }}>Ocupado</option>
                    </select>
                    @error('disponible')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $chofer->observaciones) }}</textarea>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('choferes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar Chofer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection