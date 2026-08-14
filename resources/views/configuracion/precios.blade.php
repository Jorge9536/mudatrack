@extends('layouts.app')

@section('title', 'Configuración de Precios')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-cog me-2 text-primary"></i>Configuración de Precios</h1>
        <span class="badge bg-danger ms-2">Solo Admin</span>
    </div>

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Importante:</strong> Los cambios aquí afectan todas las cotizaciones futuras. 
        Los precios ya cotizados no se modifican automáticamente.
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Precios Base por Zona</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('configuracion.precios.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Precios por Zona -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">La Paz → La Paz</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" name="precio_la_paz" class="form-control" 
                                           value="{{ $config->precio_la_paz }}" step="5" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">El Alto → El Alto</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" name="precio_el_alto" class="form-control" 
                                           value="{{ $config->precio_el_alto }}" step="5" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">El Alto ↔ La Paz</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" name="precio_el_alto_la_paz" class="form-control" 
                                           value="{{ $config->precio_el_alto_la_paz }}" step="5" min="0">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Costos Adicionales -->
                        <h6 class="mb-3"><i class="fas fa-plus-circle me-2 text-primary"></i>Costos Adicionales</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Por Ayudante</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" name="costo_ayudante" class="form-control" 
                                           value="{{ $config->costo_ayudante }}" step="5" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Piso Adicional</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" name="costo_piso_adicional" class="form-control" 
                                           value="{{ $config->costo_piso_adicional }}" step="5" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Callejón</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" name="costo_callejon" class="form-control" 
                                           value="{{ $config->costo_callejon }}" step="5" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Km Extra (más de 10km)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs</span>
                                    <input type="number" name="costo_km_extra" class="form-control" 
                                           value="{{ $config->costo_km_extra }}" step="1" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 border-top pt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Configuración
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary ms-2">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Resumen Actual</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>La Paz → La Paz</td>
                                    <td><strong>{{ number_format($config->precio_la_paz, 2) }} Bs</strong></td>
                                </tr>
                                <tr>
                                    <td>El Alto → El Alto</td>
                                    <td><strong>{{ number_format($config->precio_el_alto, 2) }} Bs</strong></td>
                                </tr>
                                <tr>
                                    <td>El Alto ↔ La Paz</td>
                                    <td><strong>{{ number_format($config->precio_el_alto_la_paz, 2) }} Bs</strong></td>
                                </tr>
                                <tr>
                                    <td>Ayudante</td>
                                    <td><strong>{{ number_format($config->costo_ayudante, 2) }} Bs</strong></td>
                                </tr>
                                <tr>
                                    <td>Piso Adicional</td>
                                    <td><strong>{{ number_format($config->costo_piso_adicional, 2) }} Bs</strong></td>
                                </tr>
                                <tr>
                                    <td>Callejón</td>
                                    <td><strong>{{ number_format($config->costo_callejon, 2) }} Bs</strong></td>
                                </tr>
                                <tr>
                                    <td>Km Extra</td>
                                    <td><strong>{{ number_format($config->costo_km_extra, 2) }} Bs</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info small mt-2 mb-0">
                        <i class="fas fa-clock me-1"></i>
                        Última actualización: {{ $config->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection