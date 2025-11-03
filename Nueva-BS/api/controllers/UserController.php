<?php
class UserController {
    private $userModel;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../models/User.php';
        global $conn;
        $this->userModel = new User($conn);
        header('Content-Type: application/json');
    }

    // GET /users/{id}
    public function show($userId) {
        $this->userModel->id = $userId;
        if ($this->userModel->readOne()) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $this->userModel->id,
                    'username' => $this->userModel->username,
                    'email' => $this->userModel->email,
                    'first_name' => $this->userModel->first_name ?? null,
                    'last_name' => $this->userModel->last_name ?? null,
                    'profile_img' => $this->userModel->profile_img ? '/Nueva-BS/api/uploads/' . $this->userModel->profile_img : null,
                ]
            ]);
            return;
        }
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }

    // PUT /users/{id} (JSON o form-data)
    public function update($userId, $data) {
        $this->userModel->id = $userId;
        $this->userModel->readOne();

        if (isset($data['username'])) $this->userModel->username = $data['username'];
        if (isset($data['email'])) $this->userModel->email = $data['email'];
        if (isset($data['first_name'])) $this->userModel->first_name = $data['first_name'];
        if (isset($data['last_name'])) $this->userModel->last_name = $data['last_name'];
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres']);
                return;
            }
            $this->userModel->password = $data['password'];
        }

        // Soportar imagen de perfil en form-data
        if (isset($data['_files']['profile_image']) && $data['_files']['profile_image']['error'] === UPLOAD_ERR_OK) {
            $file = $data['_files']['profile_image'];
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            if (!in_array($file['type'], $allowed_types) || $file['size'] > $max_size) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Archivo de imagen inválido']);
                return;
            }
            $upload_dir = __DIR__ . '/../uploads/users/' . $userId . '/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $dest = $upload_dir . 'profile.' . $extension;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $this->userModel->profile_img = 'users/' . $userId . '/profile.' . $extension;
            }
        }

        if ($this->userModel->update()) {
            $this->userModel->readOne();
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $this->userModel->id,
                    'username' => $this->userModel->username,
                    'email' => $this->userModel->email,
                    'first_name' => $this->userModel->first_name ?? null,
                    'last_name' => $this->userModel->last_name ?? null,
                    'profile_img' => $this->userModel->profile_img ? '/Nueva-BS/api/uploads/' . $this->userModel->profile_img : null,
                ]
            ]);
            return;
        }
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario']);
    }
}
?>