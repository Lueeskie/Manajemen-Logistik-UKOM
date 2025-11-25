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
                            <?php if (($_SESSION['role'] ?? '') === 'Supplier'): ?>
                                <a href="<?= BASE_URL ?>supplier/tambah" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Request
                                </a>
                            <?php endif; ?>
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
                                                <th>Nama Paket</th>
                                                <th>Jumlah</th>
                                                <th>Berat (kg)</th>
                                                <th>Alamat Asal</th>
                                                <th>Alamat Tujuan</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($requests as $r): ?>
                                            <tr>
                                                <td><code>#<?= str_pad($r['id_request'], 4, '0', STR_PAD_LEFT) ?></code></td>
                                                <td class="text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($r['nama_barang'] ?? '-') ?>">
                                                    <?= htmlspecialchars($r['nama_barang'] ?? 'Tidak ada nama barang') ?>
                                                </td>
                                                <td><span class="badge bg-primary-soft"><?= $r['jumlah_barang'] ?></span></td>
                                                <td><span class="badge bg-primary-soft"><?= $r['berat'] ?> kg</span></td>
                                                <td class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($r['alamat_asal'] ?? '-') ?>">
                                                    <?= htmlspecialchars(strlen($r['alamat_asal'] ?? '') > 30 ? substr($r['alamat_asal'], 0, 30).'...' : ($r['alamat_asal'] ?? '-')) ?>
                                                </td>
                                                <td class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($r['alamat_tujuan'] ?? '-') ?>">
                                                    <?= htmlspecialchars(strlen($r['alamat_tujuan'] ?? '') > 30 ? substr($r['alamat_tujuan'], 0, 30).'...' : ($r['alamat_tujuan'] ?? '-')) ?>
                                                </td>
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
                                                        } elseif ($status == 'Ditolak') {
                                                            $statusClass = 'danger';
                                                            $statusIcon = 'x-circle';
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
                                                <td style="min-width: 200px;"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-action" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-gear"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detailModal<?= $r['id_request'] ?>">
                                                                    Detail
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <?php 
                                                                $status = $r['status'] ?? 'Menunggu Konfirmasi';
                                                                $isLocked = ($status === 'Sampai ke Tujuan');
                                                                ?>
                                                                <?php if ($isLocked): ?>
                                                                    <a class="dropdown-item text-muted" href="#" onclick="return false;">
                                                                        Edit <small class="text-muted">(<?= $status ?>)</small>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <a class="dropdown-item" href="<?= BASE_URL ?>supplier/edit/<?= $r['id_request'] ?>">
                                                                        Edit
                                                                    </a>
                                                                <?php endif; ?>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                                                                    <?php 
                                                                    $status = $r['status'] ?? 'Menunggu Konfirmasi';
                                                                    if ($status === 'Dikirim'): ?>
                                                                        <a class="dropdown-item text-muted" href="#" onclick="return false;">
                                                                            Hapus <small class="text-muted">(Sudah Dikirim)</small>
                                                                        </a>
                                                                    <?php elseif ($status === 'Sampai ke Tujuan'): ?>
                                                                        <a class="dropdown-item text-muted" href="#" onclick="return false;">
                                                                            Hapus <small class="text-muted">(Sudah Sampai)</small>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <a class="dropdown-item text-danger" href="#" onclick="hapusRequest(<?= $r['id_request'] ?>, '<?= $status ?>'); return false;">
                                                                            Hapus
                                                                        </a>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
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
    <?php if (!empty($requests)): ?>
    <?php foreach ($requests as $r): ?>
        <?php
        $status = $r['status'] ?? 'Menunggu Konfirmasi';
        if ($status == 'Menunggu Konfirmasi') {
            $modalStatusClass = 'warning';
            $modalStatusIcon = 'clock';
        } elseif ($status == 'Dikonfirmasi') {
            $modalStatusClass = 'info';
            $modalStatusIcon = 'check-circle';
        } elseif ($status == 'Dikirim') {
            $modalStatusClass = 'primary';
            $modalStatusIcon = 'truck';
        } elseif ($status == 'Sampai ke Tujuan') {
            $modalStatusClass = 'success';
            $modalStatusIcon = 'box-seam';
        } elseif ($status == 'Ditolak') {
            $modalStatusClass = 'danger';
            $modalStatusIcon = 'x-circle';
        } else {
            $modalStatusClass = 'secondary';
            $modalStatusIcon = '';
        }
        ?>
        <div class="modal fade" id="detailModal<?= $r['id_request'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-file-text-fill"></i>
                            Detail Request #<?= str_pad($r['id_request'], 4, '0', STR_PAD_LEFT) ?>
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="info-box">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><i class="bi bi-box-seam me-2"></i>Nama Paket</strong>
                                    <p class="mb-0 mt-1"><?= htmlspecialchars($r['nama_barang'] ?? '-') ?></p>
                                </div>
                                <span class="badge bg-<?= $modalStatusClass ?>-soft">
                                    <?php if ($modalStatusIcon): ?>
                                        <i class="bi bi-<?= $modalStatusIcon ?> me-1"></i>
                                    <?php endif; ?>
                                    <?= $status ?>
                                </span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <strong><i class="bi bi-123 me-2"></i>Jumlah Barang</strong>
                                    <p class="mb-0"><?= $r['jumlah_barang'] ?> unit</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <strong><i class="bi bi-weight me-2"></i>Berat Total</strong>
                                    <p class="mb-0"><?= $r['berat'] ?> kg</p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="detail-item">
                                    <strong><i class="bi bi-calendar-event me-2"></i>Tanggal Dibuat</strong>
                                    <p class="mb-0"><?= date('d F Y, H:i', strtotime($r['created_at'])) ?> WIB</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <strong><i class="bi bi-geo-alt me-2"></i>Alamat Asal</strong>
                                <p class="mb-0 mt-2"><small><?= nl2br(htmlspecialchars($r['alamat_asal'] ?? 'Tidak diisi')) ?></small></p>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="bi bi-geo-alt-fill me-2"></i>Alamat Tujuan</strong>
                                <p class="mb-0 mt-2"><small><?= nl2br(htmlspecialchars($r['alamat_tujuan'] ?? 'Tidak diisi')) ?></small></p>
                            </div>
                        </div>
                        <hr>

                        <div>
                            <strong><i class="bi bi-file-text me-2"></i>Deskripsi Paket</strong>
                            <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($r['deskripsi'] ?? 'Tidak ada deskripsi')) ?></p>
                        </div>

                        <?php if (!empty($r['foto_paket'])): ?>
                            <hr>
                            <div class="text-center">
                                <strong><i class="bi bi-image me-2"></i>Foto Paket</strong>
                                <div class="mt-3">
                                    <img src="<?= BASE_URL ?>uploads/paket/<?= htmlspecialchars($r['foto_paket']) ?>" 
                                         class="img-fluid" 
                                         style="max-height: 450px; object-fit: contain;" 
                                         alt="Foto Paket">
                                </div>
                            </div>
                        <?php else: ?>
                            <hr>
                            <div class="text-center py-4" style="background: rgba(100, 116, 139, 0.05); border-radius: 12px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 mt-2"><em>Belum ada foto paket</em></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        function hapusRequest(requestId, status = 'Menunggu Konfirmasi') {
            let title = 'Hapus Request?';
            let text = 'Apakah Anda yakin ingin menghapus request pengiriman ini?';
            let allowDelete = true;

            if (status === 'Dikirim') {
                title = 'Tidak Dapat Menghapus!';
                text = 'Request barang sudah dalam perjalanan.';
                allowDelete = false;
            } else if (status === 'Sampai ke Tujuan') {
                title = 'Tidak Dapat Menghapus!';
                text = 'Request barang sudah sampai ke tujuan.';
                allowDelete = false;
            }

            Swal.fire({
                title: title,
                text: text,
                icon: allowDelete ? 'warning' : 'error',
                showCancelButton: allowDelete,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: allowDelete ? 'Ya, Hapus!' : 'OK',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && allowDelete) {
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