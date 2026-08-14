@extends('layouts.app')

@section('title', 'Vehículos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-truck me-2 text-primary"></i>Gestión de Vehículos</h1>
        <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Vehículo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Placa</th>
                            <th>Marca/Modelo</th>
                            <th>Tipo</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiculos as $vehiculo)
                        <tr>
                            <td><strong>{{ $vehiculo->placa }}</strong></td>
                            <td>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                            <td><span class="badge bg-secondary">{{ $vehiculo->tipo }}</span></td>
                            <td>{{ $vehiculo->capacidad_kg }} kg</td>
                            <td>
                                @if($vehiculo->disponible)
                                    <span class="badge bg-success">Disponible</span>
                                @else
                                    <span class="badge bg-danger">Ocupado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('vehiculos.toggle-disponibilidad', $vehiculo) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $vehiculo->disponible ? 'btn-danger' : 'btn-success' }}">
                                        <i class="fas {{ $vehiculo->disponible ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No hay vehículos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $vehiculos->links() }}
        </div>
    </div>
</div>
@endsection