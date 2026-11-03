<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Servicio - MudaTrack</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            padding: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1e3a5f;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header .logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #1e3a5f;
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }
        .header p {
            color: #6c757d;
            margin: 5px 0;
            font-size: 13px;
        }
        .header .subtitle {
            font-size: 14px;
            color: #1e3a5f;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #1e3a5f;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 12px;
        }
        .label {
            color: #6c757d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 12px;
        }
        table th {
            background: #f8f9fa;
            padding: 8px;
            text-align: left;
            border: 1px solid #dee2e6;
            color: #1e3a5f;
        }
        table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            padding: 15px 0;
            border-top: 2px solid #1e3a5f;
            margin-top: 10px;
            color: #1e3a5f;
        }
        .total .monto {
            font-size: 22px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background: #198754;
            color: white;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .estado-servicio {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            background: #e2e8f0;
            color: #1e3a5f;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('storage/logo.png') }}" alt="MudaTrack" class="logo">

        <h1>MudaTrack</h1>
        <p>Transporte · Mudanza y Traslado</p>
        <p class="subtitle">COMPROBANTE DE SERVICIO</p>
        <p style="font-size: 11px; margin-top: 5px;">
            N° {{ str_pad($servicio->id, 6, '0', STR_PAD_LEFT) }} |
            Fecha: {{ $servicio->fecha_servicio->format('d/m/Y') }}
        </p>
    </div>
    <div class="section">
        <div class="section-title">Datos del Cliente</div>
        <div class="row">
            <span class="label">Nombre:</span>
            <span><strong>{{ $servicio->cliente->nombre_completo }}</strong></span>
        </div>
        <div class="row">
            <span class="label">Teléfono:</span>
            <span>{{ $servicio->cliente->telefono }}</span>
        </div>
    </div>
    <div class="section">
        <div class="section-title">Datos del Servicio</div>
        <div class="row">
            <span class="label">Origen:</span>
            <span>{{ $servicio->origen }}</span>
        </div>
        <div class="row">
            <span class="label">Destino:</span>
            <span>{{ $servicio->destino }}</span>
        </div>
        <div class="row">
            <span class="label">Distancia:</span>
            <span>{{ number_format($servicio->distancia_km ?? 0, 1) }} km</span>
        </div>
        <div class="row">
            <span class="label">Vehículo:</span>
            <span>{{ $servicio->vehiculo ? $servicio->vehiculo->placa . ' (' . $servicio->vehiculo->tipo . ')' : 'No asignado' }}</span>
        </div>
        <div class="row">
            <span class="label">Chofer:</span>
            <span>{{ $servicio->chofer ? $servicio->chofer->nombre_completo : 'No asignado' }}</span>
        </div>
        <div class="row">
            <span class="label">Ayudantes:</span>
            <span>{{ $servicio->cantidad_ayudantes }}</span>
        </div>
        <div class="row">
            <span class="label">Estado del Servicio:</span>
            <span class="estado-servicio">{{ $servicio->estado_label }}</span>
        </div>
        <div class="row">
            <span class="label">Estado de Pago:</span>
            <span>
                @if($servicio->estado_pago === 'pagado')
                    <span class="badge badge-success">✓ PAGADO</span>
                @else
                    <span class="badge badge-danger">PENDIENTE</span>
                @endif
            </span>
        </div>

        @if($servicio->metodo_pago && $servicio->estado_pago === 'pagado')
        <div class="row">
            <span class="label">Método de Pago:</span>
            <span>{{ $servicio->metodo_pago_label }}</span>
        </div>
        @endif
    </div>
    @if($servicio->bienes->count() > 0)
    <div class="section">
        <div class="section-title">Bienes Transportados</div>
        <table>
            <thead>
                <tr>
                    <th style="width:70%;">Descripción</th>
                    <th style="width:30%;text-align:center;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicio->bienes as $bien)
                <tr>
                    <td>
                        {{ $bien->nombre }}
                        @if($bien->descripcion)
                            <br><small style="color:#6c757d;">{{ $bien->descripcion }}</small>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $bien->cantidad }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="total">
        TOTAL: <span class="monto">{{ number_format($servicio->costo_total, 2) }} Bs</span>
    </div>
    <div class="footer">
        <p>Este comprobante tiene validez como constancia de servicio prestado por MudaTrack.</p>
        <p>Generado: {{ now()->format('d/m/Y H:i') }} · Gracias por confiar en nosotros</p>
    </div>
</body>
</html>