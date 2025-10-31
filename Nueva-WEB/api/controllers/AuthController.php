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

        // Manejar subida de imagen de perfil o avatar por género
        $gender = isset($data['gender']) ? $data['gender'] : 'other';
        // Determinar avatar por género antes de registrar
        $avatarFile = 'other.png';
        if ($gender === 'male') $avatarFile = 'male.png';
        if ($gender === 'female') $avatarFile = 'female.png';

        // Asignar una ruta temporal válida (pero con el ID real tras registrar)
        $this->user->profile_img = 'users/default/profile.png';
        $this->user->username = $data['username'];
        $this->user->email = $data['email'];
        $this->user->password = $data['password'];

        if ($this->user->register()) {
            $userId = $this->user->id;
            $destDir = __DIR__ . '/../uploads/users/' . $userId . '/';
            if (!file_exists($destDir)) mkdir($destDir, 0777, true);
            $profile_img = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                // Usar la extensión original
                $extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                $dest = $destDir . 'profile.' . $extension;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $dest)) {
                    $profile_img = 'users/' . $userId . '/profile.' . $extension;
                }
            } else {
                // Copiar avatar por género
                $src = __DIR__ . '/../media/' . $avatarFile;
                $dest = $destDir . 'profile.png';
                if (file_exists($src)) {
                    copy($src, $dest);
                    $profile_img = 'users/' . $userId . '/profile.png';
                }
            }
            if ($profile_img) {
                $this->user->profile_img = $profile_img;
                $this->user->update();
            }
            // Obtener roles del usuario
            $roles = $this->user->getRoles();
            // Generar JWT
            $token = $this->generateToken($this->user->id, $this->user->username, $this->user->email, $roles);
            // No enviar cookie JWT en registro, solo devolver el token en la respuesta
            $userData = [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'profile_img' => $this->user->profile_img ? '/Nueva-WEB/api/uploads/' . $this->user->profile_img : null,
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
                'profile_img' => $this->user->profile_img ? '/Nueva-WEB/api/uploads/' . $this->user->profile_img : null,
                'created_at' => $this->user->created_at,
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
            if ($this->user->profile_img) {
                $oldImagePath = __DIR__ . '/../uploads/' . $this->user->profile_img;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $profile_img = $this->uploadProfileImage($_FILES['profile_image']);
            if ($profile_img) {
                $this->user->profile_img = $profile_img;
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
                'profile_img' => $this->user->profile_img ? '/Nueva-WEB/api/uploads/' . $this->user->profile_img : null,
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
        if ($this->user->profile_img) {
            $imagePath = __DIR__ . '/../uploads/' . $this->user->profile_img;
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

        // Obtener extensión original
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Obtener ID de usuario (de la instancia actual)
        $userId = isset($this->user->id) ? $this->user->id : null;
        if (!$userId) {
            // No se puede guardar sin ID de usuario
            return false;
        }

        // Crear directorio por usuario si no existe
        $upload_dir = __DIR__ . '/../uploads/users/' . $userId . '/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Nombre fijo: profile.ext
        $filename = 'profile.' . $extension;
        $filepath = $upload_dir . $filename;

        // Si ya existe, eliminarlo
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Devolver la ruta relativa desde uploads/
            return 'users/' . $userId . '/' . $filename;
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
        // Detectar si estamos en HTTPS o HTTP
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

        $expires = time() + (60 * 60 * 24 * 7); // 7 días

        // Compatibilidad: usar sintaxis de array (PHP >= 7.3) o parámetros antiguos
        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
            // No establecer 'domain' para que use el host actual (evita problemas en localhost)
            setcookie('auth_token', $token, [
                'expires'  => $expires,
                'path'     => '/',
                'secure'   => $isHttps, // true solo bajo HTTPS
                'httponly' => true,
                'samesite' => $isHttps ? 'None' : 'Lax',
            ]);
        } else {
            // Fallback para PHP < 7.3
            // Nota: SameSite no se puede establecer con esta firma; quedará por defecto (Lax en la mayoría de navegadores)
            setcookie('auth_token', $token, $expires, '/', '', $isHttps, true);
        }

        // Cabecera de depuración para confirmar que se intentó setear la cookie
        header('X-Debug-SetAuthCookie: called');
        // Permitir que el navegador exponga este header y el Set-Cookie (no requerido para almacenar la cookie, pero útil para debug CORS)
        header('Access-Control-Expose-Headers: X-Debug-SetAuthCookie, Set-Cookie');
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