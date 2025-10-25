<?php
require_once '../controllers/AuthController.php';
require_once '../controllers/ProductController.php';
require_once '../controllers/UserController.php';
require_once '../middleware/AuthMiddleware.php';

$router = new Router();

// User authentication routes
$router->post('/api/login', [AuthController::class, 'login']);
$router->post('/api/register', [AuthController::class, 'register']);

// Product routes
$router->get('/api/products', [ProductController::class, 'index']);
$router->post('/api/products', [ProductController::class, 'store']);
$router->get('/api/products/{id}', [ProductController::class, 'show']);
$router->put('/api/products/{id}', [ProductController::class, 'update']);
$router->delete('/api/products/{id}', [ProductController::class, 'destroy']);

// User routes
$router->get('/api/users/{id}', [UserController::class, 'show']);
$router->put('/api/users/{id}', [UserController::class, 'update']);

// Middleware for protected routes
$router->group(['middleware' => AuthMiddleware::class], function() use ($router) {
    $router->get('/api/user/profile', [UserController::class, 'profile']);
    $router->get('/api/user/cart', [UserController::class, 'cart']);
});

$router->run();
?>