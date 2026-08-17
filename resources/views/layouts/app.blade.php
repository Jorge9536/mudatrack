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
            transition: background-color 0.3s ease, color 0.3s ease;
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
        
        /* Estilo especial para el enlace de 2FA */
        .sidebar .nav-link.twofa-active {
            color: #198754;
        }
        
        .sidebar .nav-link.twofa-active i {
            color: #198754;
        }
        
        .sidebar .nav-link.twofa-inactive {
            color: #ffc107;
        }
        
        .sidebar .nav-link.twofa-inactive i {
            color: #ffc107;
        }
        
        /* Estilo para Mapa de Vehículos */
        .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-left: 3px solid #0d6efd;
        }
        
        .sidebar .nav-link.vehicle-map:hover {
            background: linear-gradient(135deg, #bbdefb, #90caf9);
        }
        
        .sidebar .nav-link.vehicle-map.active {
            background: #0d6efd;
            color: white;
            border-left-color: white;
        }
        
        .sidebar .nav-link.vehicle-map i.fa-truck {
            color: #0d6efd;
        }
        
        .sidebar .nav-link.vehicle-map.active i.fa-truck {
            color: white;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: background-color 0.3s ease, border-color 0.3s ease;
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

        /* ============================================ */
        /* BOTÓN DE CAMBIO DE TEMA (THEME TOGGLE) */
        /* ============================================ */
        .theme-toggle-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: #0d6efd;
            color: white;
            font-size: 1.4rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .theme-picker-dropdown {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 1050;
            background: white;
            border-radius: 12px;
            padding: 12px 0;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            display: none;
            min-width: 200px;
            overflow: hidden;
        }

        .theme-picker-dropdown.show {
            display: block;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .theme-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.2s ease;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            font-size: 0.9rem;
            color: #333;
        }

        .theme-option:hover {
            background: #f0f7ff;
        }

        .theme-option .color-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #e9ecef;
            flex-shrink: 0;
        }

        .theme-option .theme-name {
            font-weight: 500;
        }

        /* ============================================ */
        /* TEMAS DE COLOR (THEMES) */
        /* ============================================ */

        /* --- TEMA OSCURO (Dark) --- */
        body.theme-dark {
            background: #1a1a2e;
            color: #e0e0e0;
        }

        body.theme-dark .navbar,
        body.theme-dark .sidebar,
        body.theme-dark .card,
        body.theme-dark .modal-content,
        body.theme-dark .theme-picker-dropdown,
        body.theme-dark .dropdown-menu {
            background: #16213e !important;
            border-color: #2a3a5e !important;
            color: #e0e0e0 !important;
        }

        body.theme-dark .navbar-brand {
            color: #4fc3f7 !important;
        }

        body.theme-dark .sidebar .nav-link {
            color: #b0bec5 !important;
        }

        body.theme-dark .sidebar .nav-link:hover {
            background: #1a2a4a !important;
            color: #4fc3f7 !important;
        }

        body.theme-dark .sidebar .nav-link.active {
            background: #0d47a1 !important;
            color: white !important;
        }

        body.theme-dark .card-header {
            border-bottom-color: #2a3a5e !important;
        }

        body.theme-dark .form-control,
        body.theme-dark .form-select {
            background: #1a2a4a !important;
            border-color: #2a3a5e !important;
            color: #e0e0e0 !important;
        }

        body.theme-dark .form-control:focus,
        body.theme-dark .form-select:focus {
            background: #1a2a4a !important;
            border-color: #4fc3f7 !important;
            box-shadow: 0 0 0 0.25rem rgba(79, 195, 247, 0.25);
        }

        body.theme-dark .table {
            color: #e0e0e0 !important;
        }

        body.theme-dark .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(255,255,255,0.03);
        }

        body.theme-dark .text-muted {
            color: #90a4ae !important;
        }

        body.theme-dark .alert-success {
            background: #1b5e20;
            border-color: #2e7d32;
            color: #a5d6a7;
        }

        body.theme-dark .alert-danger {
            background: #b71c1c;
            border-color: #c62828;
            color: #ef9a9a;
        }

        body.theme-dark .alert-info {
            background: #0d47a1;
            border-color: #1565c0;
            color: #90caf9;
        }

        body.theme-dark .btn-close {
            filter: invert(1);
        }

        body.theme-dark .theme-toggle-btn {
            background: #0d47a1;
        }

        body.theme-dark .dropdown-menu .dropdown-item {
            color: #e0e0e0 !important;
        }

        body.theme-dark .dropdown-menu .dropdown-item:hover {
            background: #1a2a4a !important;
        }

        body.theme-dark .role-badge.admin {
            background: #c62828;
        }

        body.theme-dark .role-badge.recepcionista {
            background: #0d47a1;
        }

        body.theme-dark .role-badge.chofer {
            background: #f9a825;
            color: #000;
        }

        body.theme-dark .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, #0d47a1, #1a237e);
            border-left-color: #4fc3f7;
        }

        body.theme-dark .sidebar .nav-link.vehicle-map i.fa-truck {
            color: #4fc3f7;
        }

        body.theme-dark .sidebar .nav-link.vehicle-map.active {
            background: #0d47a1;
            color: white;
        }

        body.theme-dark .sidebar .nav-link.vehicle-map.active i.fa-truck {
            color: white;
        }

        body.theme-dark #mapa {
            border-color: #2a3a5e;
        }

        /* --- TEMA AZUL (Blue) --- */
        body.theme-blue {
            background: #e3f2fd;
        }

        body.theme-blue .navbar,
        body.theme-blue .sidebar,
        body.theme-blue .card,
        body.theme-blue .modal-content,
        body.theme-blue .theme-picker-dropdown,
        body.theme-blue .dropdown-menu {
            background: #ffffff !important;
            border-color: #90caf9 !important;
        }

        body.theme-blue .navbar-brand {
            color: #0d47a1 !important;
        }

        body.theme-blue .sidebar .nav-link {
            color: #0d47a1 !important;
        }

        body.theme-blue .sidebar .nav-link:hover {
            background: #bbdefb !important;
        }

        body.theme-blue .sidebar .nav-link.active {
            background: #0d47a1 !important;
            color: white !important;
        }

        body.theme-blue .card-header {
            border-bottom-color: #90caf9 !important;
        }

        body.theme-blue .form-control:focus,
        body.theme-blue .form-select:focus {
            border-color: #0d47a1 !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 71, 161, 0.25);
        }

        body.theme-blue .theme-toggle-btn {
            background: #0d47a1;
        }

        body.theme-blue .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-left-color: #0d47a1;
        }

        /* --- TEMA VERDE (Green) --- */
        body.theme-green {
            background: #e8f5e9;
        }

        body.theme-green .navbar,
        body.theme-green .sidebar,
        body.theme-green .card,
        body.theme-green .modal-content,
        body.theme-green .theme-picker-dropdown,
        body.theme-green .dropdown-menu {
            background: #ffffff !important;
            border-color: #a5d6a7 !important;
        }

        body.theme-green .navbar-brand {
            color: #1b5e20 !important;
        }

        body.theme-green .sidebar .nav-link {
            color: #1b5e20 !important;
        }

        body.theme-green .sidebar .nav-link:hover {
            background: #c8e6c9 !important;
        }

        body.theme-green .sidebar .nav-link.active {
            background: #1b5e20 !important;
            color: white !important;
        }

        body.theme-green .card-header {
            border-bottom-color: #a5d6a7 !important;
        }

        body.theme-green .form-control:focus,
        body.theme-green .form-select:focus {
            border-color: #1b5e20 !important;
            box-shadow: 0 0 0 0.25rem rgba(27, 94, 32, 0.25);
        }

        body.theme-green .theme-toggle-btn {
            background: #1b5e20;
        }

        body.theme-green .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-left-color: #1b5e20;
        }

        /* --- TEMA MORADO (Purple) --- */
        body.theme-purple {
            background: #f3e5f5;
        }

        body.theme-purple .navbar,
        body.theme-purple .sidebar,
        body.theme-purple .card,
        body.theme-purple .modal-content,
        body.theme-purple .theme-picker-dropdown,
        body.theme-purple .dropdown-menu {
            background: #ffffff !important;
            border-color: #ce93d8 !important;
        }

        body.theme-purple .navbar-brand {
            color: #4a148c !important;
        }

        body.theme-purple .sidebar .nav-link {
            color: #4a148c !important;
        }

        body.theme-purple .sidebar .nav-link:hover {
            background: #e1bee7 !important;
        }

        body.theme-purple .sidebar .nav-link.active {
            background: #4a148c !important;
            color: white !important;
        }

        body.theme-purple .card-header {
            border-bottom-color: #ce93d8 !important;
        }

        body.theme-purple .form-control:focus,
        body.theme-purple .form-select:focus {
            border-color: #4a148c !important;
            box-shadow: 0 0 0 0.25rem rgba(74, 20, 140, 0.25);
        }

        body.theme-purple .theme-toggle-btn {
            background: #4a148c;
        }

        body.theme-purple .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, #f3e5f5, #e1bee7);
            border-left-color: #4a148c;
        }

        /* --- TEMA ROJO (Red) --- */
        body.theme-red {
            background: #ffebee;
        }

        body.theme-red .navbar,
        body.theme-red .sidebar,
        body.theme-red .card,
        body.theme-red .modal-content,
        body.theme-red .theme-picker-dropdown,
        body.theme-red .dropdown-menu {
            background: #ffffff !important;
            border-color: #ef9a9a !important;
        }

        body.theme-red .navbar-brand {
            color: #b71c1c !important;
        }

        body.theme-red .sidebar .nav-link {
            color: #b71c1c !important;
        }

        body.theme-red .sidebar .nav-link:hover {
            background: #ffcdd2 !important;
        }

        body.theme-red .sidebar .nav-link.active {
            background: #b71c1c !important;
            color: white !important;
        }

        body.theme-red .card-header {
            border-bottom-color: #ef9a9a !important;
        }

        body.theme-red .form-control:focus,
        body.theme-red .form-select:focus {
            border-color: #b71c1c !important;
            box-shadow: 0 0 0 0.25rem rgba(183, 28, 28, 0.25);
        }

        body.theme-red .theme-toggle-btn {
            background: #b71c1c;
        }

        body.theme-red .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            border-left-color: #b71c1c;
        }

        /* --- TEMA AMARILLO (Yellow) --- */
        body.theme-yellow {
            background: #fffde7;
        }

        body.theme-yellow .navbar,
        body.theme-yellow .sidebar,
        body.theme-yellow .card,
        body.theme-yellow .modal-content,
        body.theme-yellow .theme-picker-dropdown,
        body.theme-yellow .dropdown-menu {
            background: #ffffff !important;
            border-color: #ffe082 !important;
        }

        body.theme-yellow .navbar-brand {
            color: #f57f17 !important;
        }

        body.theme-yellow .sidebar .nav-link {
            color: #f57f17 !important;
        }

        body.theme-yellow .sidebar .nav-link:hover {
            background: #ffecb3 !important;
        }

        body.theme-yellow .sidebar .nav-link.active {
            background: #f57f17 !important;
            color: white !important;
        }

        body.theme-yellow .card-header {
            border-bottom-color: #ffe082 !important;
        }

        body.theme-yellow .form-control:focus,
        body.theme-yellow .form-select:focus {
            border-color: #f57f17 !important;
            box-shadow: 0 0 0 0.25rem rgba(245, 127, 23, 0.25);
        }

        body.theme-yellow .theme-toggle-btn {
            background: #f57f17;
        }

        body.theme-yellow .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, #fffde7, #ffecb3);
            border-left-color: #f57f17;
        }

        /* Ajustes para móvil en temas */
        @media (max-width: 768px) {
            .theme-toggle-btn {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
                bottom: 15px;
                right: 15px;
            }

            .theme-picker-dropdown {
                bottom: 70px;
                right: 15px;
                min-width: 170px;
            }

            .theme-option {
                padding: 8px 16px;
                font-size: 0.8rem;
            }

            .theme-option .color-circle {
                width: 20px;
                height: 20px;
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i> {{ auth()->user()->name }}
                                @if(auth()->user()->isAdmin())
                                    <span class="role-badge admin">Admin</span>
                                @elseif(auth()->user()->isRecepcionista())
                                    <span class="role-badge recepcionista">Recepcionista</span>
                                @elseif(auth()->user()->isChofer())
                                    <span class="role-badge chofer">Chofer</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('2fa.setup') }}">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        @if(auth()->user()->google2fa_enabled)
                                            <span class="text-success">🔒 2FA Activado</span>
                                        @else
                                            <span class="text-warning">⚠️ Activar 2FA</span>
                                        @endif
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============================================ -->
    <!-- CONTENIDO PRINCIPAL CON SIDEBAR -->
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
                        <!-- DASHBOARD -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-chart-pie"></i> Dashboard
                            </a>
                        </li>

                        <!-- CLIENTES -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" 
                               href="{{ route('clientes.index') }}">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>

                        <!-- SERVICIOS -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}" 
                               href="{{ route('servicios.index') }}">
                                <i class="fas fa-tasks"></i> Servicios
                            </a>
                        </li>

                        <!-- 2FA -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('2fa.*') ? 'active' : '' }} 
                                       @if(auth()->user()->google2fa_enabled) twofa-active @else twofa-inactive @endif" 
                               href="{{ route('2fa.setup') }}">
                                <i class="fas fa-shield-alt"></i>
                                @if(auth()->user()->google2fa_enabled)
                                    🔒 Seguridad (2FA)
                                @else
                                    ⚠️ Activar 2FA
                                @endif
                            </a>
                        </li>

                        <!-- ============================================ -->
                        <!-- SECCIÓN ADMIN -->
                        <!-- ============================================ -->
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

                        <!-- ============================================ -->
                        <!-- SECCIÓN ADMIN Y RECEPCIONISTA -->
                        <!-- ============================================ -->
                        @if(auth()->user()->hasAnyRole(['admin', 'recepcionista']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gps.index') ? 'active' : '' }}" 
                               href="{{ route('gps.index') }}">
                                <i class="fas fa-map-marked-alt"></i> Seguimiento GPS
                            </a>
                        </li>
                        
                        <!-- MAPA DE VEHÍCULOS (SOLO ADMIN) -->
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link vehicle-map {{ request()->routeIs('gps.admin.mapa') ? 'active' : '' }}" 
                               href="{{ route('gps.admin.mapa') }}">
                                <i class="fas fa-map-marked-alt"></i> 
                                <i class="fas fa-truck ms-1"></i> 
                                Mapa de Vehículos
                            </a>
                        </li>
                        @endif
                        
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

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Contenido de la vista -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- BOTÓN Y SELECTOR DE TEMAS -->
    <!-- ============================================ -->
    <button class="theme-toggle-btn" id="themeToggleBtn" title="Cambiar tema">
        <i class="fas fa-palette"></i>
    </button>

    <div class="theme-picker-dropdown" id="themePicker">
        <button class="theme-option" data-theme="default">
            <span class="color-circle" style="background: #f4f6f9; border-color: #0d6efd;"></span>
            <span class="theme-name">🌞 Claro (Default)</span>
        </button>
        <button class="theme-option" data-theme="dark">
            <span class="color-circle" style="background: #1a1a2e; border-color: #4fc3f7;"></span>
            <span class="theme-name">🌙 Oscuro</span>
        </button>
        <button class="theme-option" data-theme="blue">
            <span class="color-circle" style="background: #e3f2fd; border-color: #0d47a1;"></span>
            <span class="theme-name">🔵 Azul</span>
        </button>
        <button class="theme-option" data-theme="green">
            <span class="color-circle" style="background: #e8f5e9; border-color: #1b5e20;"></span>
            <span class="theme-name">🟢 Verde</span>
        </button>
        <button class="theme-option" data-theme="purple">
            <span class="color-circle" style="background: #f3e5f5; border-color: #4a148c;"></span>
            <span class="theme-name">🟣 Morado</span>
        </button>
        <button class="theme-option" data-theme="red">
            <span class="color-circle" style="background: #ffebee; border-color: #b71c1c;"></span>
            <span class="theme-name">🔴 Rojo</span>
        </button>
        <button class="theme-option" data-theme="yellow">
            <span class="color-circle" style="background: #fffde7; border-color: #f57f17;"></span>
            <span class="theme-name">🟡 Amarillo</span>
        </button>
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

    <!-- JavaScript para el selector de temas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('themeToggleBtn');
            const themePicker = document.getElementById('themePicker');
            const themeOptions = document.querySelectorAll('.theme-option');

            // Alternar visibilidad del selector
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                themePicker.classList.toggle('show');
            });

            // Cerrar selector al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!themePicker.contains(e.target) && e.target !== toggleBtn) {
                    themePicker.classList.remove('show');
                }
            });

            // Aplicar tema seleccionado
            themeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const theme = this.dataset.theme;
                    applyTheme(theme);
                    themePicker.classList.remove('show');
                    // Guardar preferencia en localStorage
                    localStorage.setItem('mudatrack-theme', theme);
                });
            });

            // Función para aplicar tema
            function applyTheme(theme) {
                // Remover todas las clases de tema
                document.body.classList.remove(
                    'theme-dark', 'theme-blue', 'theme-green', 
                    'theme-purple', 'theme-red', 'theme-yellow'
                );
                
                if (theme !== 'default') {
                    document.body.classList.add('theme-' + theme);
                }
            }

            // Cargar tema guardado
            const savedTheme = localStorage.getItem('mudatrack-theme');
            if (savedTheme && savedTheme !== 'default') {
                applyTheme(savedTheme);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>