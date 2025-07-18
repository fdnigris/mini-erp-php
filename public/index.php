<?php

session_start();

// Carregar configurações
require_once __DIR__ . '/../config/app.php';

// Autoloader
spl_autoload_register(function ($class) {
    // Remover o namespace App\
    $class = str_replace('App\\', '', $class);
    
    // Converter para o caminho do sistema
    $base_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    $file = $base_dir . 'app' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    
    // Verificar se o arquivo existe
    if (file_exists($file)) {
        require_once $file;
    }
});

// Roteador simples
$route = $_GET['route'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Mapeia rotas para controladores
$routes = [
    'home' => 'HomeController',
    'products' => 'ProductController',
    'cart' => 'CartController',
    'orders' => 'OrderController',
    'coupons' => 'CouponController',
    'cep' => 'CepController',
    'webhook' => 'WebhookController'
];

if (isset($routes[$route])) {
    $controllerName = "App\\Controllers\\" . $routes[$route];
    $controller = new $controllerName();
    
    if (method_exists($controller, $action)) {
        // Se o método espera parâmetros e temos um ID, passamos o ID
        $reflection = new ReflectionMethod($controller, $action);
        $parameters = $reflection->getParameters();
        
        if (count($parameters) > 0 && $id !== null) {
            $controller->$action($id);
        } else {
            $controller->$action();
        }
    } else {
        http_response_code(404);
        echo "Action not found";
    }
} else {
    http_response_code(404);
    echo "Route not found";
} 