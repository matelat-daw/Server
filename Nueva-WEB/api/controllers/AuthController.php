<?php
class AuthController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function register($username, $password) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Prepare SQL statement
        $stmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        
        if ($stmt->execute()) {
            return ["status" => "success", "message" => "User registered successfully."];
        } else {
            return ["status" => "error", "message" => "Registration failed."];
        }
    }

    public function login($username, $password) {
        // Prepare SQL statement
        $stmt = $this->db->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            
            // Verify password
            if (password_verify($password, $hashedPassword)) {
                // Generate a session or token for the user
                return ["status" => "success", "message" => "Login successful."];
            } else {
                return ["status" => "error", "message" => "Invalid password."];
            }
        } else {
            return ["status" => "error", "message" => "User not found."];
        }
    }
}
?>