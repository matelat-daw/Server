<?php
// filepath: c:\Server\html\Nueva-WEB\api\controllers\AuthController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        global $conn;
        $this->db = $conn;
        $this->user = new User($this->db);
    }

    public function register($data) {
        // Validar datos requeridos
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            return $this->sendResponse(400, false, "Username, email y contraseña son requeridos");
        }

        // Validar email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->sendResponse(400, false, "Email inválido");
        }

        // Validar longitud de contraseña
        if (strlen($data['password']) < 8) {
            return $this->sendResponse(400, false, "La contraseña debe tener al menos 8 caracteres");
        }

        // Verificar si el email ya existe
        $this->user->email = $data['email'];
        if ($this->user->emailExists()) {
            return $this->sendResponse(409, false, "El email ya está registrado");
        }

        // Manejar subida de imagen de perfil
        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $profile_image = $this->uploadProfileImage($_FILES['profile_image']);
            if (!$profile_image) {
                return $this->sendResponse(400, false, "Error al subir la imagen de perfil");
            }
        }

        // Crear usuario
    $this->user->username = $data['username'];
    $this->user->email = $data['email'];
    $this->user->password = $data['password'];

        if ($this->user->register()) {
            // Obtener roles del usuario
            $roles = $this->user->getRoles();
            
            // Generar JWT
            $token = $this->generateToken($this->user->id, $this->user->username, $this->user->email, $roles);
            
            // No enviar cookie JWT en registro, solo devolver el token en la respuesta

            $userData = [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'roles' => $roles
            ];

            return $this->sendResponse(201, true, "Usuario registrado exitosamente", $userData, $token);
        }

        return $this->sendResponse(500, false, "Error al registrar usuario");
    }

    public function login($data) {
        // Validar datos requeridos
        if (empty($data['email']) || empty($data['password'])) {
            return $this->sendResponse(400, false, "Email y contraseña son requeridos");
        }

        // Verificar si el usuario existe
        $this->user->email = $data['email'];
        if (!$this->user->emailExists()) {
            return $this->sendResponse(401, false, "Credenciales incorrectas");
        }

        // Verificar contraseña con bcrypt
        if (password_verify($data['password'], $this->user->password)) {
            // Obtener roles del usuario
            $roles = $this->user->getRoles();
            
            // Generar JWT
            $token = $this->generateToken($this->user->id, $this->user->username, $this->user->email, $roles);
            
            // Enviar token en cookie HTTP-only segura
            $this->setAuthCookie($token);

            $userData = [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'roles' => $roles
            ];

            return $this->sendResponse(200, true, "Login exitoso", $userData, $token);
        }

        return $this->sendResponse(401, false, "Credenciales incorrectas");
    }

    public function logout() {
        // Eliminar cookie de autenticación
        setcookie(
            'auth_token',
            '',
            time() - 3600,
            '/',
            '',
            false,  // Secure (cambiar a true en producción con HTTPS)
            true   // HttpOnly
        );

        return $this->sendResponse(200, true, "Logout exitoso");
    }

    public function validateToken() {
        $token = $this->getTokenFromCookie();
        
        if (!$token) {
            return $this->sendResponse(401, false, "Token no proporcionado");
        }

        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido o expirado");
        }

        // Obtener datos actualizados del usuario
        $this->user->id = $decoded['user_id'];
        if ($this->user->readOne()) {
            $roles = $this->user->getRoles();
            
            $userData = [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'profile_image' => $this->user->profile_image ? '/Nueva-WEB/api/uploads/profiles/' . $this->user->profile_image : null,
                'roles' => $roles
            ];

            return $this->sendResponse(200, true, "Token válido", $userData);
        }

        return $this->sendResponse(401, false, "Usuario no encontrado");
    }

    public function updateProfile($data) {
        $token = $this->getTokenFromCookie();
        
        if (!$token) {
            return $this->sendResponse(401, false, "No autorizado");
        }

        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido");
        }

        $this->user->id = $decoded['user_id'];
        $this->user->readOne();
        
        // Actualizar campos
        if (isset($data['username'])) {
            $this->user->username = $data['username'];
        }
        if (isset($data['email'])) {
            $this->user->email = $data['email'];
        }
        if (isset($data['first_name'])) {
            $this->user->first_name = $data['first_name'];
        }
        if (isset($data['last_name'])) {
            $this->user->last_name = $data['last_name'];
        }
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                return $this->sendResponse(400, false, "La contraseña debe tener al menos 8 caracteres");
            }
            $this->user->password = $data['password'];
        }

        // Manejar nueva imagen de perfil
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            // Eliminar imagen anterior si existe
            if ($this->user->profile_image) {
                $oldImagePath = __DIR__ . '/../uploads/profiles/' . $this->user->profile_image;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $profile_image = $this->uploadProfileImage($_FILES['profile_image']);
            if ($profile_image) {
                $this->user->profile_image = $profile_image;
            }
        }

        if ($this->user->update()) {
            $this->user->readOne();
            $roles = $this->user->getRoles();
            
            $userData = [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'profile_image' => $this->user->profile_image ? '/Nueva-WEB/api/uploads/profiles/' . $this->user->profile_image : null,
                'roles' => $roles
            ];

            return $this->sendResponse(200, true, "Perfil actualizado exitosamente", $userData);
        }

        return $this->sendResponse(500, false, "Error al actualizar perfil");
    }

    public function deleteProfile() {
        $token = $this->getTokenFromCookie();
        
        if (!$token) {
            return $this->sendResponse(401, false, "No autorizado");
        }

        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            return $this->sendResponse(401, false, "Token inválido");
        }

        $this->user->id = $decoded['user_id'];
        $this->user->readOne();

        // Eliminar imagen de perfil si existe
        if ($this->user->profile_image) {
            $imagePath = __DIR__ . '/../uploads/profiles/' . $this->user->profile_image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->user->delete()) {
            $this->logout();
            return $this->sendResponse(200, true, "Perfil eliminado exitosamente");
        }

        return $this->sendResponse(500, false, "Error al eliminar perfil");
    }

    private function uploadProfileImage($file) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validar tipo de archivo
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }

        // Validar tamaño
        if ($file['size'] > $max_size) {
            return false;
        }

        // Crear directorio si no existe
        $upload_dir = __DIR__ . '/../uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Obtener extensión original
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Generar nombre único: profile_userid_timestamp.ext
        $filename = 'profile_' . uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }

    private function generateToken($user_id, $username, $email, $roles) {
        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60 * 24 * 7); // 7 días
        
        $payload = [
            'iat' => $issued_at,
            'exp' => $expiration_time,
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'roles' => $roles
        ];

        return JWT::encode($payload);
    }

    private function setAuthCookie($token) {
        setcookie(
            'auth_token',
            $token,
            time() + (60 * 60 * 24 * 7), // 7 días
            '/',
            '',
            false,  // Secure (cambiar a true en producción con HTTPS)
            true   // HttpOnly (no accesible desde JavaScript)
        );
    }

    private function getTokenFromCookie() {
        // Primero intentar obtener de cookie
        if (isset($_COOKIE['auth_token'])) {
            return $_COOKIE['auth_token'];
        }
        
        // Si no está en cookie, intentar obtener del header Authorization
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    private function sendResponse($status, $success, $message, $data = null, $token = null) {
        http_response_code($status);
        
        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($data !== null) {
            $response['user'] = $data;
        }

        if ($token !== null) {
            $response['token'] = $token;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>