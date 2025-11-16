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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manajemen Logistik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white navbar-dashboard">
        <div class="container-fluid px-4">
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="d-none d-md-inline"><?php echo $_SESSION['username']; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="#" class="sidebar-item active">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="bi bi-box-seam"></i>
                    <span>Pengiriman</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="bi bi-boxes"></i>
                    <span>Stok Barang</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="bi bi-graph-up"></i>
                    <span>Laporan</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="bi bi-people"></i>
                    <span>Pengguna</span>
                </a>
            </div>
        </aside>
        <main class="main-content">
            <div class="container-fluid px-4 py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 fw-bold mb-2">Selamat datang, <?php echo $_SESSION['username']; ?>! 👋</h1>
                                <p class="text-muted mb-0">Kelola distribusi barang dan pantau stok Anda di sini</p>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg me-2"></i>Pengiriman Baru
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4 g-3">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary-light">
                                <i class="bi bi-box-seam text-primary"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Total Pengiriman</p>
                                <h4 class="stat-value">1,234</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success-light">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Terkirim</p>
                                <h4 class="stat-value">890</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning-light">
                                <i class="bi bi-clock-history text-warning"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Dalam Proses</p>
                                <h4 class="stat-value">234</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-danger-light">
                                <i class="bi bi-exclamation-circle text-danger"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Tertunda</p>
                                <h4 class="stat-value">110</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card content-card">
                            <div class="card-header">
                                <h5 class="mb-0">Pengiriman Terbaru</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>No. Tracking</th>
                                                <th>Tujuan</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><code>PKG-001234</code></td>
                                                <td>Jakarta</td>
                                                <td><span class="badge bg-success-light text-success">Terkirim</span></td>
                                                <td>12 Nov 2025</td>
                                            </tr>
                                            <tr>
                                                <td><code>PKG-001233</code></td>
                                                <td>Surabaya</td>
                                                <td><span class="badge bg-warning-light text-warning">Dalam Proses</span></td>
                                                <td>11 Nov 2025</td>
                                            </tr>
                                            <tr>
                                                <td><code>PKG-001232</code></td>
                                                <td>Bandung</td>
                                                <td><span class="badge bg-success-light text-success">Terkirim</span></td>
                                                <td>10 Nov 2025</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card content-card">
                            <div class="card-header">
                                <h5 class="mb-0">Ringkasan Performa</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">Tingkat Pengiriman Tepat Waktu</span>
                                        <span class="small fw-600">92%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 92%"></div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">Kapasitas Stok</span>
                                        <span class="small fw-600">78%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: 78%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">Kepuasan Pelanggan</span>
                                        <span class="small fw-600">95%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-info" style="width: 95%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (!empty($flash_success)) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: <?= json_encode($flash_success) ?>,
                confirmButtonColor: '#3554f5'
            });
        <?php endif; ?>
    </script>
</body>
</html>