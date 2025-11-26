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
    $uri = $uri === '' ? '/' : '/' . ltrim($uri, '/');
} else {
    $uri = '/';
}

switch (true) {
    case $uri === '/' || $uri === '/login':
        $auth = new AuthController();
        $auth->login();
        break;
    case $uri === '/register':
        $auth = new AuthController();
        $auth->register();
        break;
    case $uri === '/logout':
        $auth = new AuthController();
        $auth->logout();
        break;
    case $uri === '/dashboard':
        $dashboard = new DashboardController();
        $dashboard->index();
        break;
    case $uri === '/profile':
        $profile = new ProfileController();
        $profile->index();
        break;
    case $uri === '/profile/update':
        $profile = new ProfileController();
        $profile->update();
        break;
    case $uri === '/profile/change-password':
        $profile = new ProfileController();
        $profile->changePassword();
        break;
    case $uri === '/pengiriman':
        $pengiriman = new PengirimanController();
        $pengiriman->index();
        break;
    case $uri === '/pengiriman/tambah':
        $pengiriman = new PengirimanController();
        $pengiriman->add();
        break;
    case $uri === '/pengiriman/store':
        $pengiriman = new PengirimanController();
        $pengiriman->store();
        break;
    case $uri === '/pengiriman/hitung-jarak':
        $pengiriman = new PengirimanController();
        $pengiriman->hitungJarakAjax();
        break;
    case $uri === '/pengiriman/edit':
        $pengiriman = new PengirimanController();
        $pengiriman->edit();
        break;
    case $uri === '/pengiriman/update':
        $pengiriman = new PengirimanController();
        $pengiriman->update();
        break;
    case $uri === '/pengiriman/detail':
        $pengiriman = new PengirimanController();
        $pengiriman->detail();
        break;
    case $uri === '/pengiriman/delete':
        $pengiriman = new PengirimanController();
        $pengiriman->delete();
        break;
    case $uri === '/supplier':
        $supplier = new SupplierController();
        $supplier->dashboard();
        break;
    case $uri === '/supplier/tambah':
        $supplier = new SupplierController();
        $supplier->tambahForm();
        break;
    case $uri === '/supplier/proses-tambah':
        $supplier = new SupplierController();
        $supplier->tambahRequest();
        break;
    case preg_match('#^/supplier/edit/(\d+)$#', $uri, $m):
        $supplier = new SupplierController();
        $supplier->editForm($m[1]);
        break;
    case preg_match('#^/supplier/update/(\d+)$#', $uri, $m):
        $supplier = new SupplierController();
        $supplier->updateRequest($m[1]);
        break;
    case preg_match('#^/supplier/hapus/(\d+)$#', $uri, $m):
        $supplier = new SupplierController();
        $supplier->hapus($m[1]);
        break;
    case $uri === '/inventaris_barang':
        $inventaris = new InventarisController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'add') $inventaris->add();
            elseif ($action === 'edit') $inventaris->edit();
        } else {
            $getAction = $_GET['action'] ?? 'index';
            if ($getAction === 'delete' && isset($_GET['id'])) {
                $inventaris->delete();
            } else {
                $inventaris->index();
            }
        }
        break;
    case $uri === '/inventaris_barang/get-resi':
        $inventaris = new InventarisController();
        $inventaris->getResiList();
        break;
    case $uri === '/inventaris_barang/histori':
        $inventaris = new InventarisController();
        $inventaris->histori();
        break;
    case $uri === '/inventaris_barang/kirim':
        $inventaris = new InventarisController();
        $inventaris->kirim();
        break;
    case $uri === '/pengguna':
        $userCtrl = new UserController();
        $userCtrl->index();
        break;
    case $uri === '/pengguna/tambah':
        $userCtrl = new UserController();
        $userCtrl->tambah();
        break;
    case $uri === '/pengguna/store':
        $userCtrl = new UserController();
        $userCtrl->store();
        break;
    case preg_match('#^/pengguna/edit/(\d+)$#', $uri, $m):
        $userCtrl = new UserController();
        $userCtrl->edit($m[1]);
        break;
    case preg_match('#^/pengguna/update/(\d+)$#', $uri, $m):
        $userCtrl = new UserController();
        $userCtrl->update($m[1]);
        break;
    case preg_match('#^/pengguna/hapus/(\d+)$#', $uri, $m):
        $userCtrl = new UserController();
        $userCtrl->hapus($m[1]);
        break;
        default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1><p>URL: $uri</p>";
        break;
}
