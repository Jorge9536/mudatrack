<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MudaTrack - @yield('title', 'Sistema de Gestión')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- 🔥 LEAFLET PARA MAPAS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    
    <style>
        /* ============================================ */
        /* ESTILOS BASE (PC y MÓVIL) */
        /* ============================================ */
        * {
            box-sizing: border-box;
        }
        
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            width: 100%;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important;
        }
        
        /* ============================================ */
        /* SIDEBAR - ESTILOS BASE */
        /* ============================================ */
        .sidebar {
            min-height: 100vh;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 20px 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link {
            color: #6c757d;
            padding: 10px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .sidebar .nav-link:hover {
            background: #f0f7ff;
            color: #0d6efd;
        }
        
        .sidebar .nav-link.active {
            background: #0d6efd;
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
        }
        
        /* Badges de estado */
        .badge-pendiente { background-color: #6c757d; }
        .badge-confirmado { background-color: #0d6efd; }
        .badge-en_progreso { background-color: #ffc107; color: #000; }
        .badge-finalizado { background-color: #198754; }
        .badge-cancelado { background-color: #dc3545; }
        .badge-pendiente_pago { background-color: #dc3545; }
        .badge-pagado { background-color: #198754; }
        
        .role-badge {
            font-size: 0.65rem;
            padding: 3px 10px;
            border-radius: 20px;
            margin-left: 5px;
        }
        .role-badge.admin { background: #dc3545; color: white; }
        .role-badge.recepcionista { background: #0d6efd; color: white; }
        .role-badge.chofer { background: #ffc107; color: #000; }
        
        /* Mapa */
        #mapa {
            height: 350px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            z-index: 1;
            width: 100%;
        }
        
        .leaflet-routing-container {
            display: none !important;
        }
        
        /* Scroll personalizado */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c7cd;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8aeb4;
        }
        
        /* ============================================ */
        /* 📱 ESTILOS ESPECÍFICOS PARA MÓVIL */
        /* ============================================ */
        @media (max-width: 768px) {
            /* Ajustes generales */
            body {
                font-size: 14px;
            }
            
            /* Navbar más compacto */
            .navbar {
                padding: 8px 10px !important;
            }
            
            .navbar-brand {
                font-size: 1.1rem !important;
            }
            
            /* Sidebar se convierte en barra superior */
            .sidebar {
                min-height: auto !important;
                border-right: none !important;
                border-bottom: 1px solid #e9ecef;
                padding: 5px 10px !important;
                background: white;
                width: 100% !important;
                position: sticky !important;
                top: 0 !important;
                z-index: 999 !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            }
            
            /* Botón toggle del menú móvil */
            .sidebar-toggle-mobile {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                padding: 8px 5px;
                background: transparent;
                border: none;
                color: #0d6efd;
                font-weight: 600;
                cursor: pointer;
            }
            
            .sidebar-toggle-mobile i {
                font-size: 1.2rem;
            }
            
            /* Menú colapsado en móvil */
            .sidebar-menu-mobile {
                display: none;
                padding: 10px 0;
                background: white;
            }
            
            .sidebar-menu-mobile.show {
                display: block;
            }
            
            .sidebar .nav-link {
                padding: 10px 12px !important;
                margin: 2px 0 !important;
                font-size: 13px !important;
                white-space: normal !important;
                border-radius: 6px;
            }
            
            .sidebar .nav-link i {
                width: 20px;
                font-size: 0.9rem;
            }
            
            /* Contenido principal ocupa todo el ancho */
            #contenidoPrincipal {
                padding: 10px 12px !important;
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
            
            /* Tarjetas más compactas */
            .card {
                margin-bottom: 12px !important;
                border-radius: 10px !important;
            }
            
            .card-header {
                padding: 12px 15px !important;
                font-size: 0.95rem !important;
            }
            
            .card-body {
                padding: 12px 15px !important;
            }
            
            /* Tablas responsivas */
            .table-responsive {
                font-size: 12px !important;
            }
            
            .table-responsive table {
                min-width: 600px;
            }
            
            /* Botones más pequeños en móvil */
            .btn {
                font-size: 12px !important;
                padding: 5px 10px !important;
                border-radius: 6px !important;
            }
            
            .btn i {
                font-size: 0.85rem !important;
            }
            
            /* Formularios en móvil */
            .form-control, .form-select {
                font-size: 14px !important;
                padding: 8px 10px !important;
            }
            
            .form-label {
                font-size: 13px !important;
                margin-bottom: 4px !important;
            }
            
            /* Mapa más pequeño */
            #mapa {
                height: 250px !important;
            }
            
            /* Badges más pequeños */
            .badge {
                font-size: 0.7rem !important;
                padding: 4px 8px !important;
            }
            
            /* Modales en móvil */
            .modal-dialog {
                margin: 10px !important;
            }
            
            .modal-content {
                border-radius: 12px !important;
            }
            
            /* Alertas */
            .alert {
                padding: 10px 12px !important;
                font-size: 13px !important;
                margin-bottom: 10px !important;
            }
            
            /* Ocultar elementos innecesarios en móvil */
            .hide-mobile {
                display: none !important;
            }
            
            /* Mostrar elementos específicos para móvil */
            .show-mobile {
                display: block !important;
            }
        }
        
        /* ============================================ */
        /* 📱 ESTILOS PARA MÓVILES MUY PEQUEÑOS */
        /* ============================================ */
        @media (max-width: 576px) {
            #contenidoPrincipal {
                padding: 8px 8px !important;
            }
            
            .navbar-brand {
                font-size: 1rem !important;
            }
            
            .sidebar .nav-link {
                font-size: 12px !important;
                padding: 8px 10px !important;
            }
            
            .card-header {
                padding: 10px 12px !important;
                font-size: 0.85rem !important;
            }
            
            .card-body {
                padding: 10px 12px !important;
            }
            
            .btn {
                font-size: 11px !important;
                padding: 4px 8px !important;
            }
            
            .form-control, .form-select {
                font-size: 13px !important;
                padding: 6px 8px !important;
            }
            
            #mapa {
                height: 200px !important;
            }
            
            .modal-dialog {
                margin: 5px !important;
            }
            
            /* Grid en móvil: 1 columna */
            .row-cols-1-mobile > * {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
        }
        
        /* ============================================ */
        /* 📱 ESTILOS PARA TABLETS */
        /* ============================================ */
        @media (min-width: 769px) and (max-width: 1024px) {
            .sidebar .nav-link {
                font-size: 13px !important;
                padding: 8px 15px !important;
            }
            
            #contenidoPrincipal {
                padding: 15px !important;
            }
            
            .card-header {
                padding: 12px 18px !important;
            }
        }
        
        /* ============================================ */
        /* CLASES UTILITARIAS PARA MÓVIL */
        /* ============================================ */
        .show-mobile {
            display: none !important;
        }
        
        .hide-mobile {
            display: block !important;
        }
        
        @media (max-width: 768px) {
            .show-mobile {
                display: block !important;
            }
            .hide-mobile {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- ============================================ -->
    <!-- NAVBAR -->
    <!-- ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-truck me-2"></i>MudaTrack
            </a>
            
            <!-- Botón para abrir menú en móvil -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="fas fa-user me-1"></i> {{ auth()->user()->name }}
                                @if(auth()->user()->isAdmin())
                                    <span class="role-badge admin">Admin</span>
                                @elseif(auth()->user()->isRecepcionista())
                                    <span class="role-badge recepcionista">Recepcionista</span>
                                @elseif(auth()->user()->isChofer())
                                    <span class="role-badge chofer">Chofer</span>
                                @endif
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Salir
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============================================ -->
    <!-- CONTENIDO PRINCIPAL CON SIDEBAR MEJORADO -->
    <!-- ============================================ -->
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR CON DETECCIÓN MÓVIL -->
            <div class="col-md-2 sidebar" id="sidebarPrincipal">
                <!-- Botón toggle para móvil -->
                <button class="sidebar-toggle-mobile d-md-none" type="button" id="btnToggleMenu">
                    <span><i class="fas fa-bars me-2"></i> Menú</span>
                    <i class="fas fa-chevron-down" id="iconToggle"></i>
                </button>
                
                <!-- Menú (visible en PC, colapsado en móvil) -->
                <div class="sidebar-menu-mobile d-md-block" id="menuMobile">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-chart-pie"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" 
                               href="{{ route('clientes.index') }}">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}" 
                               href="{{ route('servicios.index') }}">
                                <i class="fas fa-tasks"></i> Servicios
                            </a>
                        </li>

                        {{-- SOLO ADMIN --}}
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('choferes.*') ? 'active' : '' }}" 
                               href="{{ route('choferes.index') }}">
                                <i class="fas fa-user-circle"></i> Choferes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ayudantes.*') ? 'active' : '' }}" 
                               href="{{ route('ayudantes.index') }}">
                                <i class="fas fa-user-friends"></i> Ayudantes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}" 
                               href="{{ route('vehiculos.index') }}">
                                <i class="fas fa-truck"></i> Vehículos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                               href="{{ route('users.index') }}">
                                <i class="fas fa-users-cog"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}" 
                               href="{{ route('configuracion.precios') }}">
                                <i class="fas fa-cog"></i> Configuración
                            </a>
                        </li>
                        @endif

                        {{-- ADMIN Y RECEPCIONISTA --}}
                        @if(auth()->user()->hasAnyRole(['admin', 'recepcionista']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gps.*') ? 'active' : '' }}" 
                               href="{{ route('gps.index') }}">
                                <i class="fas fa-map-marked-alt"></i> Seguimiento GPS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" 
                               href="{{ route('reportes.index') }}">
                                <i class="fas fa-file-alt"></i> Reportes
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="col-md-10 col-12" id="contenidoPrincipal">
                <!-- Mensajes de éxito/error -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Contenido de la vista -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SCRIPTS -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    
    <!-- JavaScript para toggle del menú en móvil -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnToggle = document.getElementById('btnToggleMenu');
            const menuMobile = document.getElementById('menuMobile');
            const iconToggle = document.getElementById('iconToggle');
            
            if (btnToggle) {
                btnToggle.addEventListener('click', function() {
                    menuMobile.classList.toggle('show');
                    iconToggle.classList.toggle('fa-chevron-down');
                    iconToggle.classList.toggle('fa-chevron-up');
                });
            }
            
            // Cerrar menú al hacer clic en un enlace (móvil)
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        menuMobile.classList.remove('show');
                        iconToggle.classList.remove('fa-chevron-up');
                        iconToggle.classList.add('fa-chevron-down');
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>