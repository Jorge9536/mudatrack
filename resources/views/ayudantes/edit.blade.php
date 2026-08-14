@extends('layouts.app')

@section('title', 'Editar Ayudante')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('ayudantes.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Ayudante</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('ayudantes.update', $ayudante) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Nombre Completo</label>
                        <input type="text" name="nombre_completo" class="form-control @error('nombre_completo') is-invalid @enderror" 
                               value="{{ old('nombre_completo', $ayudante->nombre_completo) }}" required>
                        @error('nombre_completo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                               value="{{ old('telefono', $ayudante->telefono) }}" required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Disponible</label>
                    <select name="disponible" class="form-select">
                        <option value="1" {{ old('disponible', $ayudante->disponible) ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ old('disponible', $ayudante->disponible) ? '' : 'selected' }}>No</option>
                    </select>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('ayudantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Actualizar Ayudante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection