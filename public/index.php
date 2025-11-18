<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/PengirimanController.php';

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
    case '/register':
        $auth = new AuthController();
        $auth->register();
        break;
    case '/logout':
        $auth = new AuthController();
        $auth->logout();
        break;
    case '/dashboard':
        $dashboard = new DashboardController();
        $dashboard->index();
        break;
    case '/profile':
        $profile = new ProfileController();
        $profile->index();
        break;
    case '/profile/update':
        $profile = new ProfileController();
        $profile->update();
        break;
    case '/profile/change-password':
        $profile = new ProfileController();
        $profile->changePassword();
        break;
    case '/pengiriman':
        $pengiriman = new PengirimanController();
        $pengiriman->index();
        break;
    default:
        http_response_code(404);
        echo "Page not found";
}
?>
