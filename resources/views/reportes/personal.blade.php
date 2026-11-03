@extends('layouts.app')

@section('title', 'Reporte por Personal')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <a href="{{ route('reportes.index') }}" class="text-decoration-none text-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-users me-2 text-primary"></i>Reporte por Personal</h1>
                <small class="text-muted">Productividad y actividad de choferes y ayudantes</small>
            </div>
        </div>
        <a href="{{ route('reportes.personal.exportar', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel me-1"></i> Exportar Excel
        </a>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.personal') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ $fechaFin }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Tipo</label>
                    <select name="tipo" class="form-select form-select-sm">
                        <option value="todos"    {{ $tipo === 'todos'    ? 'selected' : '' }}>Todos</option>
                        <option value="chofer"   {{ $tipo === 'chofer'   ? 'selected' : '' }}>Solo Choferes</option>
                        <option value="ayudante" {{ $tipo === 'ayudante' ? 'selected' : '' }}>Solo Ayudantes</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search me-1"></i> Filtrar</button>
                    <a href="{{ route('reportes.personal') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-start border-4 border-primary shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Total Servicios</p>
                    <h4 class="mb-0">{{ $resumen['total_servicios'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-start border-4 border-info shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Total Horas</p>
                    <h4 class="mb-0">{{ $resumen['total_horas'] }} h</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-start border-4 border-success shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Ingresos Totales</p>
                    <h4 class="mb-0 text-success">{{ number_format($resumen['total_ingresos'], 2) }} Bs</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-start border-4 border-warning shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small text-muted mb-1">Prom. horas/servicio</p>
                    <h4 class="mb-0 text-warning">{{ $resumen['promedio_horas'] }} h</h4>
                </div>
            </div>
        </div>
    </div>
    @if($topChoferes->isNotEmpty() || $topAyudantes->isNotEmpty())
    <div class="row g-3 mb-4">
        @if($topChoferes->isNotEmpty())
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 small"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Choferes por Ingresos</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;" class="text-center">#</th>
                                <th>Chofer</th>
                                <th class="text-center">Serv.</th>
                                <th class="text-center">Horas</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topChoferes as $i => $ch)
                            <tr>
                                <td class="text-center {{ $i === 0 ? 'fw-bold text-dark' : 'text-muted' }}">
                                    {{ $i + 1 }}
                                </td>
                                <td>{{ $ch['nombre'] }}</td>
                                <td class="text-center">{{ $ch['total_servicios'] }}</td>
                                <td class="text-center">{{ $ch['total_horas'] }}h</td>
                                <td class="text-end"><strong>{{ number_format($ch['total_ingresos'], 2) }} Bs</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($topAyudantes->isNotEmpty())
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 small"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Ayudantes por Ingresos</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;" class="text-center">#</th>
                                <th>Ayudante</th>
                                <th class="text-center">Serv.</th>
                                <th class="text-center">Horas</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topAyudantes as $i => $ay)
                            <tr>
                                <td class="text-center {{ $i === 0 ? 'fw-bold text-dark' : 'text-muted' }}">
                                    {{ $i + 1 }}
                                </td>
                                <td>{{ $ay['nombre'] }}</td>
                                <td class="text-center">{{ $ay['total_servicios'] }}</td>
                                <td class="text-center">{{ $ay['total_horas'] }}h</td>
                                <td class="text-end"><strong>{{ number_format($ay['total_ingresos'], 2) }} Bs</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
    @if(in_array($tipo, ['todos', 'chofer']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Choferes</h6>
                <span class="badge bg-light text-primary">{{ $choferes->count() }} con actividad</span>
            </div>
            <div class="card-body p-0">
                @forelse($choferes as $ch)
                    <div class="border-bottom">
                        <div class="p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <strong class="d-block">
                                        <i class="fas fa-user me-1 text-primary"></i>{{ $ch['nombre'] }}
                                    </strong>
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>{{ $ch['telefono'] }}
                                    </small>
                                    @if(!empty($ch['vehiculos']))
                                        <small class="text-muted ms-3">
                                            <i class="fas fa-truck me-1"></i>{{ implode(', ', $ch['vehiculos']) }}
                                        </small>
                                    @endif
                                </div>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="text-center">
                                        <div class="small text-muted">Servicios</div>
                                        <div class="h5 mb-0">{{ $ch['total_servicios'] }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="small text-muted">Horas</div>
                                        <div class="h5 mb-0">{{ $ch['total_horas'] }}h</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="small text-muted">Ingresos</div>
                                        <div class="h5 mb-0 text-success">{{ number_format($ch['total_ingresos'], 2) }} Bs</div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($ch['ayudantes']))
                                <div class="mt-2 small">
                                    <i class="fas fa-users text-muted me-1"></i>
                                    <span class="text-muted">Trabajó con:</span>
                                    {{ implode(', ', $ch['ayudantes']) }}
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" style="font-size: 0.82rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Horas</th>
                                        <th>Cliente</th>
                                        <th>Origen → Destino</th>
                                        <th>Vehículo</th>
                                        <th>Ayudantes</th>
                                        <th class="text-end">Costo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ch['servicios'] as $s)
                                    <tr>
                                        <td>#{{ $s['id'] }}</td>
                                        <td>{{ $s['fecha'] }}</td>
                                        <td><small>{{ $s['hora_inicio'] }} - {{ $s['hora_fin'] }}</small></td>
                                        <td class="text-center"><span class="badge bg-info">{{ $s['horas'] }}h</span></td>
                                        <td>{{ Str::limit($s['cliente'], 20) }}</td>
                                        <td>
                                            <small>
                                                {{ Str::limit($s['origen'], 18) }} → {{ Str::limit($s['destino'], 18) }}
                                            </small>
                                        </td>
                                        <td><small>{{ $s['vehiculo'] }}</small></td>
                                        <td>
                                            <small>
                                                {{ !empty($s['ayudantes']) ? implode(', ', $s['ayudantes']) : '—' }}
                                            </small>
                                        </td>
                                        <td class="text-end"><strong>{{ number_format($s['costo'], 2) }}</strong></td>
                                        <td><span class="badge bg-secondary">{{ $s['estado'] }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>
                        No hay actividad de choferes en este período
                    </div>
                @endforelse
            </div>
        </div>
    @endif
    @if(in_array($tipo, ['todos', 'ayudante']))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-hands-helping me-2"></i>Ayudantes</h6>
                <span class="badge bg-light text-info">{{ $ayudantes->count() }} con actividad</span>
            </div>
            <div class="card-body p-0">
                @forelse($ayudantes as $ay)
                    <div class="border-bottom">
                        <div class="p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <strong class="d-block">
                                        <i class="fas fa-user me-1 text-info"></i>{{ $ay['nombre'] }}
                                    </strong>
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>{{ $ay['telefono'] }}
                                    </small>
                                    @if(!empty($ay['vehiculos']))
                                        <small class="text-muted ms-3">
                                            <i class="fas fa-truck me-1"></i>{{ implode(', ', $ay['vehiculos']) }}
                                        </small>
                                    @endif
                                </div>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="text-center">
                                        <div class="small text-muted">Servicios</div>
                                        <div class="h5 mb-0">{{ $ay['total_servicios'] }}</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="small text-muted">Horas</div>
                                        <div class="h5 mb-0">{{ $ay['total_horas'] }}h</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="small text-muted">Ingresos</div>
                                        <div class="h5 mb-0 text-success">{{ number_format($ay['total_ingresos'], 2) }} Bs</div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($ay['choferes']))
                                <div class="mt-2 small">
                                    <i class="fas fa-id-card text-muted me-1"></i>
                                    <span class="text-muted">Trabajó con:</span>
                                    <strong>{{ implode(', ', $ay['choferes']) }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" style="font-size: 0.82rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Horas</th>
                                        <th>Cliente</th>
                                        <th>Origen → Destino</th>
                                        <th>Vehículo</th>
                                        <th>Chofer</th>
                                        <th class="text-end">Costo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ay['servicios'] as $s)
                                    <tr>
                                        <td>#{{ $s['id'] }}</td>
                                        <td>{{ $s['fecha'] }}</td>
                                        <td><small>{{ $s['hora_inicio'] }} - {{ $s['hora_fin'] }}</small></td>
                                        <td class="text-center"><span class="badge bg-info">{{ $s['horas'] }}h</span></td>
                                        <td>{{ Str::limit($s['cliente'], 20) }}</td>
                                        <td>
                                            <small>
                                                {{ Str::limit($s['origen'], 18) }} → {{ Str::limit($s['destino'], 18) }}
                                            </small>
                                        </td>
                                        <td><small>{{ $s['vehiculo'] }}</small></td>
                                        <td><small>{{ $s['chofer'] }}</small></td>
                                        <td class="text-end"><strong>{{ number_format($s['costo'], 2) }}</strong></td>
                                        <td><span class="badge bg-secondary">{{ $s['estado'] }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>
                        No hay actividad de ayudantes en este período
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection