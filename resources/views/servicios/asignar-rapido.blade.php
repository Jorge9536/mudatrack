<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Servicio #{{ $servicio->id }}</strong> - 
                Cliente: {{ $servicio->cliente->nombre_completo }}
                <br>
                <small>
                    📅 {{ $servicio->fecha_servicio->format('d/m/Y') }} 
                    🕐 {{ $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : 'N/A' }} - 
                    {{ $servicio->hora_fin ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i') : 'N/A' }}
                </small>
            </div>

            <form id="formAsignarRapido" action="{{ route('servicios.asignar', $servicio) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">🚛 Vehículo</label>
                        <select name="vehiculo_id" class="form-select" required>
                            <option value="">Seleccione un vehículo...</option>
                            @foreach($vehiculosDisponibles as $vehiculo)
                                <option value="{{ $vehiculo->id }}">
                                    {{ $vehiculo->placa }} - {{ $vehiculo->marca }}
                                    <span class="badge bg-success">✅ Disponible</span>
                                </option>
                            @endforeach
                            @if($vehiculosDisponibles->count() == 0)
                                <option value="" disabled class="text-danger">❌ No hay vehículos disponibles en este horario</option>
                            @endif
                        </select>
                        @if($vehiculosDisponibles->count() == 0)
                            <small class="text-danger">Todos los vehículos están ocupados en este horario</small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">👨‍✈️ Chofer</label>
                        <select name="chofer_id" class="form-select" required>
                            <option value="">Seleccione un chofer...</option>
                            @foreach($choferesDisponibles as $chofer)
                                <option value="{{ $chofer->id }}">
                                    {{ $chofer->nombre_completo }} - Lic. {{ $chofer->licencia }}
                                    <span class="badge bg-success">✅ Disponible</span>
                                </option>
                            @endforeach
                            @if($choferesDisponibles->count() == 0)
                                <option value="" disabled class="text-danger">❌ No hay choferes disponibles en este horario</option>
                            @endif
                        </select>
                        @if($choferesDisponibles->count() == 0)
                            <small class="text-danger">Todos los choferes están ocupados en este horario</small>
                        @endif
                    </div>
                </div>

                @if($ayudantesDisponibles->count() > 0)
                <div class="mb-3">
                    <label class="form-label fw-bold">👷 Ayudantes</label>
                    <div class="row">
                        @foreach($ayudantesDisponibles as $ayudante)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="ayudantes[]" value="{{ $ayudante->id }}" 
                                       class="form-check-input" id="ayudante_{{ $ayudante->id }}">
                                <label class="form-check-label" for="ayudante_{{ $ayudante->id }}">
                                    {{ $ayudante->nombre_completo }}
                                    <span class="badge bg-success">✅</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No hay ayudantes disponibles en este horario
                </div>
                @endif

                <hr>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Asignar Personal
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>