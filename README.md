# 🚚 Mudatrack - Sistema Integral de Gestión Logística y Seguimiento GPS

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-blue)](https://postgresql.org)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-purple)](https://getbootstrap.com)
[![GitHub](https://img.shields.io/badge/GitHub-Repositorio-black)](https://github.com/Jorge9536/mudatrack)

---

## 📋 Descripción

Sistema web desarrollado para la empresa **"Transporte Libre Mudanza y Traslado (MudaTrack)"** que permite optimizar la gestión logística y el seguimiento de servicios de mudanza en tiempo real.

### 🎯 Objetivo General

Desarrollar un Sistema Integral de Gestión Logística y Seguimiento en Tiempo Real que permita optimizar el registro de clientes, cotizaciones, planificación de servicios y seguimiento de mudanzas, mejorando la eficiencia operativa y la calidad del servicio.

### ✅ Funcionalidades

| Módulo                         | Descripción                                              |
| ------------------------------ | -------------------------------------------------------- |
| 📝 **Registro de Clientes**    | Gestión de datos personales y ubicación                  |
| 💰 **Cotización Automática**   | Cálculo según reglas de negocio (zona, ayudantes, pisos) |
| 👥 **Asignación de Personal**  | Gestión de choferes, ayudantes y vehículos               |
| 📍 **Seguimiento GPS**         | Visualización en mapa interactivo en tiempo real         |
| 📄 **Comprobantes Digitales**  | Generación de comprobantes en PDF                        |
| 📊 **Reportes y Estadísticas** | Dashboard con métricas clave y clientes morosos          |
| 🔐 **Autenticación 2FA**       | Seguridad con doble factor de autenticación              |

---

## 🛠️ Tecnologías Utilizadas

### Backend

| Tecnología   | Versión | Descripción              |
| ------------ | ------- | ------------------------ |
| PHP          | 8.1+    | Lenguaje de programación |
| Laravel      | 10.x    | Framework MVC            |
| PostgreSQL   | 15+     | Base de datos relacional |
| Eloquent ORM | -       | Mapeo objeto-relacional  |

### Frontend

| Tecnología | Versión | Descripción              |
| ---------- | ------- | ------------------------ |
| HTML5      | -       | Estructura de páginas    |
| CSS3       | -       | Estilos y diseño         |
| Bootstrap  | 5.x     | Framework CSS responsive |
| JavaScript | ES6     | Interactividad           |
| Leaflet    | -       | Biblioteca de mapas      |

### Herramientas de Desarrollo

| Herramienta        | Descripción                           |
| ------------------ | ------------------------------------- |
| Visual Studio Code | Editor de código                      |
| Git                | Control de versiones                  |
| GitHub             | Repositorio remoto                    |
| PostgreSQL pgAdmin | Administración de BD                  |
| Firebase           | Base de datos en tiempo real para GPS |

---

## 📁 Estructura del Proyecto

```bash
mudatrack10/
├── README.md                 # Descripción del proyecto
├── CHANGELOG.md              # Historial de cambios
├── .gitignore                # Archivos ignorados por Git
├── .env                      # Variables de entorno
├── composer.json             # Dependencias PHP
├── package.json              # Dependencias Node
│
├── app/
│   ├── Http/
│   │   ├── Auth/             # Autenticación
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   ├── Controllers/      # Controladores principales
│   │   │   ├── AyudanteController.php
│   │   │   ├── ChoferController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── ConfiguracionPrecioController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── GpsController.php
│   │   │   ├── GpsFirebaseController.php
│   │   │   ├── PagoController.php
│   │   │   ├── ReporteController.php
│   │   │   ├── ServicioController.php
│   │   │   ├── TwoFactorController.php
│   │   │   ├── UserController.php
│   │   │   └── VehiculoController.php
│   │   └── Middleware/       # Middlewares
│   │       ├── RoleMiddleware.php
│   │       └── TwoFactorMiddleware.php
│   └── Models/               # Modelos
│       ├── Ayudante.php
│       ├── Bien.php
│       ├── Chofer.php
│       ├── Cliente.php
│       ├── ConfiguracionPrecio.php
│       ├── ConfiguracionQr.php
│       ├── Deuda.php
│       ├── Servicio.php
│       ├── UbicacionGps.php
│       ├── User.php
│       └── Vehiculo.php
│
├── database/
│   ├── factories/            # Factories para pruebas
│   │   ├── AyudanteFactory.php
│   │   ├── BienFactory.php
│   │   ├── ChoferFactory.php
│   │   ├── ClienteFactory.php
│   │   ├── DeudaFactory.php
│   │   ├── ServicioFactory.php
│   │   ├── UbicacionGpsFactory.php
│   │   ├── UserFactory.php
│   │   └── VehiculoFactory.php
│   ├── migrations/           # Migraciones
│   └── seeders/              # Seeders
│       └── DatabaseSeeder.php
│
├── resources/
│   └── views/                # Vistas Blade
│       ├── auth/             # Autenticación
│       │   ├── login.blade.php
│       │   └── verify-2fa.blade.php
│       ├── ayudantes/        # Gestión de ayudantes
│       ├── choferes/         # Gestión de choferes
│       ├── clientes/         # Gestión de clientes
│       ├── configuracion/    # Configuración
│       ├── dashboard/        # Dashboard
│       ├── gps/              # Seguimiento GPS
│       ├── layouts/          # Layouts base
│       ├── pagos/            # Gestión de pagos
│       ├── pdf/              # Comprobantes PDF
│       ├── profile/          # Perfil de usuario
│       ├── reportes/         # Reportes
│       ├── servicios/        # Gestión de servicios
│       ├── users/            # Gestión de usuarios
│       └── vehiculos/        # Gestión de vehículos
│
├── routes/
│   ├── web.php               # Rutas web
│   ├── api.php               # Rutas API
│   └── console.php           # Rutas consola
│
├── documento/                # Documento de trabajo de grado
│   └── Marco_Practico_Jorge_es.docx
│
└── docs/                     # Documentación técnica
    └── FICHA_TECNICA.md
```
