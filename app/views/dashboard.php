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

$stats = $stats ?? ['total' => 0, 'terkirim' => 0, 'proses' => 0, 'tertunda' => 0];
$recentResi = $recentResi ?? [];

$role = strtolower($_SESSION['role'] ?? '');
$isAdmin = $role === 'admin';
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white navbar-dashboard">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="<?= BASE_URL ?>assets/logo2.png" alt="Astral Express" class="brand-logo">
                <span class="brand-text">AstralExpress</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle">
                                <?php if (!empty($user_info['foto'])): ?>
                                    <img src="<?= BASE_URL ?>uploads/profile/<?= htmlspecialchars($user_info['foto']) ?>"
                                        alt="<?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>"
                                        class="avatar-img">
                                <?php else: ?>
                                    <i class="bi bi-person-fill"></i>
                                <?php endif; ?>
                            </div>
                            <span class="d-none d-md-inline"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>profile"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
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
                <a href="<?= BASE_URL ?>dashboard" class="sidebar-item active" data-nav="dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>pengiriman" class="sidebar-item" data-nav="pengiriman">
                    <i class="bi bi-box-seam"></i>
                    <span>Pengiriman</span>
                </a>
                <?php if ($isAdmin): ?>
                    <a href="#" class="sidebar-item">
                        <i class="bi bi-boxes"></i>
                        <span>Inventaris Barang</span>
                    </a>
                    <a href="#" class="sidebar-item">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>Supplier</span>
                    </a>
                    <a href="#" class="sidebar-item">
                        <i class="bi bi-people"></i>
                        <span>Pengguna</span>
                    </a>
                <?php endif; ?>
            </div>
        </aside>
        <main class="main-content">
            <div class="container-fluid px-4 py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 fw-bold mb-2">Selamat datang, <?php echo $_SESSION['username'] ?? 'User'; ?>!</h1>
                                <p class="text-muted mb-0">Kelola distribusi barang dan pantau stok Anda di sini</p>
                            </div>
                            <div>
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
                                <h4 class="stat-value"><?php echo $stats['total']; ?></h4>
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
                                <h4 class="stat-value"><?php echo $stats['terkirim']; ?></h4>
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
                                <h4 class="stat-value"><?php echo $stats['proses']; ?></h4>
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
                                <h4 class="stat-value"><?php echo $stats['tertunda']; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3" id="pengiriman-section">
                    <div class="col-lg-12">
                        <div class="card content-card">
                            <div class="card-header">
                                <h5 class="mb-0">Pengiriman Terbaru</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nomor Resi</th>
                                                <th>Penerima</th>
                                                <th>Tujuan</th>
                                                <th>Status Pengiriman</th>
                                                <th>Pembayaran</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recentResi)) : ?>
                                                <?php foreach ($recentResi as $row) : ?>
                                                    <?php
                                                    $status_color = 'secondary';
                                                    if ($row['status_pengiriman'] == 'Terkirim') $status_color = 'success';
                                                    elseif ($row['status_pengiriman'] == 'Dalam Proses') $status_color = 'warning';
                                                    elseif ($row['status_pengiriman'] == 'Tertunda') $status_color = 'danger';

                                                    $payment_color = 'secondary';
                                                    if ($row['status_pembayaran'] == 'Terbayar') $payment_color = 'success';
                                                    elseif ($row['status_pembayaran'] == 'Belum Bayar') $payment_color = 'danger';
                                                    ?>
                                                    <tr>
                                                        <td><code><?php echo $row['nomor_resi']; ?></code></td>
                                                        <td><?php echo htmlspecialchars($row['nama_penerima']); ?></td>
                                                        <td><?php echo htmlspecialchars(substr($row['alamat_tujuan'], 0, 30)) . (strlen($row['alamat_tujuan']) > 30 ? '...' : ''); ?></td>
                                                        <td><span class="badge bg-<?php echo $status_color; ?>-light text-<?php echo $status_color; ?>"><?php echo htmlspecialchars($row['status_pengiriman']); ?></span></td>
                                                        <td><span class="badge bg-<?php echo $payment_color; ?>-light text-<?php echo $payment_color; ?>"><?php echo htmlspecialchars($row['status_pembayaran']); ?></span></td>
                                                        <td><?php echo date('d M Y', strtotime($row['tanggal_dibuat'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <i class="bi bi-inbox"></i> Belum ada resi pengiriman
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const sidebarItems = document.querySelectorAll('.sidebar-menu .sidebar-item');
                        const pengirimanSection = document.getElementById('pengiriman-section');

                        function setActive(navName) {
                            sidebarItems.forEach(item => {
                                const itemNav = item.getAttribute('data-nav');
                                if (itemNav) {
                                    item.classList.toggle('active', itemNav === navName);
                                }
                            });
                        }

                        sidebarItems.forEach(item => {
                            const target = item.getAttribute('href');
                            if (target && target.startsWith('#')) {
                                item.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const destination = document.querySelector(target);
                                    if (destination) {
                                        destination.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'start'
                                        });
                                        history.replaceState(null, '', target);
                                        setActive(this.getAttribute('data-nav'));
                                    }
                                });
                            }
                        });

                        if (window.location.hash === '#pengiriman-section' && pengirimanSection) {
                            pengirimanSection.scrollIntoView({
                                behavior: 'auto',
                                block: 'start'
                            });
                            setActive('pengiriman');
                        } else {
                            setActive('dashboard');
                        }
                    });

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
