# 📋 FICHA TÉCNICA - SISTEMA MUDATRACK

## 1. IDENTIFICACIÓN DEL SISTEMA

| Campo | Valor |
|-------|-------|
| **Nombre del Sistema** | Mudatrack - Sistema Integral de Gestión Logística |
| **Versión** | v0.2 |
| **Fecha de Creación** | Agosto 2026 |
| **Desarrollador** | Jorge Luis Romero Salcedo |
| **Tutor** | Ing. Marcelo Ramirez Durán |
| **Institución** | Escuela Militar de Ingeniería (EMI) |

---

## 2. TECNOLOGÍAS UTILIZADAS

### Backend
| Tecnología | Versión | Descripción |
|------------|---------|-------------|
| PHP | 8.1+ | Lenguaje de programación |
| Laravel | 10.x | Framework MVC |
| PostgreSQL | 15+ | Base de datos relacional |

### Frontend
| Tecnología | Versión | Descripción |
|------------|---------|-------------|
| HTML5 | - | Estructura de páginas |
| Bootstrap | 5.x | Framework CSS responsive |
| JavaScript | ES6 | Interactividad |
| Leaflet | - | Biblioteca de mapas |

---

## 3. MÓDULOS DEL SISTEMA

1. **Gestión de Clientes** - Registro, búsqueda, historial
2. **Cotización Automática** - Cálculo basado en reglas de negocio
3. **Gestión de Servicios** - Estados, asignación de personal
4. **Seguimiento GPS** - Visualización en mapa en tiempo real
5. **Gestión de Pagos** - Registro, QR, control de morosos
6. **Reportes** - Estadísticas, exportación PDF/Excel

---

## 4. REGLAS DE NEGOCIO

| Código | Regla | Prioridad |
|--------|-------|-----------|
| RN-001 | Costo base por zona | Alta |
| RN-002 | Por ayudante: +80 Bs | Alta |
| RN-003 | Por piso: +20 Bs | Alta |
| RN-004 | Por callejón: +30 Bs | Media |
| RN-005 | Un chofer no puede tener dos servicios en el mismo horario | Alta |
| RN-006 | Estados: Pendiente → Confirmado → En Progreso → Finalizado → Pagado | Alta |

---

## 5. AUTORES

| Rol | Nombre |
|-----|--------|
| **Desarrollador** | Jorge Luis Romero Salcedo |
| **Tutor** | Ing. Marcelo Ramirez Durán |
| **Cliente** | Freddy Salcedo Pacheco |

---

## 6. ENLACES

| Recurso | Enlace |
|---------|--------|
| **Repositorio** | https://github.com/Jorge9536/mudatrack |
| **Documento Word** | /documento/Marco_Practico_Jorge_es.docx |

---

## 7. VERSIONES

| Versión | Fecha | Descripción |
|---------|-------|-------------|
| v0.1 | 14/08/2026 | Estructura inicial del proyecto |
| v0.2 | 17/08/2026 | Documentación completa y mejoras |

---

**Última actualización:** Agosto 2026
