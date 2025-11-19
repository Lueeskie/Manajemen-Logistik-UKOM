<?php

include "header.php";

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
    <title>Supplier - Astral Express</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <main class="main-content">
        <div class="container-fluid px-4 py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 fw-bold mb-2"><i class="fa-solid fa-warehouse me-2"></i>Kelola Supplier</h1>
                            <p class="text-muted mb-0">Kelola permintaan pengiriman barang dari supplier</p>
                        </div>
                        <div>
                            <a href="<?= BASE_URL ?>supplier/tambah" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Request
                            </a>
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
                <div class="col-12">
                    <div class="content-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Riwayat Request Pengiriman</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($requests)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Request</th>
                                                <th>Nama Barang</th>
                                                <th>Jumlah</th>
                                                <th>Berat (kg)</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($requests as $r): ?>
                                            <tr>
                                                <td><code>#<?= str_pad($r['id_request'], 4, '0', STR_PAD_LEFT) ?></code></td>
                                                <td><?= htmlspecialchars($r['nama_barang']) ?></td>
                                                <td><span class="badge bg-primary-soft"><?= $r['jumlah_barang'] ?></span></td>
                                                <td><?= $r['berat'] ?></td>
                                                <td>
                                                    <?php 
                                                        $status = $r['status'] ?? 'Menunggu Konfirmasi';
                                                        $statusClass = $status == 'Menunggu Konfirmasi' ? 'warning' : 'success';
                                                        $statusIcon = $status == 'Menunggu Konfirmasi' ? 'clock' : '';
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>-soft">
                                                        <?php if ($statusIcon): ?>
                                                            <i class="bi bi-<?= $statusIcon ?> me-1"></i>
                                                        <?php endif; ?>
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada request pengiriman</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () { 
            <?php if ($flash_success): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: <?= json_encode($flash_success) ?>,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3554f5'
                });
            <?php endif; ?>

            <?php if ($flash_error): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: <?= json_encode($flash_error) ?>,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>