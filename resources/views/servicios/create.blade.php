@extends('layouts.app')

@section('title', 'Nuevo Servicio')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('servicios.index') }}" class="text-decoration-none text-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Nuevo Servicio</h1>
        <span class="badge bg-secondary ms-2">{{ auth()->user()->role }}</span>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('servicios.store') }}" method="POST" id="formServicio">
                        @csrf
                        
                        <!-- Datos del Cliente -->
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2 text-primary"></i>Datos del Cliente
                        </h6>
                        
                        <div class="mb-2">
                            <label class="form-label">Buscar Cliente</label>
                            <input type="text" id="buscadorCliente" class="form-control" placeholder="Escribe el nombre o teléfono para filtrar...">
                            <small class="text-muted">Escribe para buscar clientes</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required">Seleccionar Cliente</label>
                            <select name="cliente_id" id="clienteSelect" class="form-select @error('cliente_id') is-invalid @enderror" required size="4">
                                <option value="">-- Seleccione un cliente --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" 
                                            data-nombre="{{ strtolower($cliente->nombre_completo) }}"
                                            data-telefono="{{ $cliente->telefono }}"
                                            {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre_completo }} - {{ $cliente->telefono }}
                                        @if($cliente->estaBloqueado()) ⚠️ MOROSO @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Datos del Servicio -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-map-marked-alt me-2 text-primary"></i>Datos del Servicio
                        </h6>
                        
                        <!-- 🔥 MAPA PARA SELECCIONAR ORIGEN Y DESTINO -->
                        <div class="mb-3">
                            <label class="form-label">Selecciona en el mapa</label>
                            <div id="mapa" style="height: 350px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                Haz clic en el mapa para marcar Origen (🟢) y Destino (🔴)
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label required">Origen</label>
                                <input type="text" name="origen" id="origen" class="form-control @error('origen') is-invalid @enderror" 
                                       value="{{ old('origen') }}" placeholder="Ej. Av. 6 de Agosto, La Paz" required>
                                @error('origen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label required">Destino</label>
                                <input type="text" name="destino" id="destino" class="form-control @error('destino') is-invalid @enderror" 
                                       value="{{ old('destino') }}" placeholder="Ej. Calle 12, El Alto" required>
                                @error('destino')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Distancia</label>
                                <div class="input-group">
                                    <input type="number" name="distancia_km" id="distancia" 
                                           class="form-control @error('distancia_km') is-invalid @enderror" 
                                           value="{{ old('distancia_km', 0) }}" step="0.1" min="0" readonly>
                                    <span class="input-group-text">km</span>
                                </div>
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label required">Fecha</label>
                                <input type="date" name="fecha_servicio" class="form-control @error('fecha_servicio') is-invalid @enderror" 
                                       value="{{ old('fecha_servicio', date('Y-m-d', strtotime('+1 day'))) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label required">Ayudantes</label>
                                <input type="number" name="cantidad_ayudantes" id="ayudantes" 
                                       class="form-control" value="{{ old('cantidad_ayudantes', 0) }}" min="0" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label required">Pisos</label>
                                <input type="number" name="numero_pisos" id="pisos" 
                                       class="form-control" value="{{ old('numero_pisos', 1) }}" min="1" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Callejón</label>
                                <select name="es_callejon" id="callejon" class="form-select">
                                    <option value="0">No</option>
                                    <option value="1" {{ old('es_callejon') ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>
                        </div>

                        <!-- Lista de Bienes -->
                        <h6 class="border-bottom pb-2 mb-3 mt-4">
                            <i class="fas fa-boxes me-2 text-primary"></i>Lista de Bienes
                        </h6>
                        <div id="bienes-container">
                            <div class="row g-2 bien-item mb-2">
                                <div class="col-md-5">
                                    <input type="text" name="bienes[0][nombre]" class="form-control form-control-sm" placeholder="Nombre del bien" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="bienes[0][cantidad]" class="form-control form-control-sm" placeholder="Cantidad" value="1" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="bienes[0][descripcion]" class="form-control form-control-sm" placeholder="Descripción (opcional)">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarBien(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-3" onclick="agregarBien()">
                            <i class="fas fa-plus me-1"></i> Agregar Bien
                        </button>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                        </div>

                        <div class="d-flex gap-2 border-top pt-3">
                            <a href="{{ route('servicios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Servicio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Resumen de Cotización -->
        @if(auth()->user()->isAdmin())
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Cotización</h6>
                </div>
                <div class="card-body">
                    <div id="cotizacion-preview">
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-edit me-1"></i> Complete los datos del servicio
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-lock me-2"></i>Cotización</h6>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-lock fa-2x text-muted mb-2 d-block"></i>
                    <p class="text-muted small mb-0">
                        La cotización es visible solo para el Administrador
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ============================================
// 🔍 BUSCADOR DE CLIENTES (CORREGIDO)
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscadorCliente');
    const selectClientes = document.getElementById('clienteSelect');
    const opciones = selectClientes.querySelectorAll('option');
    
    // Guardar todas las opciones originales (excepto la primera)
    const opcionesOriginales = Array.from(opciones).filter(opt => opt.value !== '');
    
    // Evento de búsqueda
    if (buscador) {
        buscador.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase().trim();
            
            // Limpiar select pero mantener la opción por defecto
            selectClientes.innerHTML = '<option value="">-- Seleccione un cliente --</option>';
            
            if (textoBusqueda === '') {
                // Si no hay búsqueda, mostrar todos
                opcionesOriginales.forEach(opt => {
                    selectClientes.appendChild(opt.cloneNode(true));
                });
            } else {
                // Filtrar clientes
                let encontrados = 0;
                opcionesOriginales.forEach(opt => {
                    const nombre = opt.getAttribute('data-nombre') || '';
                    const telefono = opt.getAttribute('data-telefono') || '';
                    
                    // Buscar en nombre y teléfono
                    if (nombre.includes(textoBusqueda) || telefono.includes(textoBusqueda)) {
                        selectClientes.appendChild(opt.cloneNode(true));
                        encontrados++;
                    }
                });
                
                // Si no hay resultados, mostrar mensaje
                if (encontrados === 0) {
                    const optionMsg = document.createElement('option');
                    optionMsg.value = '';
                    optionMsg.textContent = '❌ No se encontraron clientes';
                    optionMsg.disabled = true;
                    selectClientes.appendChild(optionMsg);
                }
            }
            
            // Recalcular tamaño del select
            selectClientes.size = Math.min(6, selectClientes.options.length);
        });
    }
});

// ============================================
// 🔥 MAPA INTERACTIVO PARA SELECCIONAR ORIGEN Y DESTINO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Coordenadas de La Paz (centro)
    const centroLaPaz = [-16.498, -68.135];
    
    // Inicializar mapa
    const map = L.map('mapa').setView(centroLaPaz, 13);

    // Capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Variables para marcadores
    let markerOrigen = null;
    let markerDestino = null;
    let rutaLinea = null;

    // 🔥 Función para calcular distancia usando API de OSRM (gratuita)
    function calcularDistanciaAPI(origenCoords, destinoCoords) {
        const url = `https://router.project-osrm.org/route/v1/driving/${origenCoords.lng},${origenCoords.lat};${destinoCoords.lng},${destinoCoords.lat}?overview=false&geometries=geojson`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    const distanciaMetros = data.routes[0].distance;
                    const distanciaKm = distanciaMetros / 1000;
                    
                    document.getElementById('distancia').value = distanciaKm.toFixed(1);
                    
                    // Actualizar cotización
                    calcularCotizacion();
                    
                    // Mostrar mensaje
                    console.log(`✅ Distancia calculada: ${distanciaKm.toFixed(1)} km`);
                } else {
                    alert('❌ No se pudo calcular la distancia');
                }
            })
            .catch(error => {
                console.error('Error al calcular distancia:', error);
                // Si falla, usar cálculo manual simple (coordenadas)
                calcularDistanciaManual(origenCoords, destinoCoords);
            });
    }

    // 🔥 Cálculo manual de distancia (alternativo)
    function calcularDistanciaManual(origen, destino) {
        const R = 6371; // Radio de la Tierra en km
        const dLat = (destino.lat - origen.lat) * Math.PI / 180;
        const dLng = (destino.lng - origen.lng) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(origen.lat * Math.PI / 180) * Math.cos(destino.lat * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distancia = R * c;
        
        document.getElementById('distancia').value = distancia.toFixed(1);
        calcularCotizacion();
    }

    // 🔥 Evento: clic en el mapa
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        const coords = { lat: lat, lng: lng };

        // Si no hay origen, crear origen
        if (!markerOrigen) {
            markerOrigen = L.marker([lat, lng], {
                icon: L.divIcon({
                    html: '<i class="fas fa-circle" style="color:#198754;font-size:1.8rem;text-shadow:0 0 20px rgba(25,135,84,0.6);"></i>',
                    className: '',
                    iconSize: [20, 20]
                })
            }).addTo(map).bindPopup('📍 <strong>Origen</strong>');
            
            document.getElementById('origen').value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            markerOrigen.openPopup();
            
            // Geocodificar dirección
            geocodificar(lat, lng, 'origen');
            
        // Si no hay destino, crear destino
        } else if (!markerDestino) {
            markerDestino = L.marker([lat, lng], {
                icon: L.divIcon({
                    html: '<i class="fas fa-flag-checkered" style="color:#dc3545;font-size:1.8rem;text-shadow:0 0 20px rgba(220,53,69,0.6);"></i>',
                    className: '',
                    iconSize: [20, 20]
                })
            }).addTo(map).bindPopup('🏁 <strong>Destino</strong>');
            
            document.getElementById('destino').value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            markerDestino.openPopup();
            
            // Geocodificar dirección
            geocodificar(lat, lng, 'destino');
            
            // 🔥 Calcular distancia automáticamente
            const origenCoords = markerOrigen.getLatLng();
            const destinoCoords = markerDestino.getLatLng();
            
            // Dibujar línea de ruta
            if (rutaLinea) {
                map.removeLayer(rutaLinea);
            }
            rutaLinea = L.polyline([
                [origenCoords.lat, origenCoords.lng],
                [destinoCoords.lat, destinoCoords.lng]
            ], {
                color: '#0d6efd',
                weight: 4,
                opacity: 0.8,
                dashArray: '8, 8'
            }).addTo(map);
            
            // Calcular distancia
            calcularDistanciaAPI(
                { lat: origenCoords.lat, lng: origenCoords.lng },
                { lat: destinoCoords.lat, lng: destinoCoords.lng }
            );
            
        // Si ya hay origen y destino, resetear
        } else {
            if (confirm('¿Resetear puntos en el mapa?')) {
                // Limpiar todo
                if (markerOrigen) map.removeLayer(markerOrigen);
                if (markerDestino) map.removeLayer(markerDestino);
                if (rutaLinea) map.removeLayer(rutaLinea);
                markerOrigen = null;
                markerDestino = null;
                rutaLinea = null;
                document.getElementById('origen').value = '';
                document.getElementById('destino').value = '';
                document.getElementById('distancia').value = '0';
                calcularCotizacion();
            }
        }
    });

    // 🔥 Geocodificación inversa (coordenadas → dirección)
    function geocodificar(lat, lng, campo) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.display_name) {
                    document.getElementById(campo).value = data.display_name;
                }
            })
            .catch(error => console.error('Error al geocodificar:', error));
    }
});

// ============================================
// 🔥 RESTO DEL CÓDIGO (bienes, cotización, etc.)
// ============================================

let bienIndex = 1;

// 🔥 CONFIGURACIÓN DE PRECIOS
const CONFIG_PRECIOS = {
    precio_la_paz: {{ $configPrecios->precio_la_paz ?? 300 }},
    precio_el_alto: {{ $configPrecios->precio_el_alto ?? 200 }},
    precio_el_alto_la_paz: {{ $configPrecios->precio_el_alto_la_paz ?? 250 }},
    costo_ayudante: {{ $configPrecios->costo_ayudante ?? 80 }},
    costo_piso_adicional: {{ $configPrecios->costo_piso_adicional ?? 20 }},
    costo_callejon: {{ $configPrecios->costo_callejon ?? 30 }},
    costo_km_extra: {{ $configPrecios->costo_km_extra ?? 5 }}
};

function agregarBien() {
    const container = document.getElementById('bienes-container');
    const div = document.createElement('div');
    div.className = 'row g-2 bien-item mb-2';
    div.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="bienes[${bienIndex}][nombre]" class="form-control form-control-sm" placeholder="Nombre del bien" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="bienes[${bienIndex}][cantidad]" class="form-control form-control-sm" placeholder="Cantidad" value="1" min="1" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="bienes[${bienIndex}][descripcion]" class="form-control form-control-sm" placeholder="Descripción (opcional)">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarBien(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    bienIndex++;
    calcularCotizacion();
}

function eliminarBien(btn) {
    const item = btn.closest('.bien-item');
    if (document.querySelectorAll('.bien-item').length > 1) {
        item.remove();
        calcularCotizacion();
    } else {
        alert('Debe tener al menos un bien en la lista');
    }
}

function determinarZona(origen, destino) {
    const origenLimpio = origen.toLowerCase().trim();
    const destinoLimpio = destino.toLowerCase().trim();

    if (origenLimpio.includes('la paz') && destinoLimpio.includes('la paz')) {
        return 'la_paz';
    }
    if (origenLimpio.includes('el alto') && destinoLimpio.includes('el alto')) {
        return 'el_alto';
    }
    if ((origenLimpio.includes('el alto') && destinoLimpio.includes('la paz')) ||
        (origenLimpio.includes('la paz') && destinoLimpio.includes('el alto'))) {
        return 'el_alto_a_la_paz';
    }
    return 'la_paz';
}

function calcularCotizacion() {
    const origen = document.getElementById('origen').value || '';
    const destino = document.getElementById('destino').value || '';
    const ayudantes = parseInt(document.getElementById('ayudantes').value) || 0;
    const pisos = parseInt(document.getElementById('pisos').value) || 1;
    const callejon = document.getElementById('callejon').value === '1';
    const distancia = parseFloat(document.getElementById('distancia').value) || 0;

    const zona = determinarZona(origen, destino);
    
    const tarifas = {
        'la_paz': CONFIG_PRECIOS.precio_la_paz,
        'el_alto': CONFIG_PRECIOS.precio_el_alto,
        'el_alto_a_la_paz': CONFIG_PRECIOS.precio_el_alto_la_paz
    };
    
    const costoBase = tarifas[zona] || CONFIG_PRECIOS.precio_la_paz;
    const costoAyudante = ayudantes * CONFIG_PRECIOS.costo_ayudante;
    const costoPisos = Math.max(0, (pisos - 1) * CONFIG_PRECIOS.costo_piso_adicional);
    const costoCallejon = callejon ? CONFIG_PRECIOS.costo_callejon : 0;
    
    let costoKmExtra = 0;
    if (distancia > 10) {
        const kmExtra = distancia - 10;
        costoKmExtra = kmExtra * CONFIG_PRECIOS.costo_km_extra;
    }
    
    const total = costoBase + costoAyudante + costoPisos + costoCallejon + costoKmExtra;

    const zonaLabel = zona.replace('_', ' → ').toUpperCase();

    const preview = document.getElementById('cotizacion-preview');
    let html = `
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Zona (${zonaLabel})</span>
            <strong>${costoBase.toFixed(2)} Bs</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Ayudantes (${ayudantes} x ${CONFIG_PRECIOS.costo_ayudante} Bs)</span>
            <strong>${costoAyudante.toFixed(2)} Bs</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Pisos adicionales (${Math.max(0, pisos - 1)} x ${CONFIG_PRECIOS.costo_piso_adicional} Bs)</span>
            <strong>${costoPisos.toFixed(2)} Bs</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Callejón</span>
            <strong>${costoCallejon.toFixed(2)} Bs</strong>
        </div>
    `;
    
    if (distancia > 10) {
        const kmExtra = distancia - 10;
        html += `
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Km extra (${kmExtra.toFixed(1)} x ${CONFIG_PRECIOS.costo_km_extra} Bs)</span>
                <strong>${costoKmExtra.toFixed(2)} Bs</strong>
            </div>
        `;
    }
    
    html += `
        <hr>
        <div class="d-flex justify-content-between">
            <strong>TOTAL</strong>
            <strong class="text-primary h5">${total.toFixed(2)} Bs</strong>
        </div>
        <div class="mt-2 small text-muted">
            <i class="fas fa-info-circle me-1"></i> Cotización calculada automáticamente
            ${distancia > 0 ? ` | 📏 ${distancia.toFixed(1)} km` : ''}
            ${distancia > 10 ? ' | 🚗 Incluye km extra' : ''}
        </div>
    `;
    
    preview.innerHTML = html;
}

// Event listeners - SOLO SI EL USUARIO ES ADMIN
@if(auth()->user()->isAdmin())
document.getElementById('origen').addEventListener('input', calcularCotizacion);
document.getElementById('destino').addEventListener('input', calcularCotizacion);
document.getElementById('ayudantes').addEventListener('input', calcularCotizacion);
document.getElementById('pisos').addEventListener('input', calcularCotizacion);
document.getElementById('callejon').addEventListener('change', calcularCotizacion);
document.getElementById('distancia').addEventListener('input', calcularCotizacion);

setTimeout(calcularCotizacion, 100);
@endif
</script>
@endpush
@endsection