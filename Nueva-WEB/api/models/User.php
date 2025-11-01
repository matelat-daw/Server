<?php
// filepath: c:\Server\html\Nueva-WEB\api\models\User.php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $gender;
    public $profile_img;
    public $is_active;
    public $activation_token;
    public $activation_token_expires;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
              SET username=:username, 
                  email=:email, 
                  password=:password,
                  first_name=:first_name,
                  last_name=:last_name,
                  gender=:gender,
                  profile_img=:profile_img,
                  is_active=:is_active,
                  activation_token=:activation_token,
                  activation_token_expires=:activation_token_expires";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $first_name = $this->first_name ? htmlspecialchars(strip_tags($this->first_name)) : null;
        $last_name = $this->last_name ? htmlspecialchars(strip_tags($this->last_name)) : null;
        $gender = $this->gender ? $this->gender : 'other';
        $profile_img = $this->profile_img ? $this->profile_img : null;
        
        // Hash password con bcrypt (cost 12 para alta seguridad)
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":profile_img", $profile_img);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":activation_token", $this->activation_token);
        $stmt->bindParam(":activation_token_expires", $this->activation_token_expires);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // Asignar rol 'user' por defecto
            $this->assignDefaultRole();
            
            return true;
        }

        return false;
    }

    private function assignDefaultRole() {
        require_once __DIR__ . '/Role.php';
        $role = new Role($this->conn);
        
        // Obtener el ID del rol 'user'
        $query = "SELECT id FROM roles WHERE name = 'user' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $role->assignRoleToUser($this->id, $row['id']);
        }
    }

    public function emailExists() {
        $query = "SELECT id, username, email, password, first_name, last_name, gender, profile_img, is_active, created_at 
                  FROM " . $this->table_name . " 
                  WHERE email = :email 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->gender = $row['gender'];
            $this->profile_img = $row['profile_img'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET username=:username, 
                      email=:email";
        
        $params = [':username', ':email', ':id'];
        
        if (!empty($this->first_name)) {
            $query .= ", first_name=:first_name";
            $params[] = ':first_name';
        }
        if (!empty($this->last_name)) {
            $query .= ", last_name=:last_name";
            $params[] = ':last_name';
        }
        if (!empty($this->gender)) {
            $query .= ", gender=:gender";
            $params[] = ':gender';
        }
        if (!empty($this->password)) {
            $query .= ", password=:password";
            $params[] = ':password';
        }
        if (!empty($this->profile_img)) {
            $query .= ", profile_img=:profile_img";
            $params[] = ':profile_img';
        }
        $query .= " WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);

        if (!empty($this->first_name)) {
            $first_name = htmlspecialchars(strip_tags($this->first_name));
            $stmt->bindParam(":first_name", $first_name);
        }
        if (!empty($this->last_name)) {
            $last_name = htmlspecialchars(strip_tags($this->last_name));
            $stmt->bindParam(":last_name", $last_name);
        }
        if (!empty($this->gender)) {
            $stmt->bindParam(":gender", $this->gender);
        }
        if (!empty($this->password)) {
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt->bindParam(":password", $password_hash);
        }
        if (!empty($this->profile_img)) {
            $stmt->bindParam(":profile_img", $this->profile_img);
        }

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function readOne() {
        $query = "SELECT id, username, email, first_name, last_name, gender, profile_img, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->gender = $row['gender'];
            $this->profile_img = $row['profile_img'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    public function getRoles() {
        require_once __DIR__ . '/Role.php';
        $role = new Role($this->conn);
        $stmt = $role->getUserRoles($this->id);
        
        $roles = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $roles[] = $row['name'];
        }
        
        return $roles;
    }

    public function hasRole($role_name) {
        require_once __DIR__ . '/Role.php';
        $role = new Role($this->conn);
        return $role->userHasRole($this->id, $role_name);
    }

    public function isAdmin() {
        return $this->hasRole('admin');
    }

    /**
     * Activar cuenta de usuario usando token
     */
    public function activateAccount($token) {
        $query = "SELECT id, username, email, activation_token_expires, is_active 
                  FROM " . $this->table_name . " 
                  WHERE activation_token = :token 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si ya está activado
            if ($row['is_active'] == 1) {
                return ['success' => false, 'message' => 'Esta cuenta ya está activada'];
            }
            
            // Verificar si el token ha expirado
            $now = new DateTime();
            $expires = new DateTime($row['activation_token_expires']);
            
            if ($now > $expires) {
                return ['success' => false, 'message' => 'El enlace de activación ha expirado'];
            }
            
            // Activar cuenta
            $updateQuery = "UPDATE " . $this->table_name . " 
                           SET is_active = 1, 
                               activation_token = NULL, 
                               activation_token_expires = NULL 
                           WHERE id = :id";
            
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":id", $row['id']);
            
            if ($updateStmt->execute()) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->is_active = 1;
                
                return ['success' => true, 'message' => 'Cuenta activada exitosamente'];
            }
        }
        
        return ['success' => false, 'message' => 'Token de activación inválido'];
    }
}
?>