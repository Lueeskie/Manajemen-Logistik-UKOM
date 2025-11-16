<?php

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
    <title>Login - Manajemen Logistik</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="brand-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                AstralExpress
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item ms-3">
                        <a href="<?= BASE_URL ?>register" class="btn btn-outline-primary btn-daftar">
                            <i class="bi bi-person-plus me-2"></i>Daftar Gratis
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-pattern"></div>
        <div class="container hero-wrapper">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-white">
                    <span class="badge bg-soft-primary text-uppercase">Astral Express</span>
                    <h1 class="display-5 fw-bold mt-3">Selamat Datang di Astral Express</h1>
                    <p class="lead text-white-50 mb-4">Platform terpadu untuk mengelola distribusi barang, memantau
                        stok,
                        dan mengoptimalkan rantai pasok perusahaan Anda.</p>
                    <ul class="hero-list">
                        <li><i class="bi bi-check-circle"></i>Monitoring pergerakan Paket</li>
                    </ul>
                </div>

                <div class="col-lg-5 offset-lg-1">
                    <div class="card login-card border-0 shadow-lg">
                        <div class="card-body p-4 p-lg-5">
                            <h3 class="fw-bold mb-2">Masuk ke Akun Anda</h3>
                            <p class="text-muted mb-4">Silakan masuk untuk mengakses dashboard</p>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Email atau Username</label>
                                    <div class="input-group custom-input">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="text" class="form-control" name="username"
                                            placeholder="nama@Gmail.com" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group custom-input">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Masukkan password" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">Ingat Saya</label>
                                    </div>
                                    <a href="#" class="small text-primary text-decoration-none">Lupa Password?</a>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sekarang
                                </button>
                                <p class="text-center text-muted small">
                                    Belum punya akun? <a href="<?= BASE_URL ?>register" class="text-primary text-decoration-none fw-600">Daftar di sini</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($error)) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Masuk',
                    text: <?= json_encode($error) ?>,
                    confirmButtonColor: '#dc3545'
                });
            <?php endif; ?>
  
            <?php if (isset($success)) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: <?= json_encode($success) ?>,
                    confirmButtonColor: '#0d6efd'
                });
            <?php endif; ?>

            <?php if (!empty($flash_success)) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: <?= json_encode($flash_success) ?>,
                    confirmButtonText: 'Oke, Masuk!',
                    confirmButtonColor: '#0d6efd'
                }).then(() => {
                    const usernameInput = document.querySelector('input[name=\"username\"]');
                    if (usernameInput) {
                        usernameInput.focus();
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>

