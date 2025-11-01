# 🎨 Guía Visual - Sistema de Imágenes de Perfil

## 📸 Flujo de Imágenes

### 1️⃣ Registro de Usuario

```
┌─────────────────────────────────────────────────────────────┐
│                    USUARIO SE REGISTRA                      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                   ┌──────────────────────┐
                   │  ¿Sube imagen?       │
                   └──────────────────────┘
                    │                    │
              SÍ    │                    │ NO
                    ▼                    ▼
    ┌──────────────────────┐   ┌─────────────────────────┐
    │ Guardar imagen       │   │ Copiar avatar según     │
    │ personalizada en:    │   │ género desde media/:    │
    │                      │   │                         │
    │ uploads/users/{ID}/  │   │ male.png → Hombre       │
    │ profile.{ext}        │   │ female.png → Mujer      │
    └──────────────────────┘   │ other.png → Otro        │
                               └─────────────────────────┘
                    │                    │
                    └──────────┬─────────┘
                              ▼
                ┌────────────────────────────┐
                │ Actualizar DB:             │
                │ profile_img =              │
                │ 'users/{ID}/profile.{ext}' │
                └────────────────────────────┘
```

### 2️⃣ Actualización de Perfil

```
┌─────────────────────────────────────────────────────────────┐
│              USUARIO ACTUALIZA PERFIL                       │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                   ┌──────────────────────┐
                   │ ¿Sube nueva imagen?  │
                   └──────────────────────┘
                    │                    │
              SÍ    │                    │ NO
                    ▼                    ▼
    ┌──────────────────────┐   ┌─────────────────────────┐
    │ 1. Eliminar imagen   │   │ Mantener imagen actual  │
    │    anterior          │   │                         │
    │ 2. Guardar nueva     │   │ Solo actualizar otros   │
    │    imagen            │   │ campos del perfil       │
    │ 3. Actualizar DB     │   │                         │
    └──────────────────────┘   └─────────────────────────┘
```

### 3️⃣ Visualización en Frontend

```
┌─────────────────────────────────────────────────────────────┐
│                  USUARIO HACE LOGIN                         │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                ┌────────────────────────────┐
                │ Backend retorna:           │
                │ {                          │
                │   profile_img:             │
                │   "/Nueva-WEB/api/uploads/ │
                │   users/{ID}/profile.png"  │
                │ }                          │
                └────────────────────────────┘
                              │
                              ▼
                ┌────────────────────────────┐
                │ Frontend (user-menu.js):   │
                │ - Usa profile_img si existe│
                │ - Fallback: default.jpg    │
                │ - Muestra en menú usuario  │
                └────────────────────────────┘
                              │
                              ▼
                ┌────────────────────────────┐
                │ 🖼️ Imagen visible en el   │
                │    menú de usuario         │
                └────────────────────────────┘
```

## 📂 Estructura de Carpetas

```
c:/Server/html/Nueva-WEB/
│
├── 📁 media/                        ← Imágenes por defecto
│   ├── 🖼️ default.jpg              (Imagen genérica)
│   ├── 👨 male.png                 (Avatar masculino)
│   ├── 👩 female.png               (Avatar femenino)
│   └── 👤 other.png                (Avatar neutral)
│
├── 📁 api/
│   └── 📁 uploads/
│       └── 📁 users/                ← Imágenes de usuarios
│           ├── 📁 1/
│           │   └── 🖼️ profile.png
│           ├── 📁 2/
│           │   └── 🖼️ profile.jpg
│           └── 📁 3/
│               └── 🖼️ profile.webp
│
└── 📁 database/
    ├── 📄 newapp_schema.sql         (Schema completo)
    ├── 📄 migrate_users.sql         (Migración SQL)
    └── 📄 migrate_profile_images.php (Migración imágenes)
```

## 🔄 Flujo de Datos Completo

```
┌────────────────────────────────────────────────────────────────┐
│                        REGISTRO                                │
└────────────────────────────────────────────────────────────────┘

Cliente (Frontend)                 Servidor (Backend)             Base de Datos
      │                                  │                              │
      │  POST /api/register              │                              │
      │  + FormData con imagen           │                              │
      ├─────────────────────────────────▶│                              │
      │                                  │                              │
      │                                  │  1. Validar datos            │
      │                                  │  2. Hash password            │
      │                                  │  3. INSERT usuario           │
      │                                  ├─────────────────────────────▶│
      │                                  │                              │
      │                                  │◀─────────────────────────────┤
      │                                  │  ID del nuevo usuario        │
      │                                  │                              │
      │                                  │  4. Crear carpeta:           │
      │                                  │     uploads/users/{ID}/      │
      │                                  │                              │
      │                                  │  5. Copiar/Guardar imagen    │
      │                                  │     profile.{ext}            │
      │                                  │                              │
      │                                  │  6. UPDATE profile_img       │
      │                                  ├─────────────────────────────▶│
      │                                  │                              │
      │                                  │  7. Generar JWT              │
      │                                  │                              │
      │◀─────────────────────────────────┤                              │
      │  Response:                       │                              │
      │  {                               │                              │
      │    user: {...},                  │                              │
      │    token: "..."                  │                              │
      │  }                               │                              │
      │                                  │                              │
      │  8. Guardar en localStorage      │                              │
      │  9. Mostrar menú usuario         │                              │
      │     con imagen de perfil         │                              │
      │                                  │                              │

┌────────────────────────────────────────────────────────────────┐
│                           LOGIN                                │
└────────────────────────────────────────────────────────────────┘

Cliente (Frontend)                 Servidor (Backend)             Base de Datos
      │                                  │                              │
      │  POST /api/login                 │                              │
      ├─────────────────────────────────▶│                              │
      │                                  │                              │
      │                                  │  1. Verificar credenciales   │
      │                                  ├─────────────────────────────▶│
      │                                  │◀─────────────────────────────┤
      │                                  │  Datos del usuario           │
      │                                  │                              │
      │                                  │  2. Generar JWT              │
      │                                  │  3. Set cookie auth_token    │
      │                                  │                              │
      │◀─────────────────────────────────┤                              │
      │  Response:                       │                              │
      │  {                               │                              │
      │    user: {                       │                              │
      │      profile_img:                │                              │
      │      "/Nueva-WEB/api/uploads/    │                              │
      │       users/1/profile.png"       │                              │
      │    },                            │                              │
      │    token: "..."                  │                              │
      │  }                               │                              │
      │                                  │                              │
      │  4. Guardar en localStorage      │                              │
      │  5. Actualizar menú usuario      │                              │
      │     con imagen de perfil         │                              │
```

## 🎯 Puntos Clave

### ✅ Ventajas del Sistema

1. **Automático**: Asigna avatar según género si no se sube imagen
2. **Flexible**: Soporta múltiples formatos (jpg, png, webp, gif)
3. **Organizado**: Una carpeta por usuario
4. **Eficiente**: Solo guarda una imagen por usuario (reemplaza anterior)
5. **Seguro**: Validación de tipo y tamaño de archivo

### 🔒 Validaciones

- ✅ Tipo de archivo: `image/jpeg`, `image/jpg`, `image/png`, `image/gif`, `image/webp`
- ✅ Tamaño máximo: `5 MB`
- ✅ Extensión preservada: Mantiene la extensión original del archivo
- ✅ Nombre fijo: Siempre `profile.{extension}`

### 📊 Tabla de Género → Avatar

| Género    | Archivo Origen  | Color Sugerido |
|-----------|----------------|----------------|
| `male`    | `male.png`     | Azul 🔵       |
| `female`  | `female.png`   | Rosa 🟣       |
| `other`   | `other.png`    | Verde 🟢      |
| (no especificado) | `other.png` | Verde 🟢 |

## 🛠️ Comandos Útiles

### Ver imágenes de un usuario
```bash
dir c:\Server\html\Nueva-WEB\api\uploads\users\1\
```

### Listar todos los usuarios con imágenes
```sql
SELECT id, username, profile_img 
FROM users 
WHERE profile_img IS NOT NULL;
```

### Verificar permisos (PowerShell)
```powershell
icacls "c:\Server\html\Nueva-WEB\api\uploads\users"
```

### Limpiar imágenes de prueba
```bash
rmdir /s /q c:\Server\html\Nueva-WEB\api\uploads\users\
mkdir c:\Server\html\Nueva-WEB\api\uploads\users\
```

## 🧪 Tests de Verificación

### Test 1: Registro sin imagen (género male)
```javascript
fetch('/Nueva-WEB/api/register', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    username: 'juan',
    email: 'juan@test.com',
    password: 'password123',
    gender: 'male'
  })
});
```
**Resultado:** `users/1/profile.png` (copia de `media/male.png`)

### Test 2: Registro con imagen personalizada
```javascript
let formData = new FormData();
formData.append('username', 'maria');
formData.append('email', 'maria@test.com');
formData.append('password', 'password123');
formData.append('gender', 'female');
formData.append('profile_image', fileInput.files[0]);

fetch('/Nueva-WEB/api/register', {
  method: 'POST',
  body: formData
});
```
**Resultado:** `users/2/profile.jpg` (imagen personalizada)

## 📝 Notas Finales

- Las imágenes en `media/` son **solo plantillas**, nunca se modifican
- Cada usuario tiene su **propia carpeta** en `uploads/users/{ID}/`
- El nombre del archivo siempre es `profile.{extension}`
- Si un usuario sube una nueva imagen, **la anterior se elimina automáticamente**
- El sistema es **tolerante a fallos**: si falla la copia, el usuario se crea igual

---

**Sistema de imágenes funcionando correctamente! 🎉**
