<?php
class AuthMiddleware {
    public function handle() {
        // Obtener token de cookie o header Authorization
        $token = null;
        if (isset($_COOKIE['auth_token'])) {
            $token = $_COOKIE['auth_token'];
        } else {
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            if (isset($headers['Authorization']) && preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $m)) {
                $token = $m[1];
            }
        }

        require_once __DIR__ . '/../config/jwt.php';
        $decoded = JWT::decode($token);
        if (!$decoded || !isset($decoded['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        // Inyectar contexto simple
        $_REQUEST['auth_user_id'] = $decoded['user_id'];
    }
}
?>