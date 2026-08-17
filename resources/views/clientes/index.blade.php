@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-users me-2 text-primary"></i>Gestión de Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Cliente
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
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td>#{{ $cliente->id }}</td>
                            <td>
                                <i class="fas fa-user me-1 text-muted"></i>
                                {{ $cliente->nombre_completo }}
                            </td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>{{ $cliente->direccion ?? 'Sin registrar' }}</td>
                            <td>
                                @if($cliente->bloqueado)
                                    <span class="badge bg-danger">Bloqueado</span>
                                @else
                                    <span class="badge bg-success">Activo</span>
                                @endif
                                @if($cliente->estaBloqueado())
                                    <span class="badge bg-warning text-dark">Moroso</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clientes.toggle-bloqueo', $cliente) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $cliente->bloqueado ? 'btn-success' : 'btn-danger' }}">
                                        <i class="fas {{ $cliente->bloqueado ? 'fa-unlock' : 'fa-lock' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No hay clientes registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación reducida -->
            <div class="d-flex justify-content-center mt-3">
                @if($clientes->hasPages())
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Botón Anterior --}}
                            @if($clientes->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i> Anterior
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $clientes->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-left"></i> Anterior
                                    </a>
                                </li>
                            @endif

                            {{-- Información de página --}}
                            <li class="page-item active">
                                <span class="page-link">
                                    Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}
                                </span>
                            </li>

                            {{-- Botón Siguiente --}}
                            @if($clientes->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $clientes->nextPageUrl() }}" rel="next">
                                        Siguiente <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        Siguiente <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection