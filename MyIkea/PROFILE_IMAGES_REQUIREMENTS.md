# Requisitos de Imágenes de Perfil - MyIkea

## Ubicación
Todas las imágenes de perfil por defecto deben estar en:
```
src/main/resources/static/images/
```

## Imágenes Requeridas

Las siguientes imágenes DEBEN estar presentes en la carpeta `static/images/`:

### 1. **female.png**
- Imagen de perfil por defecto para usuarias (género: female)
- Formato: PNG
- Tamaño recomendado: 150x150 px o mayor (se escala automáticamente)
- Se usa cuando:
  - Una usuaria se registra sin subir imagen de perfil
  - El género seleccionado es "Mujer"

### 2. **male.png**
- Imagen de perfil por defecto para usuarios (género: male)
- Formato: PNG
- Tamaño recomendado: 150x150 px o mayor
- Se usa cuando:
  - Un usuario se registra sin subir imagen de perfil
  - El género seleccionado es "Varón"

### 3. **other.png**
- Imagen de perfil por defecto para otro género
- Formato: PNG
- Tamaño recomendado: 150x150 px o mayor
- Se usa cuando:
  - Un usuario se registra sin subir imagen de perfil
  - El género seleccionado es "Otro"

### 4. **default.jpg** (Opcional)
- Imagen genérica de fallback
- Se usa si no se encuentra ninguna imagen para el usuario

## Estructura de Carpetas

```
MyIkea/
├── src/
│   └── main/
│       └── resources/
│           └── static/
│               └── images/              ← AQUÍ VAN LAS IMÁGENES
│                   ├── female.png       ✓ REQUERIDO
│                   ├── male.png         ✓ REQUERIDO
│                   ├── other.png        ✓ REQUERIDO
│                   ├── default.jpg      (Opcional)
│                   └── ... otras imágenes ...
```

## Cómo se usa en el código

### En el Controlador de Registro (AuthController)
Cuando un usuario se registra sin subir imagen de perfil, se llama a:
```java
getDefaultProfilePicture(String gender)
```

Este método devuelve:
- `female.png` si gender = "female"
- `male.png` si gender = "male"
- `other.png` si gender = "other" o cualquier otro valor

### En la Plantilla (profile.html)
En la pantalla de perfil, la imagen se muestra con:
```html
<img th:src="${user.profilePicture?.startsWith('/') ? user.profilePicture : '/images/' + user.profilePicture}" ... >
```

En la base de datos se almacena solo el nombre del archivo (ej: "female.png"), no la ruta completa.

## En la Base de Datos

La columna `profile_picture` de la tabla `users` almacena:
- Solo el nombre del archivo: `female.png`, `male.png`, etc.
- **NO** la ruta completa como `/images/female.png`
- **Vacío (NULL)** si el usuario subió su propia imagen y fue eliminada

## Requisitos Técnicos

- **Formato**: PNG recomendado (también soporta JPG, GIF, WebP)
- **Colores**: RGB o RGBA (para fondo transparente si lo deseas)
- **Tamaño mínimo**: 100x100 px
- **Tamaño máximo**: No hay límite, pero se recomienda máximo 1MB cada una
- **Nombre**: Exactamente como se especifica arriba (sensible a mayúsculas/minúsculas en sistemas Linux)

## Verificación

Para verificar que todo está correcto:

1. **Carpeta**: Abre `src/main/resources/static/images/`
2. **Archivos**: Confirma que existen `female.png`, `male.png`, `other.png`
3. **Prueba**: Crea un usuario nuevo sin subir imagen y verifica que aparezca la imagen por defecto

## Troubleshooting

Si las imágenes no se muestran:

1. **Verifica la ruta**: Las carpetas son sensibles a mayúsculas/minúsculas en Linux
   - ❌ NEVER: `/Images/female.png` o `/image/female.png`
   - ✅ CORRECT: `/images/female.png`

2. **Limpia el caché del navegador**: 
   - Presiona `Ctrl+Shift+Del` (o Cmd+Shift+Del en Mac)
   - Selecciona "Cookies y otros datos del sitio" y "Imágenes en caché"

3. **Reinicia la aplicación Spring Boot**

4. **Verifica en la BD**: 
   ```sql
   SELECT * FROM users WHERE profile_picture IS NOT NULL;
   ```
   Verifica que el valor sea solo el nombre del archivo (ej: "female.png")

## Script SQL para Limpiar Rutas (si fue necesario)

Si accidentalmente se guardaron rutas completas en la BD:

```sql
-- Reemplaza /images/filename con solo filename
UPDATE users 
SET profile_picture = SUBSTRING(profile_picture, 9) 
WHERE profile_picture LIKE '/images/%';

-- Verificación
SELECT id, username, profile_picture FROM users;
```

## Notas Importantes

- Las imágenes son **estáticas** y se sirven desde Spring Boot
- Se cachean en el navegador, por eso a veces hay que limpiar el caché
- Son **obligatorias** para que el registro de usuarios sin imagen suba correctamente
- Si un usuario sube su propia imagen, se almacena en `src/main/resources/static/images/` con un nombre único generado
