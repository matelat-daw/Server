<?php
// filepath: c:\Server\html\Nueva-WEB\api\models\User.php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $profile_img;
    public $first_name;
    public $last_name;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
    $query = "INSERT INTO " . $this->table_name . " 
          SET username=:username, 
              email=:email, 
              password=:password, 
              profile_img=:profile_img
";

    $stmt = $this->conn->prepare($query);

    $this->username = htmlspecialchars(strip_tags($this->username));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $profile_img = $this->profile_img ? $this->profile_img : null;
    // Hash password con bcrypt (cost 12 para alta seguridad)
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]);

    $stmt->bindParam(":username", $this->username);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":password", $password_hash);
    $stmt->bindParam(":profile_img", $profile_img);

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
    $query = "SELECT id, username, email, password, created_at 
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
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET username=:username, 
                      email=:email,
                      first_name=:first_name,
                      last_name=:last_name";
        
        if (!empty($this->password)) {
            $query .= ", password=:password";
        }
        
        if (!empty($this->profile_img)) {
            $query .= ", profile_img=:profile_img";
        }
        
        $query .= " WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":id", $this->id);

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
    $query = "SELECT id, username, email, first_name, last_name, profile_img, created_at, updated_at 
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
            $this->profile_img = $row['profile_img'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
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
}
?>