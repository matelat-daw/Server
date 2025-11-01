# Resumen de Optimizaciones - Nueva-WEB

## Fecha: 1 de Noviembre de 2025

## Cambios en la Base de Datos

### 1. Esquema actualizado (newapp_schema.sql)
- ✅ Agregado campo `first_name VARCHAR(50)` para nombre del usuario
- ✅ Agregado campo `last_name VARCHAR(50)` para apellido del usuario
- ✅ Agregado campo `gender ENUM('male', 'female', 'other')` para género
- ✅ Agregado campo `profile_img VARCHAR(255)` para ruta de imagen de perfil

### 2. Scripts de migración creados
- `migrate_users.sql`: Script SQL para agregar columnas a tabla users existente
- `migrate_profile_images.php`: Script PHP para copiar imágenes por defecto a usuarios existentes

## Cambios en el Backend (PHP)

### 1. Modelo User (api/models/User.php)
**Optimizaciones:**
- ✅ Agregados campos: `first_name`, `last_name`, `gender`, `profile_img`
- ✅ Método `register()`: Ahora inserta todos los campos nuevos
- ✅ Método `emailExists()`: Retorna todos los campos del usuario
- ✅ Método `update()`: Actualiza todos los campos de forma condicional
- ✅ Método `readOne()`: Lee todos los campos del perfil completo

### 2. AuthController (api/controllers/AuthController.php)
**Optimizaciones:**
- ✅ `register()`: 
  - Copia imagen por género desde `media/` (male.png, female.png, other.png) a `uploads/users/{ID}/profile.png`
  - Si se sube imagen personalizada, la guarda con extensión original: `profile.{jpg|png|webp|etc}`
  - Crea directorio por usuario: `uploads/users/{ID}/`
  - Retorna todos los campos del perfil en la respuesta
  
- ✅ `login()`:
  - Retorna todos los campos del perfil (first_name, last_name, gender, profile_img)
  
- ✅ `validateToken()`:
  - Retorna perfil completo del usuario
  
- ✅ `updateProfile()`:
  - Actualiza todos los campos del perfil
  - Maneja subida de nueva imagen
  - Elimina imagen anterior si se reemplaza
  
- ✅ `uploadProfileImage()`:
  - Valida tipo de archivo (jpg, jpeg, png, gif, webp)
  - Valida tamaño máximo (5MB)
  - Guarda con nombre fijo: `profile.{extension}`
  - Elimina imágenes antiguas con diferente extensión

### 3. UserController (api/controllers/UserController.php)
**Sin cambios necesarios** - Ya implementa correctamente los campos del perfil

## Cambios en el Frontend (JavaScript)

### 1. app.js
**Optimizaciones:**
- ✅ Singleton: Previene múltiples instancias de App
- ✅ `getUserFromStorage()`: Método seguro para leer usuario de localStorage
- ✅ `initializeUserMenu()`: Refactorizado para claridad
- ✅ `updateUserMenuAfterPageChange()`: Método dedicado para actualizar menú
- ✅ Mensajes de consola mejorados
- ✅ Manejo de errores más robusto

### 2. user-menu.js
**Optimizaciones:**
- ✅ `updateUser()`: Usa `first_name` o `username` como fallback
- ✅ `updateUser()`: Usa `profile_img` del backend o fallback a `/Nueva-WEB/media/default.jpg`
- ✅ `setupLogout()`: Simplificado, delega a `AuthService.logout()`
- ✅ Eliminado código duplicado

### 3. nav.js
**Optimizaciones:**
- ✅ Eliminados event listeners duplicados
- ✅ Código más limpio y mantenible

### 4. auth.js
**Sin cambios necesarios** - Ya implementa correctamente logout y validación

## Estructura de Archivos de Imágenes

### Carpeta `media/` (Imágenes por defecto)
```
media/
├── default.jpg    (Imagen genérica por defecto)
├── male.png       (Avatar por defecto para hombres)
├── female.png     (Avatar por defecto para mujeres)
└── other.png      (Avatar por defecto para género no especificado)
```

### Carpeta `uploads/users/` (Imágenes de usuarios)
```
uploads/users/
├── 1/
│   └── profile.png   (o .jpg, .webp, etc.)
├── 2/
│   └── profile.jpg
└── 3/
    └── profile.webp
```

## Flujo de Imágenes de Perfil

### 1. Registro de Usuario
- Si NO sube imagen: Se copia de `media/{gender}.png` a `uploads/users/{ID}/profile.png`
- Si SÍ sube imagen: Se guarda en `uploads/users/{ID}/profile.{extension_original}`

### 2. Actualización de Perfil
- Si sube nueva imagen: 
  - Se elimina la imagen anterior (si existe)
  - Se guarda la nueva con `profile.{nueva_extension}`

### 3. Visualización
- Backend retorna: `/Nueva-WEB/api/uploads/users/{ID}/profile.{ext}`
- Frontend usa esta ruta directamente
- Fallback: `/Nueva-WEB/media/default.jpg`

## Rutas de API Actualizadas

### POST /api/register
**Request:**
```json
{
  "username": "string",
  "email": "string",
  "password": "string",
  "first_name": "string (opcional)",
  "last_name": "string (opcional)",
  "gender": "male|female|other (opcional, default: other)"
}
```
**+ Archivo:** `profile_image` (opcional)

**Response:**
```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "user": {
    "id": 1,
    "username": "string",
    "email": "string",
    "first_name": "string",
    "last_name": "string",
    "gender": "string",
    "profile_img": "/Nueva-WEB/api/uploads/users/1/profile.png",
    "roles": ["user"]
  },
  "token": "jwt_token"
}
```

### POST /api/login
**Response incluye:**
```json
{
  "user": {
    "first_name": "string",
    "last_name": "string",
    "gender": "string",
    "profile_img": "string"
  }
}
```

### GET /api/auth/validate
**Response incluye todos los campos del perfil**

### PUT /api/auth/profile
**Request:** (Todos los campos opcionales)
```json
{
  "username": "string",
  "email": "string",
  "first_name": "string",
  "last_name": "string",
  "gender": "male|female|other",
  "password": "string (min 8 caracteres)"
}
```
**+ Archivo:** `profile_image` (opcional)

## Pasos para Implementar

### 1. Actualizar Base de Datos
```sql
-- Si es una base de datos nueva:
source database/newapp_schema.sql

-- Si es una base de datos existente:
source database/migrate_users.sql
```

### 2. Migrar Imágenes de Usuarios Existentes
```bash
# Desde línea de comandos:
cd c:\Server\html\Nueva-WEB\database
php migrate_profile_images.php

# O desde navegador:
http://localhost/Nueva-WEB/database/migrate_profile_images.php
```

### 3. Verificar Permisos de Carpetas
```bash
# Asegurar que uploads/ tiene permisos de escritura
chmod -R 777 uploads/  # Linux
# En Windows: Propiedades > Seguridad > Modificar permisos
```

## Checklist de Verificación

- [x] Schema SQL actualizado con nuevos campos
- [x] Modelo User actualizado
- [x] AuthController optimizado
- [x] UserController revisado
- [x] Frontend actualizado (user-menu.js, nav.js, app.js)
- [x] Scripts de migración creados
- [x] Estructura de carpetas documentada
- [x] Rutas de API documentadas

## Notas Importantes

1. **Seguridad:**
   - Contraseñas hasheadas con bcrypt (cost 12)
   - Validación de tipos de archivo en upload
   - Límite de 5MB para imágenes
   - Sanitización de inputs con htmlspecialchars

2. **Rendimiento:**
   - Singleton en app.js previene duplicaciones
   - Lazy loading de componentes
   - Retry logic con límites para evitar loops infinitos

3. **Mantenibilidad:**
   - Código modular y bien organizado
   - Comentarios descriptivos
   - Nombres de funciones claros
   - Separación de responsabilidades

4. **Extensibilidad:**
   - Fácil agregar más campos al perfil
   - Sistema de roles ya implementado
   - Estructura preparada para más features

## Próximos Pasos Sugeridos

1. Implementar página de perfil de usuario con edición
2. Agregar validación de formularios en frontend
3. Implementar sistema de notificaciones
4. Agregar más campos opcionales al perfil (teléfono, dirección, etc.)
5. Implementar preview de imagen antes de subir
6. Agregar recorte/redimensionamiento de imágenes
