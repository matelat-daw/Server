<?php
class UserController {
    private $userModel;

    public function __construct() {
        require_once '../models/User.php';
        $this->userModel = new User();
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