@extends('layouts.app')

@section('title', 'Ayudantes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="fas fa-user-friends me-2 text-primary"></i>Gestión de Ayudantes</h1>
            <small class="text-muted">Personal de apoyo para mudanzas</small>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('ayudantes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Ayudante
        </a>
        @endif
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Total Ayudantes</p>
                    <h5 class="mb-0">{{ $ayudantes->total() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Disponibles</p>
                    <h5 class="mb-0">{{ $ayudantes->where('disponible', true)->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Ocupados</p>
                    <h5 class="mb-0">{{ $ayudantes->where('disponible', false)->count() }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2">
                    <p class="small mb-0 opacity-75">Disponibilidad</p>
                    <h5 class="mb-0">
                        {{ $ayudantes->total() > 0 ? round(($ayudantes->where('disponible', true)->count() / $ayudantes->total()) * 100) : 0 }}%
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Lista de Ayudantes</h6>
            <span class="badge bg-primary">{{ $ayudantes->total() }} registros</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ayudantes as $ayudante)
                        <tr>
                            <td>#{{ $ayudante->id }}</td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $ayudante->nombre_completo }}
                            </td>
                            <td>{{ $ayudante->telefono }}</td>
                            <td>
                                @if($ayudante->disponible)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Ocupado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ayudantes.show', $ayudante) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('ayudantes.edit', $ayudante) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('ayudantes.toggle-disponibilidad', $ayudante) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $ayudante->disponible ? 'btn-danger' : 'btn-success' }}">
                                        <i class="fas {{ $ayudante->disponible ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('ayudantes.destroy', $ayudante) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('¿Eliminar ayudante {{ $ayudante->nombre_completo }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-user-friends fa-3x d-block mb-2"></i>
                                <p>No hay ayudantes registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $ayudantes->links() }}
        </div>
    </div>
</div>
@endsection