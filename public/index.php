<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/InventarisController.php';

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

// Handle inventaris_barang routes (sebelum switch untuk catch semua sub-routes)
if (strpos($uri, '/inventaris_barang') === 0) {
    $inventaris = new InventarisController();
    
    // Route: /inventaris_barang/histori
    if ($uri === '/inventaris_barang/histori') {
        $inventaris->histori();
    }
    // Route: /inventaris_barang/kirim (POST)
    elseif ($uri === '/inventaris_barang/kirim') {
        $inventaris->kirim();
    }
    // Route: /inventaris_barang/get-resi (AJAX - autocomplete)
    elseif ($uri === '/inventaris_barang/get-resi') {
        $inventaris->getResiList();
    }
    // Route: /inventaris_barang (default)
    else {
        $action = $_GET['action'] ?? 'index';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_action = $_POST['action'] ?? $action;
            if ($post_action === 'add') {
                $inventaris->add();
            } elseif ($post_action === 'edit') {
                $inventaris->edit();
            }
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $inventaris->delete();
        } else {
            $inventaris->index();
        }
    }
    exit; // Penting: Stop execution setelah handle inventaris routes
}

// Switch untuk routes lainnya
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
    default:
        http_response_code(404);
        echo "Page not found";
}
?>