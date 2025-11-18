<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register - Manajemen Logistik</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/register.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="<?= BASE_URL ?>assets/logo2.png" alt="Astral Express" class="brand-logo">
                <span class="brand-text">AstralExpress</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item ms-3">
                        <a href="<?= BASE_URL ?>login" class="btn btn-outline-primary btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
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
                    <span class="badge bg-soft-primary text-uppercase">Bergabunglah Sekarang</span>
                    <h1 class="display-5 fw-bold mt-3">Daftar di Astral Express</h1>
                    <p class="lead text-white-50 mb-4">Mulai kelola distribusi barang, pantau stok, dan optimalkan rantai pasok perusahaan Anda hari ini.</p>
                    <ul class="hero-list">
                        <li><i class="bi bi-check-circle"></i>Akses dashboard lengkap</li>
                        <li><i class="bi bi-check-circle"></i>Monitoring pergerakan paket</li>
                    </ul>
                </div>

                <div class="col-lg-5 offset-lg-1">
                    <div class="card register-card border-0 shadow-lg">
                        <div class="card-body p-4 p-lg-5">
                            <h3 class="fw-bold mb-2">Buat Akun Baru</h3>
                            <p class="text-muted mb-4">Isi data di bawah untuk membuat akun</p>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <div class="input-group custom-input">
                                        <span class="input-group-text"><i class="bi bi-at"></i></span>
                                        <input type="text" class="form-control" name="username"
                                            placeholder="Pilih username" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-group custom-input">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="nama@Gmail.com" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group custom-input">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Minimal 6 karakter" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <div class="input-group custom-input">
                                        <span class="input-group-text"><i class="bi bi-lock-check"></i></span>
                                        <input type="password" class="form-control" name="confirm_password"
                                            placeholder="Ulangi password" required>
                                    </div>
                                </div>

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" name="agreeTerms" required>
                                    <label class="form-check-label small" for="agreeTerms">
                                        Saya setuju dengan <a href="#" class="text-primary text-decoration-none">Syarat & Ketentuan</a>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                                </button>

                                <p class="text-center text-muted small">
                                    Sudah punya akun? <a href="<?= BASE_URL ?>login" class="text-primary text-decoration-none fw-600">Masuk di sini</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($error)) : ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mendaftar',
                    text: <?= json_encode($error) ?>,
                    confirmButtonColor: '#dc3545'
                });
            <?php endif; ?>

            <?php if (isset($success)) : ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil',
                    text: <?= json_encode($success) ?>,
                    confirmButtonColor: '#0d6efd'
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>