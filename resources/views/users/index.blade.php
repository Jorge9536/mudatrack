@extends('layouts.app')

@section('title', 'Usuarios')

@push('styles')
<style>
    .pagination { margin-bottom: 0; }
    .pagination .page-link { padding: 0.25rem 0.6rem; font-size: 0.85rem; }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .user-avatar.admin { background: linear-gradient(135deg, #ef4444, #f97316); }
    .user-avatar.recepcionista { background: linear-gradient(135deg, #3b82f6, #0ea5e9); }
    .user-avatar.chofer { background: linear-gradient(135deg, #f59e0b, #eab308); }
    .user-avatar.ayudante { background: linear-gradient(135deg, #06b6d4, #0ea5e9); }

    .user-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-icon-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 15px;
    }
    .modal-icon-large.bg-primary-soft  { background: #dbeafe; color: #2563eb; }
    .modal-icon-large.bg-danger-soft   { background: #fee2e2; color: #dc2626; }
    .modal-icon-large.bg-success-soft  { background: #d1fae5; color: #059669; }

    .modal-header-colored { color: #fff; }

    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    .toast-custom {
        min-width: 300px;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
        font-family: 'Segoe UI', sans-serif;
        animation: slideInToast 0.3s ease;
        margin-bottom: 10px;
    }
    .toast-custom.toast-success { background: linear-gradient(135deg, #198754, #20c997); }
    .toast-custom.toast-error   { background: linear-gradient(135deg, #dc3545, #f87171); }
    .toast-custom.toast-info    { background: linear-gradient(135deg, #0dcaf0, #3b82f6); }
    .toast-custom i { font-size: 1.5rem; }
    .toast-custom .toast-text { flex: 1; font-size: 0.9rem; }
    .toast-custom .toast-close {
        background: none; border: none; color: #fff;
        font-size: 1.2rem; cursor: pointer; opacity: 0.8;
    }
    .toast-custom .toast-close:hover { opacity: 1; }

    @keyframes slideInToast {
        from { transform: translateX(120%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
    @keyframes slideOutToast {
        from { transform: translateX(0);    opacity: 1; }
        to   { transform: translateX(120%); opacity: 0; }
    }
</style>
@endpush

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

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Total Usuarios</p>
                    <h5 class="mb-0">{{ $users->total() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-danger text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Administradores</p>
                    <h5 class="mb-0">{{ $users->where('role', 'admin')->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-primary text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Recepcionistas</p>
                    <h5 class="mb-0">{{ $users->where('role', 'recepcionista')->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Choferes</p>
                    <h5 class="mb-0">{{ $users->where('role', 'chofer')->count() }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Lista de Usuarios</h6>
            <span class="badge bg-primary">{{ $users->total() }} registros</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td><strong>#{{ $user->id }}</strong></td>
                            <td>
                                <div class="user-row">
                                    <div class="user-avatar {{ $user->role }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        @if($user->id === 1)
                                            <span class="badge bg-danger">Principal</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-envelope text-muted me-1"></i>
                                <small>{{ $user->email }}</small>
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->role === 'recepcionista')
                                    <span class="badge bg-primary">Recepcionista</span>
                                @elseif($user->role === 'chofer')
                                    <span class="badge bg-warning text-dark">Chofer</span>
                                @elseif($user->role === 'ayudante')
                                    <span class="badge bg-info text-dark">Ayudante</span>
                                @else
                                    <span class="badge bg-secondary">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($user->id !== 1)
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button class="btn btn-sm btn-success"
                                            title="Resetear Contraseña"
                                            onclick="abrirModalReset({{ $user->id }}, '{{ $user->name }}')">
                                        <i class="fas fa-key"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger"
                                            title="Eliminar"
                                            onclick="abrirModalEliminar({{ $user->id }}, '{{ $user->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="form-reset-{{ $user->id }}"
                                          action="{{ route('users.reset-password', $user) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                        <input type="hidden" name="password" id="input-reset-pass-{{ $user->id }}">
                                        <input type="hidden" name="password_confirmation" id="input-reset-conf-{{ $user->id }}">
                                    </form>

                                    <form id="form-eliminar-{{ $user->id }}"
                                          action="{{ route('users.destroy', $user) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @else
                                    <span class="text-muted small ms-2">(Protegido)</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x d-block mb-2 opacity-50"></i>
                                <p class="mb-0">No hay usuarios registrados</p>
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

<div class="modal fade" id="modalResetPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title">
                    <i class="fas fa-key me-2"></i>Resetear Contraseña
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResetPassword" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="modal-icon-large bg-primary-soft">
                        <i class="fas fa-key"></i>
                    </div>

                    <p class="text-center mb-3">
                        Cambiando contraseña de<br>
                        <strong id="nombreUsuarioReset">--</strong>
                    </p>

                    <div class="mb-3">
                        <label class="form-label required">Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password"
                                   name="password"
                                   id="resetPassword"
                                   class="form-control"
                                   required
                                   minlength="6"
                                   placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password"
                                   name="password_confirmation"
                                   id="resetPasswordConfirm"
                                   class="form-control"
                                   required
                                   placeholder="Repite la contraseña">
                        </div>
                    </div>

                    <div id="mensajeErrorReset" class="alert alert-danger d-none small mb-0">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="enviarResetPassword()">
                        <i class="fas fa-save me-1"></i> Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Eliminar Usuario
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="modal-icon-large bg-danger-soft">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="mb-2">¿Estás seguro de que quieres eliminar a este usuario?</p>
                <div class="alert alert-light border mb-3">
                    <strong id="nombreUsuarioEliminar">--</strong>
                </div>
                <div class="alert alert-warning mb-0 small text-start">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta acción <strong>no se puede deshacer</strong>. El usuario perderá acceso al sistema inmediatamente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminar()">
                    <i class="fas fa-trash me-1"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

@endsection

@push('scripts')
<script>
function mostrarToast(tipo, mensaje) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'toast-custom toast-' + tipo;

    const iconos = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'info': 'fa-info-circle'
    };

    toast.innerHTML = `
        <i class="fas ${iconos[tipo] || iconos.info}"></i>
        <div class="toast-text">${mensaje}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutToast 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

let usuarioResetId = null;

function abrirModalReset(id, nombre) {
    usuarioResetId = id;
    document.getElementById('nombreUsuarioReset').textContent = nombre;
    document.getElementById('resetPassword').value = '';
    document.getElementById('resetPasswordConfirm').value = '';
    document.getElementById('mensajeErrorReset').classList.add('d-none');

    document.getElementById('formResetPassword').action = `/users/${id}/reset-password`;

    new bootstrap.Modal(document.getElementById('modalResetPassword')).show();
}

function enviarResetPassword() {
    const pass = document.getElementById('resetPassword').value;
    const confirm = document.getElementById('resetPasswordConfirm').value;
    const msg = document.getElementById('mensajeErrorReset');

    if (pass.length < 6) {
        msg.querySelector('span').textContent = 'La contraseña debe tener al menos 6 caracteres';
        msg.classList.remove('d-none');
        return;
    }

    if (pass !== confirm) {
        msg.querySelector('span').textContent = 'Las contraseñas no coinciden';
        msg.classList.remove('d-none');
        return;
    }

    msg.classList.add('d-none');
    document.getElementById('formResetPassword').submit();
}

let usuarioEliminarId = null;

function abrirModalEliminar(id, nombre) {
    usuarioEliminarId = id;
    document.getElementById('nombreUsuarioEliminar').textContent = nombre;

    new bootstrap.Modal(document.getElementById('modalEliminarUsuario')).show();
}

function confirmarEliminar() {
    if (!usuarioEliminarId) return;

    document.getElementById('form-eliminar-' + usuarioEliminarId).submit();
}
</script>
@endpush