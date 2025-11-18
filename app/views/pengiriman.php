    <?php
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login');
        exit;
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
        <link rel="stylesheet" href="<?= BASE_URL ?>css/sticky-table.css">
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
                    <a href="<?= BASE_URL ?>dashboard" class="sidebar-item">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= BASE_URL ?>pengiriman" class="sidebar-item active">
                        <i class="bi bi-box-seam"></i>
                        <span>Pengiriman</span>
                    </a>
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
                </div>
            </aside>

            <main class="main-content">

                <div class="container-fluid px-4 py-4">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h1 class="h3 fw-bold mb-2">Selamat datang, <?php echo $_SESSION['username'] ?? 'User'; ?>!</h1>
                                    <p class="text-muted mb-0">silahkan lihat detail informasi pengiriman anda di sini</p>
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


                    <div class="d-flex justify-content-end mb-4">
                        <button class="btn btn-primary btn-sm shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i>Pengiriman Baru
                        </button>
                    </div>

                    <div class="content-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Daftar Pengiriman</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-sticky-first" style="min-width: 1200px;">
                                    <thead>
                                        <tr>
                                            <th>Nomor Resi</th>
                                            <th>Nama Pengirim</th>
                                            <th>Telp Pengirim</th>
                                            <th>Kota Asal</th>
                                            <th style="min-width: 300px;">Alamat Asal</th>
                                            <th>Nama Penerima</th>
                                            <th>Telp Penerima</th>
                                            <th>Kota Tujuan</th>
                                            <th style="min-width: 300px;">Alamat Tujuan</th>
                                            <th>Paket</th>
                                            <th>Berat (Kg)</th>
                                            <th>Jarak (Km)</th>
                                            <th style="min-width: 170px;">Ongkir</th>
                                            <th>Status Pengiriman</th>
                                            <th>Status Pembayaran</th>
                                            <th style="min-width: 170px;">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($detailResi)) : ?>
                                            <?php foreach ($detailResi as $row) : ?>
                                                <?php
                                                $statusKey = strtolower($row['status_pengiriman'] ?? '');
                                                $statusColor = 'secondary';
                                                if ($statusKey === 'terkirim') {
                                                    $statusColor = 'success';
                                                } elseif ($statusKey === 'dalam proses') {
                                                    $statusColor = 'warning';
                                                } elseif ($statusKey === 'tertunda') {
                                                    $statusColor = 'danger';
                                                }

                                                $paymentKey = strtolower($row['status_pembayaran'] ?? '');
                                                $paymentColor = $paymentKey === 'terbayar' ? 'success' : 'danger';
                                                $alamatAsal = $row['alamat_asal'] ?? '';
                                                $alamatAsalShort = strlen($alamatAsal) > 50 ? substr($alamatAsal, 0, 50) . '...' : $alamatAsal;

                                                $alamatTujuan = $row['alamat_tujuan'] ?? '';
                                                $alamatTujuanShort = strlen($alamatTujuan) > 50 ? substr($alamatTujuan, 0, 50) . '...' : $alamatTujuan;
                                                ?>
                                                <tr>
                                                    <td><code><?php echo htmlspecialchars($row['nomor_resi']); ?></code></td>
                                                    <td><?php echo htmlspecialchars($row['nama_pengirim']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['telpon_pengirim']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kota_pengirim']); ?></td>
                                                    <td><?php echo htmlspecialchars($alamatAsalShort); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_penerima']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['telpon_penerima']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kota_penerima']); ?></td>
                                                    <td><?php echo htmlspecialchars($alamatTujuanShort); ?></td>
                                                    <td><?php echo htmlspecialchars($row['jumlah_paket']); ?></td>
                                                    <td><?php echo !is_null($row['berat_total']) ? number_format($row['berat_total'], 2) : '-'; ?></td>
                                                    <td><?php echo !is_null($row['total_jarak']) ? number_format($row['total_jarak'], 2) : '-'; ?></td>
                                                    <td>
                                                        <?php if (!is_null($row['total_ongkir'])) : ?>
                                                            <span class="currency-badge">
                                                                <i class="bi bi-wallet2"></i>
                                                                <?php echo 'Rp ' . number_format($row['total_ongkir'], 0, ',', '.'); ?>
                                                            </span>
                                                        <?php else : ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $statusColor; ?>-light text-<?php echo $statusColor; ?>">
                                                            <?php echo !empty($row['status_pengiriman']) ? ucwords($row['status_pengiriman']) : '-'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $paymentColor; ?>-light text-<?php echo $paymentColor; ?>">
                                                            <?php echo !empty($row['status_pembayaran']) ? ucwords($row['status_pembayaran']) : '-'; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo !empty($row['tanggal_dibuat']) ? date('d M Y', strtotime($row['tanggal_dibuat'])) : '-'; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="16" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox me-1"></i> Belum ada data pengiriman.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>