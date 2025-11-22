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
    <title>Supplier - Astral Express</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Riwayat Request Pengiriman</h5>
                                <div class="search-box">
                                    <input type="text" id="searchInput" class="form-control form-control-search" placeholder="Cari nama barang...">
                                    <i class="bi bi-search"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($requests)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="requestTable">
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
                                                <td><span class="badge bg-primary-soft"><?= $r['berat'] ?> kg</span></td>
                                                <td>
                                                    <?php 
                                                        $status = $r['status'] ?? 'Menunggu Konfirmasi';
                                                        
                                                        if ($status == 'Menunggu Konfirmasi') {
                                                            $statusClass = 'warning';
                                                            $statusIcon = 'clock';
                                                        } elseif ($status == 'Dikonfirmasi') {
                                                            $statusClass = 'info';
                                                            $statusIcon = 'check-circle';
                                                        } elseif ($status == 'Dikirim') {
                                                            $statusClass = 'primary';
                                                            $statusIcon = 'truck';
                                                        } elseif ($status == 'Sampai ke Tujuan') {
                                                            $statusClass = 'success';
                                                            $statusIcon = 'box-seam';
                                                        } else {
                                                            $statusClass = 'secondary';
                                                            $statusIcon = '';
                                                        }
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>-soft">
                                                        <?php if ($statusIcon): ?>
                                                            <i class="bi bi-<?= $statusIcon ?> me-1"></i>
                                                        <?php endif; ?>
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                                <td style="min-width: 180px;"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-action" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-gear"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="<?= BASE_URL ?>supplier/edit/<?= $r['id_request'] ?>">
                                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" onclick="hapusRequest(<?= $r['id_request'] ?>); return false;">
                                                                    <i class="bi bi-trash me-2"></i>Hapus
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
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
        function hapusRequest(requestId) {
            Swal.fire({
                title: 'Hapus Request?',
                text: 'Apakah Anda yakin ingin menghapus request pengiriman ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= BASE_URL ?>supplier/hapus/' + requestId;
                }
            });
        }
        
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('#requestTable tbody tr');
            
            tableRows.forEach(row => {
                let namaBarang = row.cells[1].textContent.toLowerCase();
                if (namaBarang.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

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