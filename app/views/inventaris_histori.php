<?php
// Extract flash messages
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Pengiriman - Manajemen Logistik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/inventaris_histori.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-dashboard">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>dashboard">
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
                                        alt="<?= htmlspecialchars($user_info['username'] ?? 'User') ?>" 
                                        class="avatar-img">
                                <?php else: ?>
                                    <i class="bi bi-person-fill"></i>
                                <?php endif; ?>
                            </div>
                            <span class="d-none d-md-inline"><?= htmlspecialchars($user_info['username'] ?? 'User') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>profile"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                            <hr class="dropdown-divider">
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="<?= BASE_URL ?>dashboard" class="sidebar-item"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a href="<?= BASE_URL ?>pengiriman" class="sidebar-item"><i class="bi bi-box-seam"></i><span>Pengiriman</span></a>
                <a href="<?= BASE_URL ?>inventaris_barang" class="sidebar-item"><i class="bi bi-boxes"></i><span>Inventaris Barang</span></a>
                <a href="<?= BASE_URL ?>inventaris_barang/histori" class="sidebar-item active"><i class="bi bi-clock-history"></i><span>Histori Pengiriman</span></a>
                <a href="<?= BASE_URL ?>supplier" class="sidebar-item"><i class="fa-solid fa-warehouse"></i><span>Supplier</span></a>
                <a href="<?= BASE_URL ?>pengguna" class="sidebar-item"><i class="bi bi-people"></i><span>Pengguna</span></a>
            </div>
        </aside>
        <main class="main-content">
            <div class="container-fluid px-4 py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 fw-bold mb-2">Histori Pengiriman Barang</h1>
                                <p class="text-muted mb-0">Daftar barang yang sudah dikirim</p>
                            </div>
                            <div>
                                <a href="<?= BASE_URL ?>inventaris_barang" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Inventaris
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($flash_success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flash_success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($flash_error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($flash_error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-muted mb-1">Total Barang Dikirim</h6>
                                        <h3 class="mb-0 fw-bold"><?= count($histori) ?></h3>
                                    </div>
                                    <div class="avatar-circle bg-success-light">
                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-muted mb-1">Total Berat (kg)</h6>
                                        <h3 class="mb-0 fw-bold">
                                            <?= number_format(array_sum(array_column($histori, 'berat')), 2) ?>
                                        </h3>
                                    </div>
                                    <div class="avatar-circle bg-primary-light">
                                        <i class="bi bi-box-seam-fill text-primary fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="text-muted mb-1">Total Quantity</h6>
                                        <h3 class="mb-0 fw-bold">
                                            <?= number_format(array_sum(array_column($histori, 'jumlah'))) ?>
                                        </h3>
                                    </div>
                                    <div class="avatar-circle bg-info-light">
                                        <i class="bi bi-boxes text-info fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="card content-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Daftar Histori Pengiriman</h5>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Cari barang...">
                                    <button class="btn btn-outline-secondary btn-sm" id="refreshBtn">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="historiTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>No Resi</th>
                                                <th>Nama Barang</th>
                                                <th>Berat (kg)</th>
                                                <th>Jumlah</th>
                                                <th>Deskripsi</th>
                                                <th>Status</th>
                                                <th>Tanggal Dikirim</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($histori)): ?>
                                                <?php foreach ($histori as $row): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['id_barang']) ?></td>
                                                        <td><?= htmlspecialchars($row['id_resi']) ?></td>
                                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                        <td><?= number_format($row['berat'], 2) ?></td>
                                                        <td><?= number_format($row['jumlah']) ?></td>
                                                        <td><?= htmlspecialchars(substr($row['deskripsi'], 0, 50)) . (strlen($row['deskripsi']) > 50 ? '...' : '') ?></td>
                                                        <td><span class="badge bg-success-light text-success">Sent</span></td>
                                                        <td><?= date('d M Y H:i', strtotime($row['updated_at'] ?? $row['created_at'])) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#detailModal"
                                                                onclick="showDetail(<?= htmlspecialchars(json_encode($row)) ?>)">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">
                                                        <i class="bi bi-clock-history fs-1 mb-3"></i><br>
                                                        Belum ada histori pengiriman barang
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
            </div>
        </main>
    </div>

    <!-- Modal Detail Barang -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be filled by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show detail modal
        function showDetail(data) {
            const content = `
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">ID Barang</th>
                        <td>${data.id_barang}</td>
                    </tr>
                    <tr>
                        <th>No Resi</th>
                        <td>${data.id_resi}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>${data.nama_barang}</td>
                    </tr>
                    <tr>
                        <th>Berat</th>
                        <td>${parseFloat(data.berat).toFixed(2)} kg</td>
                    </tr>
                    <tr>
                        <th>Jumlah</th>
                        <td>${parseInt(data.jumlah).toLocaleString()}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>${data.deskripsi || '-'}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge bg-success">Sent</span></td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td>${new Date(data.created_at).toLocaleString('id-ID')}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Dikirim</th>
                        <td>${new Date(data.updated_at || data.created_at).toLocaleString('id-ID')}</td>
                    </tr>
                </table>
            `;
            document.getElementById('detailContent').innerHTML = content;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Search client-side
            document.getElementById('searchInput').addEventListener('keyup', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#historiTable tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });

            // Refresh button
            document.getElementById('refreshBtn').addEventListener('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
    </script>
</body>
</html>