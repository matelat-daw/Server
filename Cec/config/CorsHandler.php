<?php
/**
 * CorsHandler - Manejo centralizado de CORS
 */
class CorsHandler {
    private static $allowedOrigins = [
        'https://nexus-astralis.vercel.app',
        'https://nexusastralis.duckdns.org'
    ];

    /**
     * Configura headers CORS para todas las respuestas
     */
    public static function setupCORS(): void {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        
        // Verificar condiciones para CORS
        $isNgrokOrigin = preg_match('/^https:\/\/[a-zA-Z0-9\-]+\.ngrok-free\.app$/', $origin);
        $isNgrokHost = strpos($host, '.ngrok-free.app') !== false;
        $isAllowedOrigin = in_array($origin, self::$allowedOrigins);
        $isVercelOrigin = strpos($origin, 'vercel.app') !== false;
        
        // Configurar headers CORS si cumple alguna condición
        if ($isNgrokHost || $isNgrokOrigin || $isAllowedOrigin || $isVercelOrigin) {
            if ($origin && ($isNgrokHost || $isNgrokOrigin || $isAllowedOrigin || $isVercelOrigin)) {
                header("Access-Control-Allow-Origin: " . $origin);
                header("Access-Control-Allow-Credentials: true");
            } else {
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Credentials: false");
            }
            
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin, ngrok-skip-browser-warning");
            header("Access-Control-Max-Age: 86400");
            header("Vary: Origin");
        }
    }

    /**
     * Maneja peticiones OPTIONS (preflight)
     */
    public static function handlePreflight(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            self::setupCORS();
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'CORS preflight handled']);
            exit();
        }
    }

    /**
     * Configuración completa de CORS (setup + preflight)
     */
    public static function initialize(): void {
        self::setupCORS();
        self::handlePreflight();
    }

    /**
     * Envía una respuesta de error en formato JSON con headers CORS
     */
    public static function errorResponse(string $message, int $statusCode = 500): void {
        self::setupCORS();
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }
}