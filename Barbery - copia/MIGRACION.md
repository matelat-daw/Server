# 🚀 Plan de Reorganización del Proyecto Barbery

## 📋 Objetivo
Reorganizar la estructura del proyecto para que sea más mantenible, escalable y profesional.

## 🏗️ Nueva Estructura

```
Barbery/
├── 📱 app/                      # FRONTEND - Vistas y páginas
│   ├── auth/                    # Autenticación
│   │   ├── index.php           # Página de login/registro
│   │   └── recover.php         # Recuperar contraseña
│   ├── client/                  # Área de clientes
│   │   ├── profile.php         # Perfil del cliente
│   │   ├── appointments/       # Sistema de citas
│   │   │   ├── request.php    # Solicitar turno
│   │   │   ├── turn.php       # Confirmar turno
│   │   │   └── turnview.php   # Ver turnos
│   │   └── invoices/          # Facturas del cliente
│   │       ├── invoice.php
│   │       ├── lastinvoice.php
│   │       └── showinvoices.php
│   ├── admin/                  # Área de administración
│   │   ├── admin.php
│   │   ├── clients.php
│   │   └── showtotal.php
│   └── public/                 # Páginas públicas
│       └── contact.php
│
├── 🔌 api/                      # BACKEND - Lógica de negocio
│   ├── auth/                    # API de autenticación
│   │   ├── register.php        # Procesar registro
│   │   └── logout.php          # Cerrar sesión
│   ├── client/                  # API de clientes
│   │   ├── update.php          # Actualizar datos
│   │   ├── delete.php          # Eliminar cuenta
│   │   └── getdata.php         # Obtener datos
│   ├── invoices/               # API de facturas
│   │   ├── add.php            # Crear factura
│   │   ├── export.php         # Exportar
│   │   └── save.php           # Guardar
│   ├── services/               # API de servicios
│   │   ├── add.php            # Agregar servicio
│   │   ├── remove.php         # Eliminar servicio
│   │   └── delete.php         # Borrar
│   └── backup/                 # API de backup
│       ├── db-backup.php
│       └── zip.php
│
├── ⚙️ config/                   # CONFIGURACIÓN
│   ├── database.php            # Conexión DB (antes conn.php)
│   └── paths.php               # Rutas del proyecto
│
├── 🧩 includes/                 # COMPONENTES COMPARTIDOS
│   ├── header.php
│   ├── footer.html
│   ├── conn.php               # Mantener por compatibilidad
│   ├── modals/                # Modales
│   └── navigation/            # Navegación
│
├── 🎨 assets/                   # RECURSOS ESTÁTICOS
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── img/
│
├── 💾 database/                 # BASE DE DATOS
│   ├── barbery.sql
│   └── backups/
│
└── 📦 vendor/                   # DEPENDENCIAS (PHPOffice, etc.)
```

## 🔄 Mapeo de Archivos

### Autenticación
| Anterior | Nuevo | Tipo |
|----------|-------|------|
| `index.php` | `app/auth/index.php` | Vista |
| `register.php` | `api/auth/register.php` | API |
| `recover.php` | `app/auth/recover.php` | Vista |
| `endsession.php` | `api/auth/logout.php` | API |

### Cliente
| Anterior | Nuevo | Tipo |
|----------|-------|------|
| `profile.php` | `app/client/profile.php` | Vista |
| `modify.php` | `api/client/update.php` | API |
| `delete.php` | `api/client/delete.php` | API |
| `getdata.php` | `api/client/getdata.php` | API |

### Citas
| Anterior | Nuevo | Tipo |
|----------|-------|------|
| `request.php` | `app/client/appointments/request.php` | Vista |
| `turn.php` | `app/client/appointments/turn.php` | API/Vista |
| `turnview.php` | `app/client/appointments/turnview.php` | Vista |

### Facturas
| Anterior | Nuevo | Tipo |
|----------|-------|------|
| `invoice.php` | `app/client/invoices/invoice.php` | Vista |
| `lastinvoice.php` | `app/client/invoices/lastinvoice.php` | Vista |
| `showinvoices.php` | `app/client/invoices/showinvoices.php` | Vista |
| `addInvoice.php` | `api/invoices/add.php` | API |
| `export.php` | `api/invoices/export.php` | API |
| `saveIt.php` | `api/invoices/save.php` | API |

### Administración
| Anterior | Nuevo | Tipo |
|----------|-------|------|
| `admin.php` | `app/admin/admin.php` | Vista |
| `clients.php` | `app/admin/clients.php` | Vista |
| `showtotal.php` | `app/admin/showtotal.php` | Vista |

### Servicios
| Anterior | Nuevo | Tipo |
|----------|-------|------|
| `added.php` | `api/services/add.php` | API |
| `modrem.php` | `api/services/remove.php` | API |
| `remove.php` | `api/services/delete.php` | API |

## 📝 Cambios Necesarios en el Código

### 1. Actualizar Includes
**Antes:**
```php
include "includes/conn.php";
include "includes/header.php";
```

**Después (con helpers):**
```php
require_once __DIR__ . '/../../config/paths.php';
require_file('conn.php', 'include');
require_file('header.php', 'include');
```

**O manualmente calculando niveles:**
```php
// En app/client/profile.php (2 niveles)
include "../../includes/conn.php";
include "../../includes/header.php";

// En app/client/appointments/request.php (3 niveles)
include "../../../includes/conn.php";
include "../../../includes/header.php";
```

### 2. Actualizar Rutas de Assets
**Antes:**
```php
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js"></script>
```

**Después:**
```php
<link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
<script src="<?php echo asset('js/script.js'); ?>"></script>
```

**O manualmente:**
```php
<!-- Desde app/client/profile.php -->
<link rel="stylesheet" href="../../assets/css/style.css">
<script src="../../assets/js/script.js"></script>
```

### 3. Actualizar Actions de Formularios
**Antes:**
```php
<form action="register.php" method="post">
<form action="turn.php" method="post">
```

**Después:**
```php
<form action="../../api/auth/register.php" method="post">
<form action="../appointments/turn.php" method="post">
```

### 4. Actualizar Redirecciones
**Antes:**
```php
header('Location: profile.php');
window.location.href = 'admin.php';
```

**Después:**
```php
// En API files
header('Location: ../../app/client/profile.php');

// En JavaScript
window.location.href = '../../app/admin/admin.php';
```

## 🛠️ Pasos de Migración

### Opción 1: Migración Manual (Recomendada)

1. **Hacer backup completo**
   ```powershell
   cp -r c:\Nginx-Server\html\Barbery c:\Nginx-Server\html\Barbery_backup
   ```

2. **Crear estructura de carpetas**
   - Ejecutar: `php reorganize.php` (solo crea carpetas)

3. **Mover archivos uno por uno**
   - Copiar (no mover) cada archivo a su nueva ubicación
   - Probar que funciona antes de borrar el original

4. **Actualizar includes en cada archivo**
   - Buscar todos los `include` y `require`
   - Actualizar rutas según profundidad

5. **Actualizar assets (CSS/JS)**
   - Actualizar rutas en header.php
   - Verificar que carguen correctamente

6. **Actualizar formularios**
   - Revisar todos los `action=""` en formularios
   - Actualizar rutas relativas

7. **Probar funcionalidad**
   - Login/Register
   - Perfil
   - Solicitar turno
   - Facturas
   - Admin

8. **Eliminar archivos antiguos**
   - Solo después de verificar que todo funciona

### Opción 2: Crear archivo .htaccess para redirecciones

Crear un `.htaccess` que redirija automáticamente:

```apache
RewriteEngine On

# Redireccionar archivos antiguos a nuevos
RewriteRule ^index\.php$ app/auth/index.php [L]
RewriteRule ^profile\.php$ app/client/profile.php [L]
RewriteRule ^request\.php$ app/client/appointments/request.php [L]
# ... etc
```

## ✅ Checklist de Migración

- [ ] Backup completo realizado
- [ ] Estructura de carpetas creada
- [ ] `config/paths.php` configurado
- [ ] `includes/conn.php` actualizado
- [ ] Archivos AUTH movidos y probados
- [ ] Archivos CLIENT movidos y probados
- [ ] Archivos APPOINTMENTS movidos y probados
- [ ] Archivos INVOICES movidos y probados
- [ ] Archivos ADMIN movidos y probados
- [ ] Archivos API movidos y probados
- [ ] Assets (CSS/JS/IMG) movidos
- [ ] Includes actualizados
- [ ] Formularios actualizados
- [ ] Redirecciones actualizadas
- [ ] Pruebas completas realizadas
- [ ] Archivos antiguos eliminados

## 🔍 Archivos a Revisar

Estos archivos necesitan actualización de rutas:
- `includes/header.php` - Rutas de assets
- `includes/nav_*.php` - Enlaces de navegación
- Todos los archivos con formularios
- Todos los archivos con redirecciones

## 💡 Recomendaciones

1. **No hacer todo de una vez**: Migrar por módulos (auth primero, luego client, etc.)
2. **Mantener ambas versiones temporalmente**: Tener archivos antiguos como respaldo
3. **Usar control de versiones**: Git para rastrear cambios
4. **Probar en desarrollo primero**: No hacer en producción directamente
5. **Documentar cambios**: Anotar cualquier problema encontrado

## 🐛 Problemas Comunes y Soluciones

### Error: "No se encuentra el archivo"
**Solución**: Verificar la profundidad de carpetas y ajustar `../` en las rutas

### Error: "Headers already sent"
**Solución**: Verificar que no haya espacios antes de `<?php` en los archivos movidos

### CSS/JS no cargan
**Solución**: Actualizar rutas en `header.php` o usar rutas absolutas

### Sesiones no funcionan
**Solución**: Verificar que `session_start()` esté en el archivo correcto

## 📞 Ayuda

Si encuentras problemas durante la migración, documenta:
1. Qué archivo estabas moviendo
2. Qué error obtuviste
3. Qué rutas estás usando

---

**¿Deseas que comience con la migración automática o prefieres hacerlo manualmente?**
