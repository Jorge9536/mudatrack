@extends('layouts.app')

@section('title', 'Clientes Morosos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <a href="{{ route('reportes.index') }}" class="text-decoration-none text-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h4 mb-0">
                    <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                    Clientes Morosos
                </h1>
                <small class="text-muted">Detalle de deudas pendientes y servicios asociados</small>
            </div>
        </div>
        <a href="{{ route('reportes.morosos.exportar') }}" class="btn btn-outline-success btn-sm">
            <i class="fas fa-file-excel me-1"></i> Exportar Excel
        </a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small mb-1 opacity-75">Clientes Morosos</p>
                    <h4 class="mb-0">{{ $morosos->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small mb-1 opacity-75">Total Adeudado</p>
                    <h4 class="mb-0">
                        {{ number_format($morosos->sum(fn($c) => $c->deudas->where('estado', 'pendiente')->sum('monto')), 2) }} Bs
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small mb-1 opacity-75">Deudas Pendientes</p>
                    <h4 class="mb-0">
                        {{ $morosos->sum(fn($c) => $c->deudas->where('estado', 'pendiente')->count()) }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card bg-secondary text-white shadow-sm h-100">
                <div class="card-body py-3">
                    <p class="small mb-1 opacity-75">Bloqueados</p>
                    <h4 class="mb-0">{{ $morosos->where('bloqueado', true)->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
    @forelse($morosos as $cliente)
        @php
            $deudas = $cliente->deudas->where('estado', 'pendiente');
            $totalCliente = $deudas->sum('monto');
        @endphp

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light py-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong>
                            <i class="fas fa-user me-1 text-danger"></i>
                            {{ $cliente->nombre_completo }}
                        </strong>
                        <span class="text-muted small ms-3">
                            <i class="fas fa-id-badge me-1"></i>ID #{{ $cliente->id }}
                        </span>
                        @if($cliente->telefono)
                            <span class="text-muted small ms-3">
                                <i class="fas fa-phone me-1"></i>{{ $cliente->telefono }}
                            </span>
                        @endif
                        @if($cliente->bloqueado)
                            <span class="badge bg-danger ms-2">Bloqueado</span>
                        @else
                            <span class="badge bg-warning text-dark ms-2">Activo</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end">
                            <div class="small text-muted">Deudas</div>
                            <div class="fw-bold text-danger">{{ $deudas->count() }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Total</div>
                            <div class="fw-bold text-danger">{{ number_format($totalCliente, 2) }} Bs</div>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('clientes.show', $cliente) }}"
                               class="btn btn-outline-secondary" title="Ver cliente">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente) }}"
                               class="btn btn-outline-secondary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.83rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Servicio</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th class="text-end">Monto</th>
                            <th>Fecha servicio</th>
                            <th>Origen → Destino</th>
                            <th>Vehículo</th>
                            <th>Chofer</th>
                            <th>Ayudantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deudas as $deuda)
                            @php
                                $dias = now()->diffInDays($deuda->fecha_vencimiento, false);
                                $vencida = $deuda->estaVencida();
                                $servicio = $deuda->servicio;
                            @endphp
                            <tr>
                                <td>
                                    @if($servicio)
                                        <a href="{{ route('servicios.show', $servicio) }}"
                                           class="text-decoration-none text-dark">
                                            #{{ $servicio->id }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $deuda->fecha_vencimiento->format('d/m/Y') }}
                                    <br>
                                    <small class="{{ $vencida ? 'text-danger fw-bold' : 'text-muted' }}">
                                        @if($vencida)
                                            Vencida hace {{ abs($dias) }} día(s)
                                        @else
                                            Vence en {{ $dias }} día(s)
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if($vencida)
                                        <span class="badge bg-danger">Vencida</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">{{ number_format($deuda->monto, 2) }} Bs</strong>
                                </td>
                                <td>
                                    @if($servicio)
                                        {{ optional($servicio->fecha_servicio)->format('d/m/Y') ?? '—' }}
                                        <br>
                                        <small class="text-muted">
                                            @if($servicio->hora_inicio && $servicio->hora_fin)
                                                {{ \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') }}
                                            @else
                                                —
                                            @endif
                                        </small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($servicio)
                                        <small>
                                            {{ Str::limit($servicio->origen, 22) }}
                                            →
                                            {{ Str::limit($servicio->destino, 22) }}
                                        </small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $servicio->vehiculo->placa ?? '—' }}</small>
                                </td>
                                <td>
                                    <small>{{ $servicio->chofer->nombre_completo ?? '—' }}</small>
                                </td>
                                <td>
                                    <small>
                                        @if($servicio && $servicio->ayudantes->isNotEmpty())
                                            {{ $servicio->ayudantes->pluck('nombre_completo')->join(', ') }}
                                        @else
                                            —
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total adeudado:</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($totalCliente, 2) }} Bs</td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @empty
        <div class="card shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-check-circle fa-3x d-block mb-3 text-success"></i>
                <p class="mb-0">No hay clientes morosos registrados</p>
            </div>
        </div>
    @endforelse

</div>
@endsection