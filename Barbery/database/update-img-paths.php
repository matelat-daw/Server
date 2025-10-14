<?php
// Script para actualizar las rutas de imágenes en la tabla service
include "includes/conn.php";

try {
    // Actualizar todas las rutas que empiecen con 'img/' a 'assets/img/'
    $sql = "UPDATE service SET img = REPLACE(img, 'img/', 'assets/img/') WHERE img LIKE 'img/%'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    echo "✅ Rutas de imágenes actualizadas correctamente\n\n";
    
    // Mostrar los resultados
    $sql = "SELECT id, service, img FROM service";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    echo "Servicios actualizados:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s | %-30s | %s\n", "ID", "Servicio", "Imagen");
    echo str_repeat("-", 80) . "\n";
    
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        printf("%-5s | %-30s | %s\n", $row->id, $row->service, $row->img);
    }
    
    echo "\n✅ Actualización completada con éxito!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

$conn = null;
?>
