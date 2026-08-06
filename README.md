# 🏢 Sistema de Gestión de Recursos Humanos (RRHH)

> **Nota:** Este proyecto está basado en la arquitectura del [Inventory Management System](https://github.com/fajarghifar/inventory-management-system) de [Fajar Ghifar](https://github.com/fajarghifar). Se adaptó y se está extendiendo activamente para cubrir las necesidades específicas de gestión de Recursos Humanos.

---

## 📋 Descripción

Sistema web modular para la **gestión integral de Recursos Humanos**, actualmente en desarrollo para la empresa **TIT Paraguay**. El objetivo es construir una plataforma que centralice la gestión de personal, automatice procesos de nómina y asistencia, y provea dashboards analíticos para la toma de decisiones basada en datos.

---

## ✅ Estado Actual

| Módulo | Estado |
|--------|--------|
| Diseño de base de datos relacional | ✅ Completado |
| Gestión de personal (CRUD) | ✅ Completado |
| Control de asistencia | 🔄 En desarrollo |
| Gestión de vacaciones y licencias | 🔄 En desarrollo |
| Dashboards analíticos con ApexCharts | 🔄 En desarrollo |
| Nómina y cálculo de salarios | ⏳ Pendiente |
| Reportes dinámicos con filtros avanzados | ⏳ Pendiente |

---

## 🎯 Objetivos del Proyecto

- **Centralizar** toda la información del personal en una única plataforma web.
- **Automatizar** el registro de asistencia y el cálculo de horas trabajadas.
- **Visualizar métricas clave** de RRHH en dashboards interactivos:
  - Rotación de personal.
  - Absentismo por departamento.
  - Costos de nómina por área.
  - Evolución histórica del personal.
- **Generar reportes dinámicos** orientados a la toma de decisiones gerenciales.
- **Optimizar** la gestión de vacaciones, licencias y permisos con flujos de aprobación.

---

## 🛠️ Stack Tecnológico

| Categoría | Tecnología |
|-----------|------------|
| **Framework Backend** | Laravel 12.x |
| **Frontend / Reactividad** | Laravel Livewire 3 + Alpine.js |
| **Estilos** | Tailwind CSS |
| **Tablas de Datos** | Livewire PowerGrid |
| **Gráficos y Visualización** | ApexCharts |
| **Iconos** | Blade Heroicons |
| **Base de Datos** | MySQL |
| **Control de Versiones** | Git + GitHub |

---

## 🚀 Instalación Local

### Requisitos previos
- PHP 8.2 o superior
- Composer
- Node.js y NPM
- MySQL

### Pasos

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/rromi182/sysweb.git
   cd sysweb
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar la base de datos** en el archivo `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nombre_de_tu_bd
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Enlazar almacenamiento:**
   ```bash
   php artisan storage:link
   ```

7. **Compilar assets:**
   ```bash
   npm install
   npm run build
   ```

8. **Iniciar servidor:**
   ```bash
   php artisan serve
   ```

9. **Credenciales por defecto:**
   - **Usuario:** `admin`
   - **Contraseña:** `password`

---

## 📸 Vista Previa

*(Próximamente: capturas de pantalla del dashboard de RRHH)*

---

## 🗺️ Roadmap

- [x] Diseño de base de datos relacional (tablas, relaciones, vistas, consultas SQL).
- [x] Módulo de gestión de personal (registro, edición, búsqueda).
- [ ] Módulo de control de asistencia (entrada/salida, cálculo de horas).
- [ ] Módulo de vacaciones y licencias (solicitudes, aprobaciones, saldo de días).
- [ ] Dashboards analíticos con ApexCharts (rotación, absentismo, costos).
- [ ] Módulo de nómina (cálculo de salarios, deducciones, historial de pagos).
- [ ] Reportes dinámicos con filtros avanzados y exportación.
- [ ] Notificaciones automáticas (vencimiento de contratos, cumpleaños, etc.).

---

## 👩‍💻 Desarrolladora

**María Romina Almeida Benítez**

- 📧 [romialmeida00@gmail.com](mailto:romialmeida00@gmail.com)
- 📱 +595 992 427 253
- 💼 [LinkedIn](https://linkedin.com/in/romialmeida)
- 🐙 [GitHub](https://github.com/rromi182)

---

## 🙏 Agradecimientos

Este proyecto se construyó sobre la base del excelente trabajo de **[Fajar Ghifar](https://github.com/fajarghifar)** y su [Inventory Management System](https://github.com/fajarghifar/inventory-management-system). Se adaptó y se está extendiendo con nuevos módulos específicos para la gestión de Recursos Humanos.

---

## 📄 Licencia

Este proyecto está bajo la licencia [MIT](LICENSE).

---

