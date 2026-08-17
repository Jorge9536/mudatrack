@extends('layouts.app')

@section('title', 'Choferes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-user-circle me-2 text-primary"></i>Gestión de Choferes</h1>
        <a href="{{ route('choferes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Chofer
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Licencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($choferes as $chofer)
                        <tr>
                            <td>#{{ $chofer->id }}</td>
                            <td>
                                <i class="fas fa-user-circle me-1 text-primary"></i>
                                {{ $chofer->nombre_completo }}
                            </td>
                            <td>{{ $chofer->telefono }}</td>
                            <td>{{ $chofer->licencia }}</td>
                            <td>
                                @if($chofer->disponible)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Ocupado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('choferes.show', $chofer) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('choferes.edit', $chofer) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Botón de eliminar con modal -->
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $chofer->id }}" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <!-- Modal de Confirmación de Eliminación -->
                                <div class="modal fade" id="deleteModal{{ $chofer->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $chofer->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $chofer->id }}">
                                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirmar Eliminación
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de que deseas eliminar al chofer <strong>{{ $chofer->nombre_completo }}</strong>?</p>
                                                <p class="text-muted small">Esta acción no se puede deshacer.</p>
                                                @if($chofer->servicios->count() > 0)
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Este chofer tiene {{ $chofer->servicios->count() }} servicio(s) asociado(s).
                                                        Al eliminarlo, también se eliminarán los servicios relacionados.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <form action="{{ route('choferes.destroy', $chofer) }}" method="POST" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash me-1"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No hay choferes registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $choferes->links() }}
        </div>
    </div>
</div>
@endsection