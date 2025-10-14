<?php
/**
 * Archivo de Configuración de Rutas
 * Define las rutas base para todo el proyecto
 */

// Definir la ruta raíz del proyecto
define('ROOT_PATH', dirname(__FILE__) . '/');
define('BASE_URL', 'http://localhost/Barbery/');

// Rutas de directorios
define('APP_PATH', ROOT_PATH . 'app/');
define('API_PATH', ROOT_PATH . 'api/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('VENDOR_PATH', ROOT_PATH . 'vendor/');

// URLs de assets
define('CSS_URL', BASE_URL . 'assets/css/');
define('JS_URL', BASE_URL . 'assets/js/');
define('IMG_URL', BASE_URL . 'assets/img/');

// Rutas específicas
define('AUTH_PATH', APP_PATH . 'auth/');
define('CLIENT_PATH', APP_PATH . 'client/');
define('ADMIN_PATH', APP_PATH . 'admin/');

/**
 * Función helper para incluir archivos de forma segura
 * @param string $file Ruta relativa al archivo
 * @param string $type Tipo: 'config', 'include', 'api'
 */
function require_file($file, $type = 'include') {
    $paths = [
        'config' => CONFIG_PATH,
        'include' => INCLUDES_PATH,
        'api' => API_PATH,
        'vendor' => VENDOR_PATH
    ];
    
    $fullPath = $paths[$type] . $file;
    
    if (file_exists($fullPath)) {
        require_once $fullPath;
    } else {
        die("Error: No se encontró el archivo $fullPath");
    }
}

/**
 * Función para redireccionar a una ruta
 * @param string $route Ruta relativa desde BASE_URL
 */
function redirect($route) {
    header('Location: ' . BASE_URL . $route);
    exit;
}

/**
 * Función para obtener la URL de un asset
 * @param string $path Ruta del asset (ej: 'css/style.css')
 */
function asset($path) {
    return BASE_URL . 'assets/' . $path;
}

/**
 * Calcular la profundidad de nivel para los includes
 * @param string $currentFile Archivo actual
 * @return string Prefijo de ruta (../, ../../, etc.)
 */
function get_include_prefix($currentFile = null) {
    if (!$currentFile) {
        $currentFile = $_SERVER['SCRIPT_FILENAME'];
    }
    
    $rootPath = realpath(ROOT_PATH);
    $filePath = realpath(dirname($currentFile));
    
    $relativePath = str_replace($rootPath, '', $filePath);
    $depth = substr_count($relativePath, DIRECTORY_SEPARATOR);
    
    return str_repeat('../', $depth);
}

?>
