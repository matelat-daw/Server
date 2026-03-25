# Actualización de Base de Datos - Campos Gender y Profile Picture

## Resumen de Cambios

Se han agregado dos nuevos campos al modelo de usuario (`User.java`) y al seeding automático de datos:

1. **gender**: Campo de texto (VARCHAR) para almacenar el género del usuario
   - Valores posibles: `female`, `male`, `other`
   - Valor por defecto: `female`

2. **profile_picture**: Ruta a la imagen de perfil del usuario
   - Tipo: VARCHAR(255)
   - Valor por defecto: NULL (se asigna según el género)

## Archivos Modificados

### 1. DataSeeder.java
**Ubicación**: `src/main/java/com/futureprograms/MyIkea/Seeders/DataSeeder.java`

Se actualizaron los 4 usuarios de demostración para incluir género e imagen de perfil:
- **user**: usuario genérico (female) → `/images/female.png`
- **manager**: gestor (male) → `/images/male.png`  
- **admin1**: administrador (male) → `/images/male.png`
- **admin2**: administrador (other) → `/images/other.png`

### 2. User.java (Ya actualizado en mensaje anterior)
**Ubicación**: `src/main/java/com/futureprograms/MyIkea/Models/Auth/User.java`

Ya contiene ambos campos:
```java
@Column(name = "profile_picture")
private String profilePicture;

@Column(name = "gender", length = 20)
private String gender = "female"; // Por defecto: female
```

## Opciones de Migración

### Opción 1: Nueva Instalación (Recomendado)
Si es una instalación nueva, la tabla `users` se creará automáticamente al iniciar la aplicación gracias a Spring Data JPA, y el `DataSeeder` poblará los usuarios con los campos nuevos.

**Pasos:**
1. Eliminar la base de datos anterior (si existe)
2. Ejecutar la aplicación Spring Boot
3. La tabla se creará automáticamente con los campos nuevos
4. Los usuarios de demostración se crearán con género e imágenes

### Opción 2: Migración de Base de Datos Existente
Si ya tienes datos en tu base de datos, ejecuta uno de estos scripts:

#### Usando Flyway (Automático)
El archivo `src/main/resources/db/migration/V1__add_gender_and_profile_picture.sql` se ejecutará automáticamente al iniciar la aplicación si [Flyway](https://flywaydb.org/) está configurado.

#### Ejecutar Manualmente en MySQL
```bash
# Conectarte a tu base de datos
mysql -u root -p myikea < database/migrate_users_to_v1.sql
```

O ejecuta directamente en MySQL Workbench o phpMyAdmin:
```sql
-- Agregar columna gender
ALTER TABLE users 
ADD COLUMN gender VARCHAR(20) DEFAULT 'female' AFTER password;

-- Agregar columna profile_picture  
ALTER TABLE users 
ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER gender;

-- Actualizar usuarios existentes
UPDATE users SET profile_picture = '/images/female.png' WHERE gender = 'female';
UPDATE users SET profile_picture = '/images/male.png' WHERE gender = 'male';
UPDATE users SET profile_picture = '/images/other.png' WHERE gender = 'other';
```

## Configuración de Imágenes de Perfil

Las imágenes por defecto deben estar en: `src/main/resources/static/images/`

Archivos requeridos:
- `female.png` - Imagen para usuarias
- `male.png` - Imagen para usuarios  
- `other.png` - Imagen para otro género
- `default.jpg` - Imagen genérica de fallback

## Validación

Después de aplicar los cambios, verifica en la base de datos:

```sql
-- Ver la estructura de la tabla
DESCRIBE users;

-- Ver los usuarios creados
SELECT id, username, email, gender, profile_picture FROM users;
```

Debería mostrar algo como:
```
| id | username | email               | gender | profile_picture    |
|----|----------|---------------------|--------|-------------------|
| 1  | user     | user@myikea.com     | female | /images/female.png |
| 2  | manager  | manager@myikea.com  | male   | /images/male.png   |
| 3  | admin1   | admin1@myikea.com   | male   | /images/male.png   |
| 4  | admin2   | admin2@myikea.com   | other  | /images/other.png  |
```

## Próximos Pasos

Si deseas:
1. **Permitir cambio de imagen de perfil**: Crear endpoint en ProfileController
2. **Mostrar género en perfil**: Actualizar plantilla profile.html
3. **Migración de usuarios existentes**: Actualizar los perfiles antiguos con género
