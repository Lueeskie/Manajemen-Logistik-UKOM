<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Manajemen Logistik</title>
    <style> body { font-family: Arial; } </style>
</head>
<body>
    <h2>Selamat datang di Dashboard, <?php echo $_SESSION['role']; ?>!</h2>
    <p>Ini halaman utama setelah login. Nanti kita tambah fitur seperti daftar pengiriman paket.</p>
    <a href="<?php echo BASE_URL; ?>logout">Logout</a>
</body>
</html>