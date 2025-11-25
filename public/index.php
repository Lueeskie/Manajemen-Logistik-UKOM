<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/InventarisController.php';
require_once __DIR__ . '/../app/controllers/PengirimanController.php';
require_once __DIR__ . '/../app/controllers/SupplierController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';

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


if (strpos($uri, '/inventaris_barang') === 0) {
    $inventaris = new InventarisController();
    if ($uri === '/inventaris_barang/get-resi') {
        $inventaris->getResiList();
        exit; // Penting: Stop execution setelah API call
    }
    elseif ($uri === '/inventaris_barang/histori') {
        $inventaris->histori();
        exit;
    }
    elseif ($uri === '/inventaris_barang/kirim') {
        $inventaris->kirim();
        exit;
    }
    elseif ($uri === '/inventaris_barang') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_action = $_POST['action'] ?? '';
            if ($post_action === 'add') {
                $inventaris->add();
                exit;
            } elseif ($post_action === 'edit') {
                $inventaris->edit();
                exit;
            }
        }
        $action = $_GET['action'] ?? 'index';
        if ($action === 'delete' && isset($_GET['id'])) {
            $inventaris->delete();
            exit;
        } else {
            $inventaris->index();
            exit;
        }
    }
    
    exit;
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

    case '/pengiriman/tambah':
        $pengiriman = new PengirimanController();
        $pengiriman->add();
        break;

    case '/pengiriman/store':
        $pengiriman = new PengirimanController();
        $pengiriman->store();
        break;

    case '/pengiriman/hitung-jarak':
        $pengiriman = new PengirimanController();
        $pengiriman->hitungJarakAjax();
        break;

    case '/pengiriman/edit':
        $pengiriman = new PengirimanController();
        $pengiriman->edit();
        break;

    case '/pengiriman/update':
        $pengiriman = new PengirimanController();
        $pengiriman->update();
        break;

    case '/pengiriman/detail':
        $pengiriman = new PengirimanController();
        $pengiriman->detail();
        break;

    case '/pengiriman/delete':
        $pengiriman = new PengirimanController();
        $pengiriman->delete();
        break;
    case '/supplier':
        $supplier = new SupplierController();
        $supplier->dashboard();
        break;
    case '/supplier/tambah':
        $supplier = new SupplierController();
        $supplier->tambahForm();
        break;
    case '/supplier/proses-tambah':
        $supplier = new SupplierController();
        $supplier->tambahRequest();
        break;
    case '/pengguna':
        $userCtrl = new UserController();
        $userCtrl->index();
        break;
    case '/pengguna/tambah':
        $userCtrl = new UserController();
        $userCtrl->tambah();
        break;
    case '/pengguna/store':
        $userCtrl = new UserController();
        $userCtrl->store();
        break;
    case (preg_match('#^/pengguna/edit/(\d+)$#', $uri, $matches) ? true : false):
        $userCtrl = new UserController();
        $userCtrl->edit($matches[1]);
        break;
    case (preg_match('#^/pengguna/update/(\d+)$#', $uri, $matches) ? true : false):
        $userCtrl = new UserController();
        $userCtrl->update($matches[1]);
        break;
    case (preg_match('#^/pengguna/hapus/(\d+)$#', $uri, $matches) ? true : false):
        $userCtrl = new UserController();
        $userCtrl->hapus($matches[1]);
        break;
    case (preg_match('#^/supplier/edit/(\d+)$#', $uri, $matches) ? true : false):
        $supplier = new SupplierController();
        $supplier->editForm($matches[1]);
        break;
    case (preg_match('#^/supplier/update/(\d+)$#', $uri, $matches) ? true : false):
        $supplier = new SupplierController();
        $supplier->updateRequest($matches[1]);
        break;
    case (preg_match('#^/supplier/hapus/(\d+)$#', $uri, $matches) ? true : false):
        $supplier = new SupplierController();
        $supplier->hapus($matches[1]);
        break;
        default:
        http_response_code(404);
        echo "Page not found";
}
