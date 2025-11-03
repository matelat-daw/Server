<?php
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/UserController.php';

$router = new Router();

// Base API para Nueva-BS
$apiBase = '/Nueva-BS/api';

// User authentication routes
$router->post("$apiBase/login", [AuthController::class, 'login']);
$router->post("$apiBase/register", [AuthController::class, 'register']);
$router->post("$apiBase/logout", [AuthController::class, 'logout']);
$router->post("$apiBase/activate", [AuthController::class, 'activateAccount']);
// Endpoints de perfil (PUT acepta form-data por imagen)
$router->get("$apiBase/auth/validate", [AuthController::class, 'validateToken']);
$router->put("$apiBase/auth/profile", [AuthController::class, 'updateProfile']);
$router->delete("$apiBase/auth/profile", [AuthController::class, 'deleteProfile']);

// Product routes
$router->get("$apiBase/products", [ProductController::class, 'index']);
$router->post("$apiBase/products", [ProductController::class, 'store']);
$router->get("$apiBase/products/{id}", [ProductController::class, 'show']);
$router->put("$apiBase/products/{id}", [ProductController::class, 'update']);
$router->delete("$apiBase/products/{id}", [ProductController::class, 'destroy']);

// User routes
$router->get("$apiBase/users/{id}", [UserController::class, 'show']);
$router->put("$apiBase/users/{id}", [UserController::class, 'update']);

$router->run();
?>