<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$basePath = '/Manajemen-Logistik-UKOM/public/';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
    if ($uri === '') {
        $uri = '/';
    } else {
        $uri = '/' . ltrim($uri, '/');
    }
} else {
    $uri = '/';
}

switch ($uri) {
    case '/':
    case '/login':
        $auth = new AuthController();
        $auth->login();
        break;
    case '/logout':
        $auth = new AuthController();
        $auth->logout();
        break;
    case '/dashboard':
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login'); 
            exit;
        }
        require __DIR__ . '/../app/views/dashboard.php';
        break;
    default:
        http_response_code(404);
        echo "Page not found";
}