<?php
// api/index.php

require_once 'config/database.php';
require_once 'routes/api.php';

header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Initialize the API routing
$route = new ApiRouter($method, $requestUri);
$route->handleRequest();
?>