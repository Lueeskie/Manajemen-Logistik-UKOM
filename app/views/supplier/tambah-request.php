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
    <title>Tambah Request - Astral Express</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/supplier-tambah.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <main class="main-content">
        <div class="container-fluid px-4 py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <a href="<?= BASE_URL ?>supplier" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="h3 fw-bold mb-2"><i class="bi bi-plus-circle me-2"></i>Tambah Request Pengiriman</h1>
                            <p class="text-muted mb-0">Buat permintaan pengiriman barang baru</p>
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
                            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Informasi Barang</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>supplier/proses-tambah" class="tambah-form">
                                <div class="mb-4">
                                    <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" id="nama_barang" name="nama_barang" class="form-control form-control-supplier" placeholder="Contoh: Laptop ASUS Vivobook 14" required>
                                    <small class="text-muted">Masukkan nama barang yang akan dikirim</small>
                                </div>

                                <input type="hidden" name="id_supplier" value="<?php echo $_SESSION['user_id']; ?>">

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="jumlah_barang" class="form-label">Jumlah Barang <span class="text-danger">*</span></label>
                                        <input type="number" id="jumlah_barang" name="jumlah_barang" class="form-control form-control-supplier" min="1" placeholder="Contoh: 5" required>
                                        <small class="text-muted">Jumlah unit barang</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="berat" class="form-label">Berat Total (kg) <span class="text-danger">*</span></label>
                                        <input type="number" id="berat" name="berat" class="form-control form-control-supplier" step="0.01" placeholder="Contoh: 2.5" required>
                                        <small class="text-muted">Berat total semua barang</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                                    <textarea id="deskripsi" name="deskripsi" class="form-control form-control-supplier" rows="4" placeholder="Catatan tambahan, misal: barang mudah pecah, butuh packing kayu, sudah diasuransikan, dll."></textarea>
                                    <small class="text-muted">Informasi tambahan yang penting untuk pengiriman</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>supplier" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Kirim Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="content-card info-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Penting</h5>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <i class="bi bi-clock-history"></i>
                                <div>
                                    <h6>Waktu Proses</h6>
                                    <p>Request akan diproses dalam 1-2 jam kerja</p>
                                </div>
                            </div>
                            <div class="info-box">
                                <i class="bi bi-shield-check"></i>
                                <div>
                                    <h6>Keamanan Data</h6>
                                    <p>Semua data barang dijaga dengan aman</p>
                                </div>
                            </div>
                            <div class="info-box">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <h6>Hubungi Support</h6>
                                    <p>Ada pertanyaan? Hubungi admin kami</p>
                                </div>
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