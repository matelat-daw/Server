<?php
/**
 * Script de DEBUG - Verificar contenido de tabla tables
 * Este archivo es temporal para diagnóstico
 */
include "includes/conn.php";

echo "<!DOCTYPE html>";
echo "<html><head><title>Debug Tables</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head><body class='container mt-5'>";

echo "<h1>🔍 Debug: Contenido de tabla 'tables'</h1>";
echo "<hr>";

try {
    // Verificar si la tabla existe
    $stmt = $conn->prepare("SHOW TABLES LIKE 'tables'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<div class='alert alert-danger'>";
        echo "<h3>❌ ERROR: La tabla 'tables' NO EXISTE</h3>";
        echo "<p>La tabla aún se llama 'mesa'. Necesitas ejecutar el script SQL de refactorización.</p>";
        echo "</div>";
        
        // Intentar leer de 'mesa'
        echo "<h3>Intentando leer de tabla 'mesa' (antigua):</h3>";
        $stmt = $conn->prepare("SELECT * FROM mesa ORDER BY id ASC");
        $stmt->execute();
        $mesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($mesas) > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead class='table-dark'><tr><th>ID</th><th>Nombre</th></tr></thead>";
            echo "<tbody>";
            foreach ($mesas as $mesa) {
                echo "<tr>";
                echo "<td>" . $mesa['id'] . "</td>";
                echo "<td>" . htmlspecialchars($mesa['name']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        }
        
    } else {
        echo "<div class='alert alert-success'>";
        echo "<h3>✅ La tabla 'tables' EXISTE</h3>";
        echo "</div>";
        
        // Leer todas las mesas
        $stmt = $conn->prepare("SELECT * FROM tables ORDER BY id ASC");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($tables) > 0) {
            echo "<h3>Mesas encontradas: " . count($tables) . "</h3>";
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead class='table-primary'><tr><th>ID</th><th>Nombre</th><th>Longitud</th><th>Bytes</th></tr></thead>";
            echo "<tbody>";
            foreach ($tables as $table) {
                echo "<tr>";
                echo "<td>" . $table['id'] . "</td>";
                echo "<td><code>" . htmlspecialchars($table['name']) . "</code></td>";
                echo "<td>" . strlen($table['name']) . "</td>";
                echo "<td>" . bin2hex($table['name']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-warning'>";
            echo "<h3>⚠️ La tabla 'tables' está VACÍA</h3>";
            echo "<p>No hay mesas registradas en el sistema.</p>";
            echo "</div>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Información de conexión:</h3>";
    echo "<ul>";
    echo "<li>Base de datos: " . $conn->query("SELECT DATABASE()")->fetchColumn() . "</li>";
    echo "<li>Charset: " . $conn->query("SELECT @@character_set_database")->fetchColumn() . "</li>";
    echo "<li>Collation: " . $conn->query("SELECT @@collation_database")->fetchColumn() . "</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h3>❌ Error de base de datos:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<a href='index.html' class='btn btn-primary'>Volver al inicio</a>";
echo "</body></html>";
?>
