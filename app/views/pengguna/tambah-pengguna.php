<?php

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna - Astral Express</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/pengguna-tambah.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <main class="main-content">
        <div class="container-fluid px-4 py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <a href="<?= BASE_URL ?>pengguna" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="h3 fw-bold mb-2"><i class="bi bi-person-plus me-2"></i>Tambah Pengguna Baru</h1>
                            <p class="text-muted mb-0">Buat akun pengguna/staff baru</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($flash_success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($flash_success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($flash_error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($flash_error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="content-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Informasi Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>pengguna/store" class="pengguna-form">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" id="username" name="username" class="form-control form-control-pengguna" placeholder="Contoh: john.doe" required>
                                    <small class="text-muted">Username untuk login (huruf, angka, dan titik)</small>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email" class="form-control form-control-pengguna" placeholder="contoh@email.com" required>
                                        <small class="text-muted">Email yang valid dan unik</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" id="password" name="password" class="form-control form-control-pengguna" placeholder="Minimal 6 karakter" required>
                                        <small class="text-muted">Minimal 6 karakter</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="id_role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select name="id_role" id="id_role" class="form-select" required>
                                        <option value="">Pilih Role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= $role['id_role'] ?>">
                                                <?= htmlspecialchars(ucfirst($role['nama_role'])) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="telepon" class="form-label">Telepon (Opsional)</label>
                                        <input type="tel" id="telepon" name="telepon" class="form-control form-control-pengguna" placeholder="Contoh: 08123456789">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="alamat" class="form-label">Alamat (Opsional)</label>
                                        <input type="text" id="alamat" name="alamat" class="form-control form-control-pengguna" placeholder="Contoh: Jl. Merdeka No. 123">
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>pengguna" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Tambah Pengguna
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="content-card info-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Role</h5>
                        </div>
                        <div class="card-body">
                            <div class="role-info">
                                <h6><i class="bi bi-shield-check text-danger me-2"></i>Admin</h6>
                                <p>Akses penuh ke semua fitur sistem</p>
                            </div>
                            <div class="role-info">
                                <h6><i class="bi bi-box-seam text-warning me-2"></i>Supplier</h6>
                                <p>Mengirim dan mengelola request pengiriman</p>
                            </div>
                            <div class="role-info">
                                <h6><i class="bi bi-person text-info me-2"></i>Staff</h6>
                                <p>Akses terbatas ke fitur-fitur tertentu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>