# 📚 Guía Rápida de Reorganización

## 🎯 Objetivo
Transformar la estructura plana actual en una arquitectura organizada y escalable.

## 🚀 Inicio Rápido

### Paso 1: Preparación (5 minutos)
```powershell
cd c:\Nginx-Server\html\Barbery
.\prepare-migration.ps1
```

Esto creará:
- ✅ Backup automático con fecha/hora
- ✅ Estructura completa de carpetas
- ✅ Reporte de archivos a migrar

### Paso 2: Configuración (2 minutos)
Los archivos ya están creados:
- ✅ `config/paths.php` - Sistema de rutas
- ✅ `MIGRACION.md` - Plan detallado

### Paso 3: Migración por Fases

#### Fase 1: Assets (Sin riesgo) ⭐ EMPIEZA AQUÍ
```powershell
# Mover CSS
Move-Item css\style.css assets\css\

# Mover JS
Move-Item js\script.js assets\js\

# Mover imágenes (si las hay)
Move-Item img\* assets\img\
```

**Actualizar `includes/header.php`:**
```php
<!-- Antes -->
<link rel="stylesheet" href="css/style.css">
<script src="js/script.js"></script>

<!-- Después -->
<link rel="stylesheet" href="assets/css/style.css">
<script src="assets/js/script.js"></script>
```

✅ **Probar**: Abrir cualquier página y verificar que los estilos carguen

#### Fase 2: Configuración (Bajo riesgo)
```powershell
# Copiar (no mover) el archivo de conexión
Copy-Item includes\conn.php config\database.php
```

**Actualizar `config/database.php`:**
```php
<?php
// Mantener el contenido actual pero agregar:
define('DB_CONFIG_LOADED', true);
?>
```

**Crear `includes/conn.php` como puente:**
```php
<?php
// Puente temporal para mantener compatibilidad
require_once __DIR__ . '/../config/database.php';
?>
```

✅ **Probar**: Login debería funcionar igual

#### Fase 3: Autenticación (Riesgo medio)
```powershell
# Copiar index.php
Copy-Item index.php app\auth\index.php

# Copiar register.php
Copy-Item register.php api\auth\register.php

# Copiar recover.php
Copy-Item recover.php app\auth\recover.php
```

**Actualizar `app/auth/index.php`:**
```php
// Cambiar línea 2:
include "includes/conn.php";
// Por:
include "../../includes/conn.php";

// Cambiar línea 4:
include "includes/header.php";
// Por:
include "../../includes/header.php";

// Al final, cambiar:
include "includes/footer.html";
// Por:
include "../../includes/footer.html";

// En el formulario de registro, cambiar:
<form action="register.php" method="post"...
// Por:
<form action="../../api/auth/register.php" method="post"...
```

**Actualizar `api/auth/register.php`:**
```php
// Cambiar:
include "includes/conn.php";
// Por:
include "../../includes/conn.php";

// Al final, cambiar la redirección:
// Si está en PHP:
header('Location: ../../app/auth/index.php');
// Si está en JavaScript:
window.location.href = '../../app/auth/index.php';
```

**Crear redirección en `index.php` original:**
```php
<?php
// Redirección temporal
header('Location: app/auth/index.php');
exit;
?>
```

✅ **Probar**: 
- Ir a `localhost/Barbery/` debería redirigir
- Login debería funcionar
- Registro debería funcionar

#### Fase 4: Cliente (Riesgo medio)
```powershell
# Perfil
Copy-Item profile.php app\client\profile.php
Copy-Item modify.php api\client\update.php
Copy-Item delete.php api\client\delete.php
```

**Actualizar `app/client/profile.php`:**
```php
// Línea 1-2:
include "includes/conn.php";
include "getdata.php";
// Por:
include "../../includes/conn.php";
include "../../api/client/getdata.php";

// Todos los includes:
include "includes/XXX";
// Por:
include "../../includes/XXX";

// Form action:
<form action='modify.php' method='post'...
// Por:
<form action='../../api/client/update.php' method='post'...

// Form action delete:
<form action="delete.php" method="post">
// Por:
<form action="../../api/client/delete.php" method="post">
```

**Actualizar `api/client/update.php` (modify.php):**
```php
// Cambiar includes:
include "includes/conn.php";
// Por:
include "../../includes/conn.php";

// Cambiar redirección al final:
header('Location: ../../app/client/profile.php');
```

**Crear redirección en `profile.php` original:**
```php
<?php
header('Location: app/client/profile.php');
exit;
?>
```

✅ **Probar**: Perfil debería cargar y modificar datos

## 🎨 Patrón de Actualización de Rutas

### Para archivos en `app/`:
```
app/
├── auth/ (1 nivel)      → ../includes/
├── client/ (1 nivel)    → ../includes/
│   ├── appointments/ (2 niveles) → ../../includes/
│   └── invoices/ (2 niveles)     → ../../includes/
├── admin/ (1 nivel)     → ../includes/
└── public/ (1 nivel)    → ../includes/
```

### Para archivos en `api/`:
```
api/
├── auth/ (1 nivel)      → ../../includes/
├── client/ (1 nivel)    → ../../includes/
├── invoices/ (1 nivel)  → ../../includes/
├── services/ (1 nivel)  → ../../includes/
└── backup/ (1 nivel)    → ../../includes/
```

### Regla Simple:
**Contar niveles hacia atrás:**
- 1 carpeta de profundidad = `../`
- 2 carpetas = `../../`
- 3 carpetas = `../../../`

## 📋 Checklist Rápido

### Antes de migrar cada archivo:
1. [ ] Hacer copia del archivo
2. [ ] Contar niveles de profundidad
3. [ ] Actualizar todos los `include`
4. [ ] Actualizar todos los `action=` en forms
5. [ ] Actualizar `header()` redirects
6. [ ] Actualizar `window.location` en JS
7. [ ] Probar que funciona
8. [ ] Crear redirección en archivo original

### Después de migrar:
1. [ ] Probar la funcionalidad completa
2. [ ] Verificar que los estilos carguen
3. [ ] Verificar que los scripts funcionen
4. [ ] Probar formularios
5. [ ] Probar redirecciones

## ⚠️ Reglas de Oro

1. **NUNCA borres el original hasta probar**
2. **Copia primero, mueve después**
3. **Prueba cada fase antes de continuar**
4. **Mantén el backup seguro**
5. **Documenta cualquier cambio especial**

## 🆘 Si algo sale mal

```powershell
# Restaurar desde backup
Remove-Item -Recurse c:\Nginx-Server\html\Barbery
Copy-Item -Recurse c:\Nginx-Server\html\Barbery_backup_YYYYMMDD_HHMMSS c:\Nginx-Server\html\Barbery
```

## 📞 Siguiente Nivel

Una vez completada la migración básica:
1. Implementar autoload de clases
2. Crear un router central
3. Separar lógica de presentación
4. Implementar templates
5. Crear una API REST

---

**¿Listo para empezar?** Ejecuta el script de preparación y comienza con la Fase 1 (Assets)
