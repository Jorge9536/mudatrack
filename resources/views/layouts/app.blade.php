<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MudaTrack - @yield('title', 'Sistema de Gestión')</title>
    
    <!-- ============================================ -->
    <!-- CDNs - CARGADOS GLOBALMENTE -->
    <!-- ============================================ -->
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet para mapas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    
    <!-- ============================================ -->
    <!-- FULLCALENDAR - CARGADO GLOBALMENTE -->
    <!-- ============================================ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    
    <style>
        /* ============================================ */
        /* VARIABLES DE COLOR GLOBALES (TEMA CLARO POR DEFECTO) */
        /* ============================================ */
        :root {
            /* Paleta por defecto (Gris azulado profesional) */
            --bg-body: #f8fafc;          /* Slate 50 */
            --bg-card: #ffffff;          /* Blanco */
            --bg-navbar: #ffffff;
            --bg-sidebar: #ffffff;
            --text-primary: #0f172a;     /* Slate 900 - Para títulos y números */
            --text-secondary: #334155;   /* Slate 700 - Texto normal */
            --text-muted: #64748b;       /* Slate 500 - Texto secundario */
            --border-color: #e2e8f0;     /* Slate 200 - Bordes suaves */
            --shadow-color: rgba(15, 23, 42, 0.06);
            --primary-color: #3b82f6;    /* Blue 500 - Acento principal */
            --primary-hover: #2563eb;    /* Blue 600 */
            --sidebar-active-bg: #3b82f6;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #f1f5f9; /* Slate 100 */
            --sidebar-hover-text: #3b82f6;
        }

        /* ============================================ */
        /* ESTILOS BASE */
        /* ============================================ */
        * {
            box-sizing: border-box;
        }
        
        body {
            background: var(--bg-body);
            color: var(--text-secondary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            width: 100%;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 800;
            color: var(--primary-color) !important;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
        }

        /* ============================================ */
        /* TARJETAS Y COMPONENTES */
        /* ============================================ */
        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 6px var(--shadow-color);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 20px var(--shadow-color);
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 18px 22px;
            font-weight: 700;
            color: var(--text-primary);
            font-size: 1.05rem;
        }

        .card-body {
            padding: 22px;
        }

        /* Números grandes en las tarjetas del dashboard */
        .card h2, .card .h2, .card .display-4, .card .display-5 {
            color: var(--text-primary);
            font-weight: 800;
            letter-spacing: -1px;
        }

        /* Textos secundarios dentro de las tarjetas */
        .card p, .card .text-muted {
            color: var(--text-muted) !important;
        }

        /* Títulos de sección dentro de las tarjetas */
        .card .card-title, .card h5, .card h6 {
            color: var(--text-primary);
        }

        /* ============================================ */
        /* 🎨 ARREGLO PARA TARJETAS DE ESTADO (SERVICIOS) */
        /* ============================================ */
        
        /* Convertimos las tarjetas de estado en tarjetas con fondo suave (pastel) 
           y borde de color, en lugar de fondo saturado. Esto hace que los números
           se lean perfectamente y se vea más profesional. */
        
        .card.bg-secondary {
            background-color: rgba(108, 117, 125, 0.1) !important;
            color: #475569 !important;
            border: 2px solid #94a3b8 !important;
        }
        .card.bg-secondary .display-4, .card.bg-secondary .h2, 
        .card.bg-secondary .h3, .card.bg-secondary h2, 
        .card.bg-secondary h3, .card.bg-secondary p {
            color: #475569 !important;
        }

        .card.bg-primary {
            background-color: rgba(59, 130, 246, 0.1) !important;
            color: #1e40af !important;
            border: 2px solid #3b82f6 !important;
        }
        .card.bg-primary .display-4, .card.bg-primary .h2, 
        .card.bg-primary .h3, .card.bg-primary h2, 
        .card.bg-primary h3, .card.bg-primary p {
            color: #1e40af !important;
        }

        .card.bg-warning {
            background-color: rgba(245, 158, 11, 0.1) !important;
            color: #92400e !important;
            border: 2px solid #f59e0b !important;
        }
        .card.bg-warning .display-4, .card.bg-warning .h2, 
        .card.bg-warning .h3, .card.bg-warning h2, 
        .card.bg-warning h3, .card.bg-warning p {
            color: #92400e !important;
        }

        .card.bg-success {
            background-color: rgba(16, 185, 129, 0.1) !important;
            color: #065f46 !important;
            border: 2px solid #10b981 !important;
        }
        .card.bg-success .display-4, .card.bg-success .h2, 
        .card.bg-success .h3, .card.bg-success h2, 
        .card.bg-success h3, .card.bg-success p {
            color: #065f46 !important;
        }

        .card.bg-danger {
            background-color: rgba(244, 63, 94, 0.1) !important;
            color: #9f1239 !important;
            border: 2px solid #f43f5e !important;
        }
        .card.bg-danger .display-4, .card.bg-danger .h2, 
        .card.bg-danger .h3, .card.bg-danger h2, 
        .card.bg-danger h3, .card.bg-danger p {
            color: #9f1239 !important;
        }

        .card.bg-info {
            background-color: rgba(14, 165, 233, 0.1) !important;
            color: #0c4a6e !important;
            border: 2px solid #0ea5e9 !important;
        }
        .card.bg-info .display-4, .card.bg-info .h2, 
        .card.bg-info .h3, .card.bg-info h2, 
        .card.bg-info h3, .card.bg-info p {
            color: #0c4a6e !important;
        }

        /* Caso general: si alguna tarjeta usa bg-dark o bg-custom, forzamos texto blanco */
        .card.bg-dark {
            background-color: rgba(15, 23, 42, 0.1) !important;
            color: #0f172a !important;
            border: 2px solid #0f172a !important;
        }
        .card.bg-dark .display-4, .card.bg-dark h2, .card.bg-dark h3, .card.bg-dark p {
            color: #0f172a !important;
        }

        /* ============================================ */
        /* SIDEBAR */
        /* ============================================ */
        .sidebar {
            min-height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            padding: 20px 0;
            transition: all 0.3s ease;
            overflow-x: hidden; 
        }
        
        .sidebar .nav-link {
            color: var(--text-secondary);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 12px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            white-space: normal; 
            line-height: 1.4;
        }
        
        .sidebar .nav-link:hover {
            background: var(--sidebar-hover-bg);
            color: var(--sidebar-hover-text);
            transform: translateX(4px);
        }
        
        .sidebar .nav-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        /* Estilos específicos para 2FA */
        .sidebar .nav-link.twofa-active { color: #10b981; }
        .sidebar .nav-link.twofa-active i { color: #10b981; }
        .sidebar .nav-link.twofa-inactive { color: #f59e0b; }
        .sidebar .nav-link.twofa-inactive i { color: #f59e0b; }
        
        /* Estilo para Mapa de Vehículos */
        .sidebar .nav-link.vehicle-map {
            background: linear-gradient(135deg, var(--sidebar-hover-bg), rgba(59, 130, 246, 0.1));
            border-left: 4px solid var(--primary-color);
        }
        .sidebar .nav-link.vehicle-map:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.05));
        }
        .sidebar .nav-link.vehicle-map.active {
            background: var(--primary-color);
            color: white;
            border-left-color: white;
        }
        .sidebar .nav-link.vehicle-map i.fa-truck { color: var(--primary-color); }
        .sidebar .nav-link.vehicle-map.active i.fa-truck { color: white; }
        
        /* ============================================ */
        /* BADGES Y ROLES */
        /* ============================================ */
        .role-badge {
            font-size: 0.65rem;
            padding: 3px 10px;
            border-radius: 20px;
            margin-left: 5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .role-badge.admin { background: #ef4444; color: white; }
        .role-badge.recepcionista { background: #3b82f6; color: white; }
        .role-badge.chofer { background: #f59e0b; color: #000; }
        
        #mapa {
            height: 350px;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            z-index: 1;
            width: 100%;
        }
        
        .leaflet-routing-container { display: none !important; }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* ============================================ */
        /* 📱 ESTILOS ESPECÍFICOS PARA MÓVIL */
        /* ============================================ */
        @media (max-width: 768px) {
            body { font-size: 14px; }
            .navbar { padding: 8px 10px !important; }
            .navbar-brand { font-size: 1.1rem !important; }
            
            .sidebar {
                min-height: auto !important;
                border-right: none !important;
                border-bottom: 1px solid var(--border-color);
                padding: 5px 10px !important;
                background: var(--bg-sidebar);
                width: 100% !important;
                position: sticky !important;
                top: 0 !important;
                z-index: 999 !important;
                box-shadow: 0 2px 4px var(--shadow-color);
            }
            
            .sidebar-toggle-mobile {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                padding: 8px 5px;
                background: transparent;
                border: none;
                color: var(--primary-color);
                font-weight: 700;
                cursor: pointer;
            }
            
            .sidebar-toggle-mobile i { font-size: 1.2rem; }
            
            .sidebar-menu-mobile {
                display: none;
                padding: 10px 0;
                background: var(--bg-sidebar);
            }
            .sidebar-menu-mobile.show { display: block; }
            
            .sidebar .nav-link {
                padding: 10px 12px !important;
                margin: 2px 0 !important;
                font-size: 13px !important;
                white-space: normal !important;
                border-radius: 8px;
            }
            .sidebar .nav-link i { width: 20px; font-size: 0.9rem; }
            
            #contenidoPrincipal {
                padding: 10px 12px !important;
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
            
            .card { margin-bottom: 12px !important; border-radius: 12px !important; }
            .card-header { padding: 12px 15px !important; font-size: 0.95rem !important; }
            .card-body { padding: 15px !important; }
            
            .table-responsive { font-size: 12px !important; }
            .table-responsive table { min-width: 600px; }
            
            .btn { font-size: 12px !important; padding: 6px 12px !important; border-radius: 8px !important; font-weight: 600; }
            .btn i { font-size: 0.85rem !important; }
            
            .form-control, .form-select { font-size: 14px !important; padding: 8px 12px !important; border-radius: 8px !important; }
            .form-label { font-size: 13px !important; margin-bottom: 4px !important; font-weight: 600; }
            
            #mapa { height: 250px !important; }
            .badge { font-size: 0.7rem !important; padding: 4px 8px !important; }
            
            .modal-dialog { margin: 10px !important; }
            .modal-content { border-radius: 14px !important; }
            .alert { padding: 12px 15px !important; font-size: 13px !important; margin-bottom: 10px !important; border-radius: 10px; }
            
            .hide-mobile { display: none !important; }
            .show-mobile { display: block !important; }
        }
        
        @media (max-width: 576px) {
            #contenidoPrincipal { padding: 8px 8px !important; }
            .navbar-brand { font-size: 1rem !important; }
            .sidebar .nav-link { font-size: 12px !important; padding: 8px 10px !important; }
            .card-header { padding: 10px 12px !important; font-size: 0.85rem !important; }
            .card-body { padding: 12px !important; }
            .btn { font-size: 11px !important; padding: 5px 10px !important; }
            .form-control, .form-select { font-size: 13px !important; padding: 6px 10px !important; }
            #mapa { height: 200px !important; }
            .modal-dialog { margin: 5px !important; }
            .row-cols-1-mobile > * { flex: 0 0 100% !important; max-width: 100% !important; }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .sidebar .nav-link { font-size: 13px !important; padding: 10px 15px !important; }
            #contenidoPrincipal { padding: 20px !important; }
            .card-header { padding: 15px 20px !important; }
        }
        
        .show-mobile { display: none !important; }
        .hide-mobile { display: block !important; }
        
        @media (max-width: 768px) {
            .show-mobile { display: block !important; }
            .hide-mobile { display: none !important; }
        }

        /* ============================================ */
        /* BOTÓN DE CAMBIO DE TEMA (THEME TOGGLE) */
        /* ============================================ */
        .theme-toggle-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1050;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: none;
            background: var(--primary-color);
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle-btn:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 8px 25px rgba(0,0,0,0.35);
        }

        .theme-picker-dropdown {
            position: fixed;
            bottom: 90px;
            right: 25px;
            z-index: 1050;
            background: var(--bg-card);
            border-radius: 14px;
            padding: 10px 0;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: none;
            min-width: 220px;
            overflow: hidden;
            border: 1px solid var(--border-color);
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
            padding: 12px 20px;
            cursor: pointer;
            transition: background 0.2s ease;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            font-size: 0.9rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .theme-option:hover {
            background: var(--sidebar-hover-bg);
        }

        .theme-option .color-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid var(--border-color);
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .theme-option .theme-name {
            font-weight: 600;
        }

        /* ============================================ */
        /* PALETAS DE COLORES PROFESIONALES (5 Tonos)  */
        /* ============================================ */

        /* --- TEMA OSCURO (Slate) --- */
        body.theme-dark {
            --bg-body: #0f172a;          /* Slate 900 */
            --bg-card: #1e293b;          /* Slate 800 */
            --bg-navbar: #1e293b;
            --bg-sidebar: #1e293b;
            --text-primary: #f8fafc;     /* Slate 50 */
            --text-secondary: #cbd5e1;   /* Slate 300 */
            --text-muted: #94a3b8;       /* Slate 400 */
            --border-color: #334155;     /* Slate 700 */
            --shadow-color: rgba(0, 0, 0, 0.4);
            --primary-color: #3b82f6;    /* Blue 500 */
            --primary-hover: #60a5fa;    /* Blue 400 */
            --sidebar-active-bg: #2563eb;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #334155;
            --sidebar-hover-text: #60a5fa;
        }
        body.theme-dark .card-header { border-bottom-color: #334155 !important; }
        body.theme-dark .form-control, body.theme-dark .form-select {
            background: #0f172a !important; border-color: #334155 !important; color: #f8fafc !important;
        }
        body.theme-dark .form-control:focus, body.theme-dark .form-select:focus {
            border-color: #3b82f6 !important; box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
        body.theme-dark .table { color: #cbd5e1 !important; }
        body.theme-dark .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(255,255,255,0.03); color: #cbd5e1 !important;
        }
        body.theme-dark .alert-success { background: #064e3b; border-color: #065f46; color: #6ee7b7; }
        body.theme-dark .alert-danger { background: #7f1d1d; border-color: #991b1b; color: #fca5a5; }
        body.theme-dark .alert-info { background: #1e3a8a; border-color: #1e40af; color: #93c5fd; }
        body.theme-dark .btn-close { filter: invert(1); }
        body.theme-dark .dropdown-menu { background: #1e293b; border-color: #334155; }
        body.theme-dark .dropdown-menu .dropdown-item { color: #cbd5e1 !important; }
        body.theme-dark .dropdown-menu .dropdown-item:hover { background: #334155 !important; }

        /* --- TEMA AZUL (Sky) --- */
        body.theme-blue {
            --bg-body: #f0f9ff;          /* Sky 50 */
            --bg-card: #ffffff;
            --bg-navbar: #ffffff;
            --bg-sidebar: #ffffff;
            --text-primary: #0c4a6e;     /* Sky 900 */
            --text-secondary: #075985;   /* Sky 800 */
            --text-muted: #0284c7;       /* Sky 600 */
            --border-color: #bae6fd;     /* Sky 200 */
            --shadow-color: rgba(12, 74, 110, 0.08);
            --primary-color: #0ea5e9;    /* Sky 500 */
            --primary-hover: #0284c7;    /* Sky 600 */
            --sidebar-active-bg: #0ea5e9;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #e0f2fe; /* Sky 100 */
            --sidebar-hover-text: #0284c7;
        }

        /* --- TEMA VERDE (Emerald) --- */
        body.theme-green {
            --bg-body: #ecfdf5;          /* Emerald 50 */
            --bg-card: #ffffff;
            --bg-navbar: #ffffff;
            --bg-sidebar: #ffffff;
            --text-primary: #064e3b;     /* Emerald 900 */
            --text-secondary: #065f46;   /* Emerald 800 */
            --text-muted: #059669;       /* Emerald 600 */
            --border-color: #a7f3d0;     /* Emerald 200 */
            --shadow-color: rgba(6, 78, 59, 0.08);
            --primary-color: #10b981;    /* Emerald 500 */
            --primary-hover: #059669;    /* Emerald 600 */
            --sidebar-active-bg: #10b981;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #d1fae5; /* Emerald 100 */
            --sidebar-hover-text: #059669;
        }

        /* --- TEMA MORADO (Violet) --- */
        body.theme-purple {
            --bg-body: #f5f3ff;          /* Violet 50 */
            --bg-card: #ffffff;
            --bg-navbar: #ffffff;
            --bg-sidebar: #ffffff;
            --text-primary: #2e1065;     /* Violet 900 */
            --text-secondary: #4c1d95;   /* Violet 800 */
            --text-muted: #7c3aed;       /* Violet 600 */
            --border-color: #ddd6fe;     /* Violet 200 */
            --shadow-color: rgba(46, 16, 101, 0.08);
            --primary-color: #8b5cf6;    /* Violet 500 */
            --primary-hover: #7c3aed;    /* Violet 600 */
            --sidebar-active-bg: #8b5cf6;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #ede9fe; /* Violet 100 */
            --sidebar-hover-text: #7c3aed;
        }

        /* --- TEMA ROJO (Rose) --- */
        body.theme-red {
            --bg-body: #fff1f2;          /* Rose 50 */
            --bg-card: #ffffff;
            --bg-navbar: #ffffff;
            --bg-sidebar: #ffffff;
            --text-primary: #881337;     /* Rose 900 */
            --text-secondary: #9f1239;   /* Rose 800 */
            --text-muted: #e11d48;       /* Rose 600 */
            --border-color: #fecdd3;     /* Rose 200 */
            --shadow-color: rgba(136, 19, 55, 0.08);
            --primary-color: #f43f5e;    /* Rose 500 */
            --primary-hover: #e11d48;    /* Rose 600 */
            --sidebar-active-bg: #f43f5e;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #ffe4e6; /* Rose 100 */
            --sidebar-hover-text: #e11d48;
        }

        /* --- TEMA AMARILLO (Amber) --- */
        body.theme-yellow {
            --bg-body: #fffbeb;          /* Amber 50 */
            --bg-card: #ffffff;
            --bg-navbar: #ffffff;
            --bg-sidebar: #ffffff;
            --text-primary: #78350f;     /* Amber 900 */
            --text-secondary: #92400e;   /* Amber 800 */
            --text-muted: #d97706;       /* Amber 600 */
            --border-color: #fde68a;     /* Amber 200 */
            --shadow-color: rgba(120, 53, 15, 0.08);
            --primary-color: #f59e0b;    /* Amber 500 */
            --primary-hover: #d97706;    /* Amber 600 */
            --sidebar-active-bg: #f59e0b;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #fef3c7; /* Amber 100 */
            --sidebar-hover-text: #d97706;
        }

        @media (max-width: 768px) {
            .theme-toggle-btn { width: 48px; height: 48px; font-size: 1.2rem; bottom: 15px; right: 15px; }
            .theme-picker-dropdown { bottom: 75px; right: 15px; min-width: 190px; }
            .theme-option { padding: 10px 16px; font-size: 0.85rem; }
            .theme-option .color-circle { width: 22px; height: 22px; }
        }
    </style>
</head>
<body>
    <!-- ============================================ -->
    <!-- NAVBAR -->
    <!-- ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm" style="background-color: var(--bg-navbar); border-bottom: 1px solid var(--border-color);">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-truck me-2"></i>MudaTrack 222
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" style="color: var(--text-primary); font-weight: 600;">
                                <i class="fas fa-user me-1"></i> {{ auth()->user()->name }}
                                @if(auth()->user()->isAdmin())
                                    <span class="role-badge admin">Admin</span>
                                @elseif(auth()->user()->isRecepcionista())
                                    <span class="role-badge recepcionista">Recepcionista</span>
                                @elseif(auth()->user()->isChofer())
                                    <span class="role-badge chofer">Chofer</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="background-color: var(--bg-card); border-color: var(--border-color); box-shadow: 0 10px 30px var(--shadow-color);">
                                <li>
                                    <a class="dropdown-item" href="{{ route('2fa.setup') }}" style="color: var(--text-secondary);">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        @if(auth()->user()->google2fa_enabled)
                                            <span class="text-success">🔒 2FA Activado</span>
                                        @else
                                            <span class="text-warning">⚠️ Activar 2FA</span>
                                        @endif
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" style="color: var(--text-secondary);"
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
            <!-- SIDEBAR -->
            <div class="col-lg-3 col-md-4 sidebar" id="sidebarPrincipal">
                <button class="sidebar-toggle-mobile d-md-none" type="button" id="btnToggleMenu">
                    <span><i class="fas fa-bars me-2"></i> Menú</span>
                    <i class="fas fa-chevron-down" id="iconToggle"></i>
                </button>
                
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
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('calendario.*') ? 'active' : '' }}" 
                               href="{{ route('calendario.index') }}">
                                <i class="fas fa-calendar-alt"></i> Calendario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dispositivos.*') ? 'active' : '' }}" 
                               href="{{ route('dispositivos.index') }}">
                                <i class="fas fa-satellite-dish"></i> Dispositivos GPS
                            </a>
                        </li>
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
                        @if(auth()->user()->hasAnyRole(['admin', 'recepcionista']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gps.index') ? 'active' : '' }}" 
                               href="{{ route('gps.index') }}">
                                <i class="fas fa-map-marked-alt"></i> Seguimiento GPS
                            </a>
                        </li>
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
            <div class="col-lg-9 col-md-8 col-12" id="contenidoPrincipal">
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

                @yield('content')
            </div>
        </div>
    </div>

    <!-- THEME TOGGLE -->
    <button class="theme-toggle-btn" id="themeToggleBtn" title="Cambiar tema">
        <i class="fas fa-palette"></i>
    </button>

    <div class="theme-picker-dropdown" id="themePicker">
        <button class="theme-option" data-theme="default">
            <span class="color-circle" style="background: #f8fafc; border-color: #3b82f6;"></span>
            <span class="theme-name">🌞 Claro (Default)</span>
        </button>
        <button class="theme-option" data-theme="dark">
            <span class="color-circle" style="background: #0f172a; border-color: #3b82f6;"></span>
            <span class="theme-name">🌙 Oscuro</span>
        </button>
        <button class="theme-option" data-theme="blue">
            <span class="color-circle" style="background: #f0f9ff; border-color: #0ea5e9;"></span>
            <span class="theme-name">🔵 Azul</span>
        </button>
        <button class="theme-option" data-theme="green">
            <span class="color-circle" style="background: #ecfdf5; border-color: #10b981;"></span>
            <span class="theme-name">🟢 Verde</span>
        </button>
        <button class="theme-option" data-theme="purple">
            <span class="color-circle" style="background: #f5f3ff; border-color: #8b5cf6;"></span>
            <span class="theme-name">🟣 Morado</span>
        </button>
        <button class="theme-option" data-theme="red">
            <span class="color-circle" style="background: #fff1f2; border-color: #f43f5e;"></span>
            <span class="theme-name">🔴 Rojo</span>
        </button>
        <button class="theme-option" data-theme="yellow">
            <span class="color-circle" style="background: #fffbeb; border-color: #f59e0b;"></span>
            <span class="theme-name">🟡 Amarillo</span>
        </button>
    </div>

    <!-- ============================================ -->
    <!-- SCRIPTS - CARGADOS GLOBALMENTE -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    
    <!-- ============================================ -->
    <!-- FULLCALENDAR JS - CARGADO GLOBALMENTE -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>
    
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

            if(toggleBtn && themePicker) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    themePicker.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!themePicker.contains(e.target) && e.target !== toggleBtn) {
                        themePicker.classList.remove('show');
                    }
                });

                themeOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        const theme = this.dataset.theme;
                        applyTheme(theme);
                        themePicker.classList.remove('show');
                        localStorage.setItem('mudatrack-theme', theme);
                    });
                });

                function applyTheme(theme) {
                    document.body.classList.remove(
                        'theme-dark', 'theme-blue', 'theme-green', 
                        'theme-purple', 'theme-red', 'theme-yellow'
                    );
                    
                    if (theme !== 'default') {
                        document.body.classList.add('theme-' + theme);
                    }
                }

                const savedTheme = localStorage.getItem('mudatrack-theme');
                if (savedTheme && savedTheme !== 'default') {
                    applyTheme(savedTheme);
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>