<?php
// filepath: c:\Server\html\Nueva-WEB\api\models\Role.php
class Role {
    private $conn;
    private $table_name = "roles";
    private $user_roles_table = "user_roles";

    public $id;
    public $name;
    public $description;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los roles
    public function readAll() {
        $query = "SELECT id, name, description, created_at 
                  FROM " . $this->table_name . " 
                  ORDER BY name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Obtener un rol por ID
    public function readOne() {
        $query = "SELECT id, name, description, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    // Obtener roles de un usuario
    public function getUserRoles($user_id) {
        // Seleccionar solo columnas que existen de forma segura en la mayoría de esquemas (id, name)
        $query = "SELECT r.id, r.name 
                  FROM " . $this->table_name . " r
                  INNER JOIN " . $this->user_roles_table . " ur ON r.id = ur.role_id
                  WHERE ur.user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt;
    }

    // Asignar rol a usuario
    public function assignRoleToUser($user_id, $role_id) {
        $query = "INSERT INTO " . $this->user_roles_table . " 
                  (user_id, role_id) 
                  VALUES (:user_id, :role_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":role_id", $role_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Remover rol de usuario
    public function removeRoleFromUser($user_id, $role_id) {
        $query = "DELETE FROM " . $this->user_roles_table . " 
                  WHERE user_id = :user_id AND role_id = :role_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":role_id", $role_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Verificar si un usuario tiene un rol específico
    public function userHasRole($user_id, $role_name) {
        $query = "SELECT COUNT(*) as count 
                  FROM " . $this->user_roles_table . " ur
                  INNER JOIN " . $this->table_name . " r ON ur.role_id = r.id
                  WHERE ur.user_id = :user_id AND r.name = :role_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":role_name", $role_name);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Verificar si un usuario es admin
    public function isAdmin($user_id) {
        return $this->userHasRole($user_id, 'admin');
    }

    // Crear nuevo rol
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, description=:description";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Actualizar rol
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, description=:description 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Eliminar rol
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>