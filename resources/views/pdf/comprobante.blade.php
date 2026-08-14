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
            border-bottom: 3px double #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6c757d;
            margin: 5px 0;
        }
        .header .subtitle {
            font-size: 14px;
            color: #0d6efd;
            font-weight: bold;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #0d6efd;
            font-size: 14px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 13px;
        }
        .label {
            color: #6c757d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 13px;
        }
        table th {
            background: #f8f9fa;
            padding: 8px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        .total {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            padding: 15px 0;
            border-top: 2px solid #0d6efd;
            margin-top: 10px;
            color: #0d6efd;
        }
        .total .monto {
            font-size: 24px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        .status-pagado {
            color: #198754;
            font-weight: bold;
        }
        .status-pendiente {
            color: #dc3545;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background: #198754;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚚 MudaTrack</h1>
        <p>Transporte Libre · Mudanza y Traslado</p>
        <p class="subtitle">COMPROBANTE DE SERVICIO</p>
        <p style="font-size: 12px; margin-top: 5px;">
            N° {{ $servicio->id }} | 
            Fecha: {{ $servicio->fecha_servicio->format('d/m/Y') }}
        </p>
    </div>

    <!-- DATOS DEL CLIENTE -->
    <div class="section">
        <div class="section-title">📋 DATOS DEL CLIENTE</div>
        <div class="row">
            <span class="label">Nombre:</span>
            <span><strong>{{ $servicio->cliente->nombre_completo }}</strong></span>
        </div>
        <div class="row">
            <span class="label">Teléfono:</span>
            <span>{{ $servicio->cliente->telefono }}</span>
        </div>
    </div>

    <!-- DATOS DEL SERVICIO -->
    <div class="section">
        <div class="section-title">📍 DATOS DEL SERVICIO</div>
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
            <span class="label">Vehiculo:</span>
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
            <span class="label">Estado:</span>
            <span>
                @if($servicio->estado === 'pagado')
                    <span class="badge badge-success">✅ Pagado</span>
                @else
                    <span class="badge badge-warning">⏳ Pendiente</span>
                @endif
            </span>
        </div>
    </div>

    <!-- LISTA DE BIENES -->
    <div class="section">
        <div class="section-title">📦 BIENES TRANSPORTADOS</div>
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
                    <td>{{ $bien->nombre }}</td>
                    <td style="text-align:center;">{{ $bien->cantidad }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- TOTAL -->
    <div class="total">
        TOTAL: <span class="monto">{{ number_format($servicio->costo_total, 2) }} Bs</span>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Este comprobante tiene validez como constancia de servicio prestado por MudaTrack.</p>
        <p>Generado: {{ now()->format('d/m/Y H:i') }} | Gracias por confiar en nosotros 🙌</p>
    </div>
</body>
</html>