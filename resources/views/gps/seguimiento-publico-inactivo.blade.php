<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicio Finalizado - MudaTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container" style="max-width: 500px; margin-top: 80px;">
        <div class="card shadow-sm text-center">
            <div class="card-body p-5">
                <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                <h2 class="mt-4">¡Servicio finalizado!</h2>
                <p class="text-muted">
                    El seguimiento GPS de este servicio ha finalizado.
                </p>
                <hr>
                <div class="text-start">
                    <p><strong>Cliente:</strong> {{ $servicio->cliente->nombre_completo }}</p>
                    <p><strong>Servicio:</strong> #{{ $servicio->id }}</p>
                    <p><strong>Fecha:</strong> {{ $servicio->fecha_servicio->format('d/m/Y') }}</p>
                    <p><strong>Origen:</strong> {{ $servicio->origen }}</p>
                    <p><strong>Destino:</strong> {{ $servicio->destino }}</p>
                </div>
                <p class="text-muted small mt-4 mb-0">
                    Gracias por confiar en <strong>MudaTrack</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>