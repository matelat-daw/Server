# Implementación: Página de Perfil de Usuario

**Fecha**: Marzo 2026  
**Estado**: ✅ Completado y compilado exitosamente

## Resumen

Se ha implementado un sistema completo de gestión de perfil de usuario que permite a los usuarios:
- ✅ Ver y editar sus datos personales (nombre, apellido, email, teléfono)
- ✅ Cambiar su contraseña con validación de contraseña actual
- ✅ Subir y cambiar su foto de perfil
- ✅ Eliminar su propia cuenta permanentemente

## Cambios Realizados

### 1. **Entidad User** (`Models/Auth/User.java`)

Se agregaron nuevos campos a la entidad JPA:
```java
private String firstName;           // Nombre del usuario
private String lastName;            // Apellido del usuario
private String phoneNumber;         // Teléfono de contacto
private String profilePicture;      // Nombre de archivo de la foto de perfil
private LocalDateTime createdAt;    // Fecha de creación del usuario
```

Estos campos se generarán automáticamente en la tabla `users` mediante Hibernate DDL (`spring.jpa.hibernate.ddl-auto=update`).

### 2. **UserService** (`Services/auth/UserService.java`)

Se agregaron nuevos métodos para la gestión de usuarios:
```java
public User findById(Integer id)                 // Buscar usuario por ID
public User findByEmailIfExists(String email)  // Buscar email sin lanzar excepción
public void update(User user)                   // Actualizar datos de usuario
public void updatePassword(Integer userId, ...)  // Cambiar contraseña con encoding
```

### 3. **ProfileController** (NUEVO: `Controllers/ProfileController.java`)

Controlador que maneja todas las operaciones del perfil:

**Rutas implementadas:**
- `GET /profile` - Mostrar página de perfil
- `POST /profile/update` - Actualizar datos personales
- `POST /profile/update-password` - Cambiar contraseña
- `POST /profile/update-picture` - Subir/cambiar foto de perfil
- `POST /profile/delete` - Eliminar cuenta del usuario

**Funcionalidades:**
- Autenticación requerida en todas las rutas
- Validación de email único (no permitir emails duplicados)
- Validación de contraseña actual antes de cambiar
- Validación de coincidencia de contraseñas
- Eliminación segura de foto anterior al subir nueva
- Confirmación con contraseña para eliminar cuenta
- Mensajes de error/éxito con RedirectAttributes

### 4. **Vista de Perfil** (NUEVA: `templates/perfil/profile.html`)

Página HTML responsiva con Bootstrap 5 que incluye:

**Secciones:**
1. **Panel lateral** - Foto de perfil actual con opción de cambiar
2. **Datos Personales** - Formulario para editar nombre, apellido, email, teléfono
3. **Seguridad** - Cambio de contraseña con validación
4. **Zona de Peligro** - Eliminación de cuenta con modal de confirmación

**Características:**
- Diseño responsivo (mobile-first)
- Foto de perfil con fallback a imagen placeholder
- Mensajes de éxito/error contextуales
- Modal de confirmación para rechazo accidental
- Fecha de creación del usuario
- Muestra roles del usuario
- Validación del lado del cliente

### 5. **Actualización de Navegación** (`templates/fragments/nav.html`)

Se agregó enlace a perfil en la barra de navegación:
```html
<li th:if="${LOGGED}" class="nav-item">
    <a href="/profile" class="nav-link text-light">
        <i class="fas fa-cog"></i> Mi Perfil
    </a>
</li>
```

Visible solo para usuarios autenticados.

### 6. **SecurityConfig** (`Security/SecurityConfig.java`)

Se agregó configuración explícita de seguridad:
```java
.requestMatchers("/profile/**").hasAnyRole("USER", "MANAGER", "ADMIN")
```

Esto garantiza que solo usuarios autenticados puedan acceder a las rutas de perfil.

## Flujo de Operaciones

### Cambiar Datos Personales
1. Usuario accede a `/profile`
2. Completa el formulario de datos personales
3. POST a `/profile/update`
4. Validación de email único en servidor
5. Actualización en BD y mensaje de éxito

### Cambiar Contraseña
1. Usuario ingresa contraseña actual + nueva contraseña
2. POST a `/profile/update-password`
3. Validación de contraseña actual (comparar con BCrypt)
4. Validación de coincidencia de nuevas contraseñas
5. Encoding de nueva contraseña con BCrypt
6. Actualización en BD

### Cambiar Foto de Perfil
1. Usuario hace clic en "Cambiar foto"
2. Selecciona archivo de imagen
3. JavaScript muestra botón de confirmar con nombre de archivo
4. POST a `/profile/update-picture` con MultipartFile
5. FileUploadService valida y guarda imagen en `static/images/`
6. Foto anterior es eliminada automáticamente
7. Nuevo nombre de archivo guardado en BD

### Eliminar Cuenta
1. Usuario hace clic en "Eliminar mi cuenta"
2. Se abre modal de confirmación con advertencias
3. Ingresa contraseña para confirmar
4. POST a `/profile/delete`
5. Validación de contraseña
6. Foto de perfil eliminada del servidor
7. Usuario eliminado de BD
8. Redirección a `/logout`

## Base de Datos

**Tabla `users` (generada automáticamente):**
```sql
ALTER TABLE `users` ADD COLUMN `first_name` VARCHAR(255);
ALTER TABLE `users` ADD COLUMN `last_name` VARCHAR(255);
ALTER TABLE `users` ADD COLUMN `phone_number` VARCHAR(20);
ALTER TABLE `users` ADD COLUMN `profile_picture` VARCHAR(255);
ALTER TABLE `users` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

Los cambios se aplicarán automáticamente en el siguiente reinicio de la aplicación.

## Validaciones Implementadas

### En Servidor (ProfileController)
- ✅ Usuario debe estar autenticado
- ✅ Email debe ser único (no usado por otro usuario)
- ✅ Contraseña actual debe ser correcta antes de cambiar
- ✅ Nuevas contraseñas deben coincidir
- ✅ Contraseña mínimo 6 caracteres
- ✅ Email debe tener formato válido
- ✅ Confirmación con contraseña para eliminar cuenta

### En Cliente (JavaScript)
- ✅ Validación de campos requeridos
- ✅ Formato de email
- ✅ Cambio de botón según selección de archivo
- ✅ Modal de confirmación para eliminación

## Integración con Servicios Existentes

### FileUploadService
- Se reutiliza para guardar fotos de perfil en `static/images/`
- Se reutiliza para eliminar fotos antiguas
- Valida tamaño máximo (5MB) y extensiones permitidas

### UserService
- Se extiende con métodos de búsqueda y actualización
- Se integra con PasswordEncoder para codificación BCrypt

### Spring Security
- Autenticación requerida en `/profile/**`
- Usuario obtiene info actual vía `Authentication` principal
- Control de roles (USER, MANAGER, ADMIN)

## Archivos Modificados/Creados

```
CREADOS:
- src/main/java/.../Controllers/ProfileController.java
- src/main/resources/templates/perfil/profile.html

MODIFICADOS:
- src/main/java/.../Models/Auth/User.java (agregados 5 campos)
- src/main/java/.../Services/auth/UserService.java (4 nuevos métodos)
- src/main/java/.../Security/SecurityConfig.java (1 nueva regla)
- src/main/resources/templates/fragments/nav.html (1 nuevo enlace)

TOTAL: 2 archivos nuevos + 4 archivos modificados
```

## Testing Manual

1. **Crear cuenta**: `/register`
2. **Login**: `/login`
3. **Acceder a perfil**: `/profile`
4. **Actualizar datos**: Cambiar nombre, email, teléfono
5. **Cambiar contraseña**: Introducir contraseña actual y nueva
6. **Cambiar foto**: Seleccionar imagen (JPG/PNG/GIF/WEBP)
7. **Verificar cambios**: Los cambios aparecen inmediatamente
8. **Eliminar cuenta**: Hacer clic en "Eliminar mi cuenta" y confirmar

## Notas de Desarrollo

- El sitio usa Bootstrap 5.3.3 para estilos
- FontAwesome para iconos
- Thymeleaf para templates
- Spring Security con BCrypt para contraseñas

## Próximas Mejoras Sugeridas

- [ ] Autenticación de dos factores (2FA)
- [ ] Historial de cambios en la cuenta
- [ ] Restricciones de cambio frecuente de email
- [ ] Recuperación de cuenta eliminada (período de gracia)
- [ ] Validación de foto de mejor calidad
- [ ] Resizing automático de fotos
- [ ] Integración con LDAP/OAuth
- [ ] Preferencias de privacidad del usuario
- [ ] Auditoría de cambios en la cuenta

---

**Compilación**: ✅ Exitosa sin errores de compilación  
**Estado**: 🟢 Listo para producción
