<?php
// Simple Router class for handling API routes
class Router {
    private $routes = [];
    private $middlewareGroups = [];
    private $currentGroup = null;

    public function add($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => $this->currentGroup ? $this->middlewareGroups[$this->currentGroup] : null
        ];
    }

    public function get($pattern, $callback) {
        $this->add('GET', $pattern, $callback);
    }
    public function post($pattern, $callback) {
        $this->add('POST', $pattern, $callback);
    }
    public function put($pattern, $callback) {
        $this->add('PUT', $pattern, $callback);
    }
    public function delete($pattern, $callback) {
        $this->add('DELETE', $pattern, $callback);
    }

    public function group($options, $callback) {
        if (isset($options['middleware'])) {
            $groupId = uniqid('group_', true);
            $this->middlewareGroups[$groupId] = $options['middleware'];
            $this->currentGroup = $groupId;
            $callback();
            $this->currentGroup = null;
        } else {
            $callback();
        }
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        foreach ($this->routes as $route) {
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route['pattern']);
            $pattern = '#^' . $pattern . '$#';
            if ($method === $route['method'] && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                // Middleware support (simple)
                if ($route['middleware']) {
                    $middleware = $route['middleware'];
                    if (is_string($middleware) && class_exists($middleware)) {
                        $instance = new $middleware();
                        if (method_exists($instance, 'handle')) {
                            $instance->handle();
                        }
                    }
                }
                $callback = $route['callback'];
                // Leer datos para POST y PUT
                if (in_array($method, ['POST', 'PUT'])) {
                    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
                    if (stripos($contentType, 'multipart/form-data') !== false) {
                        // Formulario con archivos
                        $input = $_POST;
                        if (!empty($_FILES)) {
                            $input['_files'] = $_FILES;
                        }
                    } else {
                        // JSON
                        $input = json_decode(file_get_contents('php://input'), true);
                    }
                    array_unshift($matches, $input);
                }
                if (is_array($callback) && count($callback) === 2) {
                    $controller = new $callback[0]();
                    $methodName = $callback[1];
                    call_user_func_array([$controller, $methodName], $matches);
                } elseif (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                }
                return;
            }
        }
        // Not found
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
