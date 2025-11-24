<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');
$isAdmin = $role === 'admin';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Resi - Manajemen Logistik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <style>
        .dl-compact dt,
        .dl-compact dd {
            margin-bottom: 0.1rem;
        }
    </style>
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
                <a href="<?= BASE_URL ?>dashboard" class="sidebar-item">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>pengiriman" class="sidebar-item active">
                    <i class="bi bi-box-seam"></i>
                    <span>Pengiriman</span>
                </a>
                <?php if ($isAdmin): ?>
                    <a href="#" class="sidebar-item disabled">
                        <i class="bi bi-boxes"></i>
                        <span>Inventaris Barang</span>
                    </a>
                    <a href="#" class="sidebar-item disabled">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>Supplier</span>
                    </a>
                    <a href="#" class="sidebar-item disabled">
                        <i class="bi bi-people"></i>
                        <span>Pengguna</span>
                    </a>
                <?php endif; ?>
            </div>
        </aside>

        <main class="main-content">
            <div class="container-fluid px-4 py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Detail Resi</h1>
                        <p class="text-muted mb-0">Nomor Resi: <code><?php echo htmlspecialchars($resi['nomor_resi']); ?></code></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= BASE_URL ?>pengiriman" class="btn btn-light border">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <?php if ($isAdmin): ?>
                            <a href="<?= BASE_URL ?>pengiriman/edit?id=<?php echo $resi['id']; ?>" class="btn btn-primary">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="content-card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Pengirim</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0 dl-compact">
                                    <dt class="col-sm-4 text-muted small">Nama</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($resi['nama_pengirim']); ?></dd>
                                    <dt class="col-sm-4 text-muted small">Telepon</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($resi['telpon_pengirim']); ?></dd>
                                    <dt class="col-sm-4 text-muted small">Kota</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($resi['kota_pengirim']); ?> (<?php echo htmlspecialchars($resi['origin_city_code']); ?>)</dd>
                                    <dt class="col-sm-4 text-muted small">Alamat</dt>
                                    <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($resi['alamat_asal'])); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="content-card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Penerima</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0 dl-compact">
                                    <dt class="col-sm-4 text-muted small">Nama</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($resi['nama_penerima']); ?></dd>
                                    <dt class="col-sm-4 text-muted small">Telepon</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($resi['telpon_penerima']); ?></dd>
                                    <dt class="col-sm-4 text-muted small">Kota</dt>
                                    <dd class="col-sm-8"><?php echo htmlspecialchars($resi['kota_penerima']); ?> (<?php echo htmlspecialchars($resi['destination_city_code']); ?>)</dd>
                                    <dt class="col-sm-4 text-muted small">Alamat</dt>
                                    <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($resi['alamat_tujuan'])); ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-lg-6">
                        <div class="content-card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Detail Paket</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0 dl-compact">
                                    <dt class="col-sm-5 text-muted small">Jumlah Paket</dt>
                                    <dd class="col-sm-7"><?php echo htmlspecialchars($resi['jumlah_paket']); ?></dd>
                                    <dt class="col-sm-5 text-muted small">Berat Total</dt>
                                    <dd class="col-sm-7">
                                        <?php if (!is_null($resi['berat_total'])): ?>
                                            <span class="text-primary fw-semibold"><?php echo rtrim(rtrim(number_format($resi['berat_total'], 2), '0'), '.'); ?> kg</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </dd>
                                    <dt class="col-sm-5 text-muted small">Jarak</dt>
                                    <dd class="col-sm-7">
                                        <?php if (!is_null($resi['total_jarak'])): ?>
                                            <span class="text-success fw-semibold"><?php echo rtrim(rtrim(number_format($resi['total_jarak'], 2), '0'), '.'); ?> km</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </dd>
                                    <dt class="col-sm-5 text-muted small">Ongkir</dt>
                                    <dd class="col-sm-7">
                                        <?php if (!is_null($resi['total_ongkir'])): ?>
                                            <span class="text-danger fw-semibold">Rp <?php echo number_format($resi['total_ongkir'], 0, ',', '.'); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="content-card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">Status</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0 dl-compact">
                                    <dt class="col-sm-5 text-muted small">Pengiriman</dt>
                                    <dd class="col-sm-7">
                                        <?php
                                        $statKey = strtolower($resi['status_pengiriman'] ?? '');
                                        $statClass = 'text-secondary';
                                        if ($statKey === 'terkirim') {
                                            $statClass = 'text-success';
                                        } elseif ($statKey === 'dalam proses') {
                                            $statClass = 'text-warning';
                                        } elseif ($statKey === 'tertunda') {
                                            $statClass = 'text-danger';
                                        }
                                        ?>
                                        <span class="fw-semibold <?php echo $statClass; ?>">
                                            <?php echo !empty($resi['status_pengiriman']) ? ucwords($resi['status_pengiriman']) : '-'; ?>
                                        </span>
                                    </dd>
                                    <dt class="col-sm-5 text-muted small">Pembayaran</dt>
                                    <dd class="col-sm-7">
                                        <?php
                                        $payKey = strtolower($resi['status_pembayaran'] ?? '');
                                        $payClass = $payKey === 'terbayar' ? 'text-success' : 'text-danger';
                                        ?>
                                        <span class="fw-semibold <?php echo $payClass; ?>">
                                            <?php echo !empty($resi['status_pembayaran']) ? ucwords($resi['status_pembayaran']) : 'Belum Bayar'; ?>
                                        </span>
                                    </dd>
                                    <dt class="col-sm-5 text-muted small">Tanggal</dt>
                                    <dd class="col-sm-7"><?php echo !empty($resi['tanggal_dibuat']) ? date('d M Y', strtotime($resi['tanggal_dibuat'])) : '-'; ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
