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
                                <a href="{{ route('choferes.show', $chofer) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('choferes.edit', $chofer) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
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