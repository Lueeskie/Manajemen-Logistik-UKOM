<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$flash_success = '';
if (isset($_SESSION['flash_success'])) {
    $flash_success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Manajemen Logistik</title>
    <style>
        body {
            font-family: Arial;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <h2>Selamat datang di Dashboard, <?php echo $_SESSION['role']; ?>!</h2>
    <p>Ini halaman utama setelah login. Nanti kita tambah fitur seperti daftar pengiriman paket.</p>
    <a href="<?php echo BASE_URL; ?>logout">Logout</a>

    <script>
        <?php if (!empty($flash_success)) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: <?= json_encode($flash_success) ?>,
                confirmButtonColor: '#0d6efd'
            });
        <?php endif; ?>
    </script>
</body>

</html>
