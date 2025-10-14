# MAPEO COMPLETO DE MIGRACIÓN
## Migración completada el 14 de octubre de 2025

### ESTRUCTURA FINAL:

```
Barbery/
├── app/                          # VISTAS (Frontend)
│   ├── auth/
│   │   ├── index.php            # Página principal / Login
│   │   └── recover.php          # Recuperación de contraseña
│   ├── client/
│   │   ├── profile.php          # Perfil del cliente
│   │   ├── appointments/
│   │   │   ├── request.php      # Solicitar cita
│   │   │   ├── turn.php         # Procesar cita
│   │   │   └── turnview.php     # Ver citas
│   │   └── invoices/
│   │       ├── invoice.php      # Ver factura
│   │       ├── lastinvoice.php  # Última factura
│   │       └── showinvoices.php # Listado de facturas
│   ├── admin/
│   │   ├── admin.php            # Panel de administración
│   │   ├── clients.php          # Gestión de clientes
│   │   ├── admin.html           # Vista admin estática
│   │   └── admin-online.html    # Vista admin online
│   └── contact.php              # Página de contacto
│
├── api/                          # BACKEND (Lógica de negocio)
│   ├── auth/
│   │   ├── login.php            # Procesar login
│   │   ├── register.php         # Procesar registro
│   │   └── logout.php           # Cerrar sesión
│   ├── client/
│   │   ├── update.php           # Actualizar perfil
│   │   ├── delete.php           # Eliminar perfil
│   │   └── getdata.php          # Obtener datos cliente
│   ├── services/
│   │   ├── added.php            # Agregar servicio
│   │   ├── modrem.php           # Modificar/remover servicio
│   │   ├── remove.php           # Eliminar servicio
│   │   └── saveIt.php           # Guardar servicio
│   ├── invoices/
│   │   └── addInvoice.php       # Agregar factura
│   └── utilities/
│       ├── db-backup.php        # Backup de BD
│       ├── export.php           # Exportar datos
│       ├── zip.php              # Comprimir archivos
│       ├── update.php           # Actualizar datos
│       └── showtotal.php        # Mostrar totales
│
├── assets/                       # RECURSOS ESTÁTICOS
│   ├── css/
│   │   └── style.css            # Estilos principales
│   ├── js/
│   │   └── script.js            # JavaScript principal
│   └── img/                     # Imágenes
│
├── config/                       # CONFIGURACIÓN
│   ├── connect.php              # Conexión a BD
│   └── paths.php                # Rutas del sistema
│
├── database/                     # BASE DE DATOS
│   ├── barbery.sql              # Schema de BD
│   └── update-img-paths.php     # Script actualización
│
├── includes/                     # COMPONENTES COMPARTIDOS
│   ├── header.php               # Header HTML
│   ├── footer.html              # Footer HTML
│   ├── conn.php                 # Conexión alternativa
│   ├── nav_*.php/html           # Menús de navegación
│   └── modal*.html              # Modales diversos
│
├── vendor/                       # DEPENDENCIAS (Composer)
├── DBase/                        # Base de datos local (?)
└── [archivos raíz]              # Redirecciones y backups

```

## ARCHIVOS MIGRADOS (Total: 27)

### FASE 5: SERVICIOS (4 archivos)
- `added.php` → `api/services/added.php`
- `modrem.php` → `api/services/modrem.php`
- `remove.php` → `api/services/remove.php`
- `saveIt.php` → `api/services/saveIt.php`

### FASE 6: FACTURAS (4 archivos)
- `addInvoice.php` → `api/invoices/addInvoice.php`
- `invoice.php` → `app/client/invoices/invoice.php`
- `lastinvoice.php` → `app/client/invoices/lastinvoice.php`
- `showinvoices.php` → `app/client/invoices/showinvoices.php`

### FASE 7: CITAS (3 archivos)
- `request.php` → `app/client/appointments/request.php`
- `turn.php` → `app/client/appointments/turn.php`
- `turnview.php` → `app/client/appointments/turnview.php`

### FASE 8: ADMINISTRACIÓN (4 archivos)
- `admin.php` → `app/admin/admin.php`
- `clients.php` → `app/admin/clients.php`
- `admin.html` → `app/admin/admin.html`
- `admin-online.html` → `app/admin/admin-online.html`

### FASE 9: UTILIDADES (5 archivos)
- `db-backup.php` → `api/utilities/db-backup.php`
- `export.php` → `api/utilities/export.php`
- `zip.php` → `api/utilities/zip.php`
- `update.php` → `api/utilities/update.php`
- `showtotal.php` → `api/utilities/showtotal.php`

### FASE 10: OTROS (2 archivos)
- `contact.php` → `app/contact.php`

### FASE 11: CONFIGURACIÓN (2 archivos)
- `connect.php` → `config/connect.php.old`
- `barbery.sql` → `database/barbery.sql.backup`

### FASE 12: LIMPIEZA
- Eliminadas carpetas obsoletas: `css/`, `js/`, `img/`

## ARCHIVOS CON REDIRECCIÓN EN RAÍZ

Los siguientes archivos en la raíz ahora redirigen a sus nuevas ubicaciones:
- `addInvoice.php`
- `invoice.php`
- `lastinvoice.php`
- `showinvoices.php`
- `request.php`
- `turnview.php`
- `admin.php`
- `clients.php`
- `contact.php`

## ARCHIVOS QUE PERMANECEN EN RAÍZ

### Scripts de desarrollo/testing:
- `test.php`, `test2.php` (pueden eliminarse en producción)
- `debug-images.php` (herramienta de depuración)
- `update-includes.php` (script de migración)
- `reorganize.php` (script de organización)
- `prepare-migration.ps1` (script PowerShell)

### Documentación:
- `INICIO-RAPIDO.md`
- `MIGRACION.md`
- `MAPEO-MIGRACION.md` (este archivo)

### Archivos backup:
- Todos los archivos `*.backup` (pueden eliminarse después de verificar)

## PRÓXIMOS PASOS

1. ✅ Verificar que todas las funcionalidades funcionan
2. ✅ Probar las redirecciones
3. ⚠️ Actualizar rutas en archivos que referencien los movidos
4. ⚠️ Eliminar archivos `.backup` después de confirmar
5. ⚠️ Eliminar scripts de test en producción
6. ✅ Commit a Git con todos los cambios

## BENEFICIOS DE LA NUEVA ESTRUCTURA

- ✅ **Separación clara**: Frontend (app/) vs Backend (api/)
- ✅ **Organización lógica**: Por funcionalidad (auth, client, admin)
- ✅ **Escalabilidad**: Fácil agregar nuevas funciones
- ✅ **Mantenibilidad**: Código más fácil de encontrar y editar
- ✅ **Seguridad**: APIs separadas de vistas
- ✅ **Performance**: Assets organizados y optimizados
- ✅ **Profesional**: Estructura tipo MVC empresarial
