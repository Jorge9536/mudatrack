@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-users-cog me-2 text-primary"></i>Gestión de Usuarios</h1>
            <small class="text-muted">Administración de cuentas del sistema</small>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
        </a>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Total Usuarios</p>
                    <h5 class="mb-0">{{ $users->total() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Administradores</p>
                    <h5 class="mb-0">{{ $users->where('role', 'admin')->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Recepcionistas</p>
                    <h5 class="mb-0">{{ $users->where('role', 'recepcionista')->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Choferes</p>
                    <h5 class="mb-0">{{ $users->where('role', 'chofer')->count() }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Lista de Usuarios</h6>
            <span class="badge bg-primary">{{ $users->total() }} registros</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $user->name }}
                                @if($user->id === 1)
                                    <span class="badge bg-danger ms-1">Principal</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->role === 'recepcionista')
                                    <span class="badge bg-primary">Recepcionista</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chofer</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($user->id !== 1)
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalResetPass{{ $user->id }}">
                                    <i class="fas fa-key"></i>
                                </button>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('¿Eliminar usuario {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">(Protegido)</span>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Modal Reset Password -->
                        @if($user->id !== 1)
                        <div class="modal fade" id="modalResetPass{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('users.reset-password', $user) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h6 class="modal-title"><i class="fas fa-key me-2 text-primary"></i>Resetear Contraseña</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Usuario:</strong> {{ $user->name }}</p>
                                            <div class="mb-3">
                                                <label class="form-label">Nueva Contraseña</label>
                                                <input type="password" name="password" class="form-control" required minlength="6">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Confirmar Contraseña</label>
                                                <input type="password" name="password_confirmation" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Actualizar Contraseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x d-block mb-2"></i>
                                <p>No hay usuarios registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection