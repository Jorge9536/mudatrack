@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('users.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Crear Nuevo Usuario</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Mostrar errores generales si los hay --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Nombre Completo</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Contraseña</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               required minlength="6">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Rol</label>
                        <select name="role" id="roleSelect" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Seleccione un rol...</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Campos extra para Chofer y Ayudante --}}
                <div id="camposExtra" style="display: none;">
                    <hr>
                    <h6 class="text-primary mb-3"><i class="fas fa-id-card me-1"></i> Datos del Personal</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Teléfono / Celular</label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                   value="{{ old('telefono') }}" placeholder="Ej. 77712345">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Campo exclusivo de Choferes --}}
                        <div class="col-md-6 mb-3" id="campoLicencia" style="display: none;">
                            <label class="form-label required">Licencia de Conducir</label>
                            <input type="text" name="licencia" id="licencia" class="form-control @error('licencia') is-invalid @enderror"
                                   value="{{ old('licencia') }}" placeholder="Ej. 12345678">
                            @error('licencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('roleSelect');
        const camposExtra = document.getElementById('camposExtra');
        const campoLicencia = document.getElementById('campoLicencia');
        const inputTelefono = document.getElementById('telefono');
        const inputLicencia = document.getElementById('licencia');

        function toggleCampos() {
            const rol = roleSelect.value;
            
            if (rol === 'chofer' || rol === 'ayudante') {
                camposExtra.style.display = 'block';
                inputTelefono.setAttribute('required', 'required');
            } else {
                camposExtra.style.display = 'none';
                inputTelefono.removeAttribute('required');
            }

            if (rol === 'chofer') {
                campoLicencia.style.display = 'block';
                inputLicencia.setAttribute('required', 'required');
            } else {
                campoLicencia.style.display = 'none';
                inputLicencia.removeAttribute('required');
            }
        }

        roleSelect.addEventListener('change', toggleCampos);
        
        // Ejecutar al cargar por si hay un error y se mantiene el rol seleccionado
        if (roleSelect.value) {
            toggleCampos();
        }
    });
</script>
@endpush