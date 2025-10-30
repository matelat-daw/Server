<?php
class UserController {
    private $userModel;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../models/User.php';
        global $conn;
        $this->userModel = new User($conn);
    }

    public function getUserDetails($userId) {
        return $this->userModel->find($userId);
    }

    public function updateUserProfile($userId, $data) {
        return $this->userModel->update($userId, $data);
    }

    public function deleteUser($userId) {
        return $this->userModel->delete($userId);
    }
}
?>