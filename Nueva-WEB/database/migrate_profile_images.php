<?php
/**
 * Script de migración para copiar imágenes de perfil por defecto a usuarios existentes
 * Ejecutar desde la línea de comandos: php migrate_profile_images.php
 * O desde el navegador accediendo a: /Nueva-WEB/database/migrate_profile_images.php
 */

require_once __DIR__ . '/../api/config/database.php';

// Función para copiar imagen según género del usuario
function copyDefaultProfileImage($userId, $gender = 'other') {
    // Determinar imagen por género
    $avatarFile = 'other.png';
    if ($gender === 'male') {
        $avatarFile = 'male.png';
    } elseif ($gender === 'female') {
        $avatarFile = 'female.png';
    }
    
    // Rutas
    $srcAvatar = __DIR__ . '/../media/' . $avatarFile;
    $userDir = __DIR__ . '/../api/uploads/users/' . $userId . '/';
    $destAvatar = $userDir . 'profile.png';
    
    // Crear directorio si no existe
    if (!file_exists($userDir)) {
        mkdir($userDir, 0777, true);
    }
    
    // Copiar imagen si no existe ya
    if (file_exists($srcAvatar) && !file_exists($destAvatar)) {
        if (copy($srcAvatar, $destAvatar)) {
            return true;
        }
    }
    
    return false;
}

try {
    echo "=== Iniciando migración de imágenes de perfil ===\n\n";
    
    // Obtener todos los usuarios
    $query = "SELECT id, username, gender, profile_img FROM users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $usersProcessed = 0;
    $imagesCopied = 0;
    $errors = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userId = $row['id'];
        $username = $row['username'];
        $gender = $row['gender'] ?? 'other';
        $profileImg = $row['profile_img'];
        
        echo "Procesando usuario #{$userId} ({$username})... ";
        
        // Verificar si ya tiene imagen
        $userImagePath = __DIR__ . '/../api/uploads/users/' . $userId . '/profile.png';
        
        if (file_exists($userImagePath)) {
            echo "Ya tiene imagen de perfil\n";
            $usersProcessed++;
            continue;
        }
        
        // Copiar imagen por defecto según género
        if (copyDefaultProfileImage($userId, $gender)) {
            echo "Imagen copiada exitosamente\n";
            $imagesCopied++;
            
            // Actualizar base de datos
            $updateQuery = "UPDATE users SET profile_img = :profile_img WHERE id = :id";
            $updateStmt = $conn->prepare($updateQuery);
            $profileImgPath = 'users/' . $userId . '/profile.png';
            $updateStmt->bindParam(':profile_img', $profileImgPath);
            $updateStmt->bindParam(':id', $userId);
            $updateStmt->execute();
        } else {
            echo "Error al copiar imagen\n";
            $errors++;
        }
        
        $usersProcessed++;
    }
    
    echo "\n=== Migración completada ===\n";
    echo "Usuarios procesados: {$usersProcessed}\n";
    echo "Imágenes copiadas: {$imagesCopied}\n";
    echo "Errores: {$errors}\n";
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
