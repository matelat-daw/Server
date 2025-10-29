<?php
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

$router = new Router();


// Helper para prefijo base
$apiBase = '/Nueva-WEB/api';

// User authentication routes
$router->post("$apiBase/login", [AuthController::class, 'login']);
$router->post("$apiBase/register", [AuthController::class, 'register']);

// Product routes
$router->get("$apiBase/products", [ProductController::class, 'index']);
$router->post("$apiBase/products", [ProductController::class, 'store']);
$router->get("$apiBase/products/{id}", [ProductController::class, 'show']);
$router->put("$apiBase/products/{id}", [ProductController::class, 'update']);
$router->delete("$apiBase/products/{id}", [ProductController::class, 'destroy']);

// User routes
$router->get("$apiBase/users/{id}", [UserController::class, 'show']);
$router->put("$apiBase/users/{id}", [UserController::class, 'update']);

// Middleware for protected routes
$router->group(['middleware' => AuthMiddleware::class], function() use ($router, $apiBase) {
    $router->get("$apiBase/user/profile", [UserController::class, 'profile']);
    $router->get("$apiBase/user/cart", [UserController::class, 'cart']);
});

$router->run();
?>