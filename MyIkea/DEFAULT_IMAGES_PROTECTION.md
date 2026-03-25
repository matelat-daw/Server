# Protección de Imágenes por Defecto - Explicación

## Problema que se presentaba

Cuando un usuario actualizaba su foto de perfil, la aplicación intentaba **eliminar su foto anterior del disco**. Si esa foto anterior era una de las imágenes por defecto (`female.png`, `male.png`, `other.png`), ¡se eliminaba la imagen por defecto del sistema!

### Ejemplo del problema:
1. Usuario "María" se registra → se le asigna `female.png` (imagen por defecto)
2. María sube una foto nueva → la app intenta borrar `female.png` 
3. **RESULTADO**: `female.png` se elimina del servidor ❌
4. Nuevos usuarios que se registren sin foto no tendrán imagen por defecto

## Solución implementada

Se agregó **protección en el método `deleteImage()`** del `FileUploadService.java` para que NUNCA elimine:
- ✅ `female.png` - Imagen por defecto para usuarias
- ✅ `male.png` - Imagen por defecto para usuarios  
- ✅ `other.png` - Imagen por defecto para otro género
- ✅ `default.jpg` - Imagen genérica de fallback

Ahora si se intenta eliminar una de estas imágenes, el sistema:
1. **Detecta** que es una imagen protegida
2. **Registra** un aviso en los logs: "⚠️ Intento de eliminar imagen protegida"
3. **No la elimina** del disco
4. **No causa error** - continúa normalmente

## Código de protección

En `FileUploadService.java`, el método `deleteImage()` ahora incluye:

```java
// Proteger las imágenes por defecto - NUNCA ELIMINARLAS
String[] protectedImages = {"female.png", "male.png", "other.png", "default.jpg"};
for (String protectedImage : protectedImages) {
    if (fileName.equalsIgnoreCase(protectedImage)) {
        // No eliminar
        return false;
    }
}
```

## Ubicación de las imágenes

Todas las imágenes **deben estar** en:
```
src/main/resources/static/images/
```

Y esta carpeta **NO se borra** con:
- `mvn clean` - solo limpia la carpeta `target/`
- `mvn clean install` - regenera `target/` pero NO toca `src/`
- Limpieza normal del proyecto

## ¿Qué sucede ahora?

### Actualización de foto de perfil:
1. Usuario sube nueva foto
2. El sistema intenta eliminar la foto anterior
3. Si es una imagen por defecto → **se ignora la eliminación** ✓
4. Si es una foto subida por el usuario → **se elimina** ✓
5. Nueva foto se guarda correctamente ✓

### Eliminación de cuenta:
1. Usuario elimina su cuenta
2. El sistema intenta eliminar todas sus fotos
3. Si tienen una foto por defecto → **se ignora** ✓
4. Imagen por defecto permanece disponible para otros usuarios ✓

## Verificación

Para confirmar que la solución funciona:

1. **Look at logs**: Al actualizar foto, verifica que NO aparezca mensaje de error
2. **Check file system**: 
   - Sube una foto (ej: mioto.jpg)
   - Sube otra foto 
   - Verifica que miota.jpg desaparezca pero female.png sigue ahí ✓
3. **Test new registrations**: 
   - Crea usuario nuevo sin foto
   - Verifica que aparezca imagen por defecto ✓

## Changelog

**Versión Actual:**
- ✅ Imágenes por defecto protegidas
- ✅ No se eliminan al actualizar foto de perfil
- ✅ No se eliminan al borrar cuenta de usuario
- ✅ Se registran intentos de eliminación en logs

**Nota**: Si necesitabas eliminar una imagen por defecto por algún motivo, deberás:
1. Ir directamente a la carpeta del disco
2. Eliminar el archivo manualmente
3. O contactar al administrador
