<?php
class AuthMiddleware {
    public function validateUser($request) {
        // Check if the user is authenticated
        if (!isset($request['user_id'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Unauthorized']);
            exit();
        }
    }
}
?>