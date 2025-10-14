<?php
/**
 * Helper Script - Actualizar Includes en Archivos
 * Este script ayuda a actualizar las rutas de includes en un archivo
 */

function updateIncludes($filePath, $depth) {
    if (!file_exists($filePath)) {
        echo "❌ Archivo no encontrado: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    $prefix = str_repeat('../', $depth);
    
    // Patrones a buscar y reemplazar
    $replacements = [
        // Includes básicos
        'include "includes/' => 'include "' . $prefix . 'includes/',
        "include 'includes/" => "include '" . $prefix . "includes/",
        'require "includes/' => 'require "' . $prefix . 'includes/',
        "require 'includes/" => "require '" . $prefix . "includes/",
        'include_once "includes/' => 'include_once "' . $prefix . 'includes/',
        "include_once 'includes/" => "include_once '" . $prefix . "includes/",
        'require_once "includes/' => 'require_once "' . $prefix . 'includes/',
        "require_once 'includes/" => "require_once '" . $prefix . "includes/",
        
        // Assets en HTML/PHP
        'href="css/' => 'href="' . $prefix . 'assets/css/',
        'src="js/' => 'src="' . $prefix . 'assets/js/',
        'src="img/' => 'src="' . $prefix . 'assets/img/',
    ];
    
    $updated = $content;
    $changes = 0;
    
    foreach ($replacements as $search => $replace) {
        $count = 0;
        $updated = str_replace($search, $replace, $updated, $count);
        $changes += $count;
    }
    
    if ($changes > 0) {
        // Crear backup
        $backupPath = $filePath . '.backup';
        file_put_contents($backupPath, $content);
        
        // Guardar archivo actualizado
        file_put_contents($filePath, $updated);
        
        echo "✅ Archivo actualizado: $filePath ($changes cambios)\n";
        echo "   📦 Backup creado: $backupPath\n";
        return true;
    } else {
        echo "ℹ️  No se necesitan cambios en: $filePath\n";
        return true;
    }
}

function analyzeFile($filePath) {
    if (!file_exists($filePath)) {
        echo "❌ Archivo no encontrado: $filePath\n";
        return;
    }
    
    $content = file_get_contents($filePath);
    
    echo "\n📊 Análisis de: " . basename($filePath) . "\n";
    echo str_repeat("-", 50) . "\n";
    
    // Buscar includes
    preg_match_all('/(include|require|include_once|require_once)\s+["\']([^"\']+)["\']/', $content, $matches);
    if (!empty($matches[2])) {
        echo "📁 Includes encontrados:\n";
        foreach (array_unique($matches[2]) as $include) {
            echo "   - $include\n";
        }
    }
    
    // Buscar actions en formularios
    preg_match_all('/action\s*=\s*["\']([^"\']+)["\']/', $content, $actions);
    if (!empty($actions[1])) {
        echo "\n📝 Form actions encontrados:\n";
        foreach (array_unique($actions[1]) as $action) {
            echo "   - $action\n";
        }
    }
    
    // Buscar header redirects
    preg_match_all('/header\s*\(\s*["\']Location:\s*([^"\']+)["\']/', $content, $redirects);
    if (!empty($redirects[1])) {
        echo "\n🔀 Redirecciones encontradas:\n";
        foreach (array_unique($redirects[1]) as $redirect) {
            echo "   - $redirect\n";
        }
    }
    
    // Buscar window.location en JS
    preg_match_all('/window\.location(?:\.href)?\s*=\s*["\']([^"\']+)["\']/', $content, $jsRedirects);
    if (!empty($jsRedirects[1])) {
        echo "\n🔀 Redirecciones JS encontradas:\n";
        foreach (array_unique($jsRedirects[1]) as $redirect) {
            echo "   - $redirect\n";
        }
    }
    
    echo "\n";
}

// Modo de uso
if (php_sapi_name() !== 'cli') {
    echo "⚠️  Este script debe ejecutarse desde línea de comandos\n";
    echo "Uso: php update-includes.php <comando> <archivo> [profundidad]\n\n";
    echo "Comandos:\n";
    echo "  analyze <archivo>           - Analizar un archivo\n";
    echo "  update <archivo> <depth>    - Actualizar includes (depth = niveles de profundidad)\n";
    echo "\nEjemplos:\n";
    echo "  php update-includes.php analyze profile.php\n";
    echo "  php update-includes.php update app/client/profile.php 2\n";
    exit(1);
}

// Procesar argumentos
$command = $argv[1] ?? null;
$file = $argv[2] ?? null;
$depth = intval($argv[3] ?? 1);

if (!$command || !$file) {
    echo "❌ Faltan argumentos\n\n";
    echo "Uso:\n";
    echo "  php update-includes.php analyze <archivo>\n";
    echo "  php update-includes.php update <archivo> <depth>\n";
    exit(1);
}

switch ($command) {
    case 'analyze':
        analyzeFile($file);
        break;
        
    case 'update':
        echo "🔄 Actualizando includes con profundidad: $depth\n\n";
        updateIncludes($file, $depth);
        break;
        
    default:
        echo "❌ Comando desconocido: $command\n";
        echo "Comandos válidos: analyze, update\n";
        exit(1);
}

?>
