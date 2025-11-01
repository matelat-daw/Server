# 🚀 Guía de Actualización - Nueva-WEB

## Resumen de Cambios

Se ha realizado una optimización completa del sistema de perfiles de usuario, incluyendo:

- ✅ Campos adicionales en la tabla `users`: `first_name`, `last_name`, `gender`, `profile_img`
- ✅ Sistema de imágenes de perfil con avatares por género
- ✅ Optimización del código frontend y backend
- ✅ Eliminación de código redundante
- ✅ Mejora en el flujo de autenticación y menú de usuario

## 📋 Requisitos Previos

- Servidor Apache o Nginx con PHP 7.4+
- MySQL 5.7+ o MariaDB 10.3+
- Extensión PHP GD o Imagick (para manejo de imágenes)
- Permisos de escritura en la carpeta `uploads/`

## 🔧 Instalación

### Paso 1: Actualizar la Base de Datos

#### Opción A: Base de Datos Nueva
```bash
mysql -u root -p
CREATE DATABASE newapp;
USE newapp;
source c:/Server/html/Nueva-WEB/database/newapp_schema.sql
```

#### Opción B: Base de Datos Existente
```bash
mysql -u root -p newapp < c:/Server/html/Nueva-WEB/database/migrate_users.sql
```

### Paso 2: Migrar Imágenes de Usuarios Existentes

#### Opción A: Desde línea de comandos
```bash
cd c:/Server/html/Nueva-WEB/database
php migrate_profile_images.php
```

#### Opción B: Desde el navegador
```
http://localhost/Nueva-WEB/database/migrate_profile_images.php
```

### Paso 3: Verificar Permisos

#### En Windows (PowerShell como Administrador):
```powershell
icacls "c:\Server\html\Nueva-WEB\api\uploads" /grant Users:F /T
```

#### En Linux/Mac:
```bash
chmod -R 777 /path/to/Nueva-WEB/api/uploads
```

### Paso 4: Limpiar Caché del Navegador

1. Abre DevTools (F12)
2. Haz clic derecho en el botón de recargar
3. Selecciona "Vaciar caché y recargar de forma forzada"

## 🧪 Pruebas de Verificación

### 1. Probar Registro sin Imagen
```
POST /Nueva-WEB/api/register
{
  "username": "testuser",
  "email": "test@example.com",
  "password": "password123",
  "gender": "male"
}
```
**Resultado esperado:** 
- Usuario creado con imagen `users/1/profile.png` copiada desde `media/male.png`

### 2. Probar Registro con Imagen
```
POST /Nueva-WEB/api/register (multipart/form-data)
{
  "username": "testuser2",
  "email": "test2@example.com",
  "password": "password123",
  "gender": "female",
  "profile_image": [archivo]
}
```
**Resultado esperado:**
- Usuario creado con imagen personalizada en `users/2/profile.{extension}`

### 3. Probar Login
```
POST /Nueva-WEB/api/login
{
  "email": "test@example.com",
  "password": "password123"
}
```
**Resultado esperado:**
- Respuesta incluye `profile_img` con ruta completa

### 4. Probar Menú de Usuario
1. Haz login en la aplicación
2. Verifica que aparece el menú de usuario con avatar
3. Recarga la página (F5)
4. El menú de usuario debe permanecer visible

## 📁 Estructura de Archivos Modificados

```
Nueva-WEB/
├── database/
│   ├── newapp_schema.sql              [MODIFICADO]
│   ├── migrate_users.sql              [NUEVO]
│   └── migrate_profile_images.php     [NUEVO]
├── api/
│   ├── models/
│   │   └── User.php                   [MODIFICADO]
│   └── controllers/
│       └── AuthController.php         [MODIFICADO]
├── frontend/
│   ├── app.js                         [MODIFICADO]
│   ├── components/
│   │   ├── nav/
│   │   │   └── nav.js                 [MODIFICADO]
│   │   └── user-menu/
│   │       └── user-menu.js           [MODIFICADO]
├── OPTIMIZACIONES-COMPLETADAS.md     [NUEVO]
└── test-optimizaciones.html           [NUEVO]
```

## 🐛 Solución de Problemas

### Problema: "No se pueden cargar las imágenes"
**Solución:** Verifica los permisos de la carpeta `uploads/`

### Problema: "El menú de usuario desaparece tras recargar"
**Solución:** 
1. Abre DevTools (F12) > Console
2. Verifica si hay errores de validación de token
3. Verifica que la cookie `auth_token` está presente en Application > Cookies

### Problema: "Error al subir imagen"
**Solución:**
1. Verifica el tamaño de la imagen (máx 5MB)
2. Verifica el formato (jpg, png, gif, webp)
3. Verifica los permisos de escritura en `uploads/users/`

### Problema: "La migración de imágenes falla"
**Solución:**
1. Verifica que existen los archivos en `media/` (male.png, female.png, other.png)
2. Verifica permisos de lectura en `media/`
3. Verifica permisos de escritura en `api/uploads/users/`

## 📊 Base de Datos - Estructura Actualizada

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),              -- NUEVO
    last_name VARCHAR(50),               -- NUEVO
    gender ENUM('male', 'female', 'other') DEFAULT 'other',  -- NUEVO
    profile_img VARCHAR(255) DEFAULT NULL,  -- NUEVO
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## 🔐 Seguridad

- ✅ Contraseñas hasheadas con bcrypt (cost 12)
- ✅ Validación de tipos de archivo en upload
- ✅ Límite de tamaño de archivo (5MB)
- ✅ Sanitización de inputs con `htmlspecialchars`
- ✅ JWT con expiración de 7 días
- ✅ Cookies HTTP-only para tokens

## 📈 Mejoras de Rendimiento

- ✅ Singleton en app.js (previene duplicaciones)
- ✅ Lazy loading de componentes
- ✅ Retry logic con límites (evita loops infinitos)
- ✅ Caché de usuario en localStorage
- ✅ Validación de token en segundo plano

## 🎯 Próximos Pasos Sugeridos

1. **Implementar página de perfil de usuario**
   - Formulario de edición completo
   - Preview de imagen antes de subir
   - Validaciones en tiempo real

2. **Mejorar manejo de imágenes**
   - Redimensionamiento automático
   - Recorte de imágenes
   - Compresión automática

3. **Agregar más funcionalidades**
   - Sistema de notificaciones
   - Historial de compras
   - Wishlist/Favoritos
   - Recuperación de contraseña

## 📞 Soporte

Si encuentras algún problema o necesitas ayuda:
1. Revisa la documentación en `OPTIMIZACIONES-COMPLETADAS.md`
2. Abre el archivo `test-optimizaciones.html` en tu navegador
3. Verifica los logs de la consola del navegador (F12)

## 📝 Notas Finales

- Todos los cambios son **retrocompatibles**
- Los usuarios existentes seguirán funcionando
- La migración de imágenes es **opcional** pero recomendada
- El sistema asigna avatares por defecto según género automáticamente

---

**¡Actualización completada con éxito! 🎉**

Para más detalles técnicos, consulta `OPTIMIZACIONES-COMPLETADAS.md`
