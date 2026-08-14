<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MudaTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card .logo i {
            font-size: 3rem;
            color: #0d6efd;
        }
        .login-card .logo h2 {
            font-weight: 700;
            color: #0d6efd;
            margin: 0;
        }
        .login-card .logo p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }
        .btn-login {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            background: #0d6efd;
            border: none;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
        }
        .error-message {
            background: #f8d7da;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.9rem;
            color: #842029;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <i class="fas fa-truck"></i>
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
                <label class="form-label fw-semibold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                    <input type="email" name="email" class="form-control border-0 bg-light" 
                           value="{{ old('email') }}" placeholder="admin@mudatrack.com" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-0 bg-light" 
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

            <div class="text-center mt-3">
            </div>
            <hr class="my-3">
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