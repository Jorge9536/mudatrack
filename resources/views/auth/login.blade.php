<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MudaTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('storage/logo.png') }}">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #b87333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 70px rgba(0,0,0,0.35);
        }
        .login-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card .logo img {
            max-width: 180px;
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .login-card .logo h2 {
            font-weight: 800;
            color: #1e3a5f;
            margin: 5px 0 0;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
        }
        .login-card .logo p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 5px 0 0;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.9rem;
        }
        .input-group-text {
            background: #f1f5f9;
            border: none;
            color: #64748b;
        }
        .form-control {
            border-radius: 0 10px 10px 0;
            padding: 12px 15px;
            border: none;
            background: #f1f5f9;
        }
        .form-control:focus {
            background: #e2e8f0;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.15);
        }
        .btn-login {
            border-radius: 10px;
            padding: 13px;
            font-weight: 700;
            background: linear-gradient(135deg, #1e3a5f, #b87333);
            border: none;
            transition: all 0.3s;
            font-size: 1rem;
            letter-spacing: 0.3px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(30, 58, 95, 0.35);
            filter: brightness(1.05);
        }
        .error-message {
            background: #fee2e2;
            border-left: 4px solid #dc3545;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.9rem;
            color: #991b1b;
        }
        .form-check-input:checked {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
        }
        .divider-text {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <img src="{{ asset('storage/logo.png') }}" alt="MudaTrack Logo">
            <h2>MudaTrack</h2>
            <p>Sistema de Gestión Logística</p>
        </div>

        @if ($errors->any())
            <div class="error-message mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}" placeholder="tu@email.com" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label small" for="remember">Recordarme</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100">
                <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
            </button>

            <hr class="my-4">
            <div class="text-center">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Sistema seguro · Todos los derechos reservados
                </small>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>