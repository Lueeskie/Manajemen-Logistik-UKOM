<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$request = $request ?? null;
if (!$request) {
    header('Location: ' . BASE_URL . 'supplier');
    exit;
}
$status = $request['status'] ?? 'Menunggu Konfirmasi';
$isAdmin = ($_SESSION['role'] ?? '') === 'Admin';
$canEditData = !in_array($status, ['Dikirim', 'Sampai ke Tujuan']);
$canChangeStatus = in_array($status, ['Menunggu Konfirmasi', 'Dikonfirmasi', 'Dikirim']);
$isFinal = $status === 'Sampai ke Tujuan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Request - Astral Express</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                            <h1 class="h3 fw-bold mb-2"><i class="bi bi-pencil-square me-2"></i>Edit Request Pengiriman</h1>
                            <p class="text-muted mb-0">Ubah data request pengiriman barang</p>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Informasi Paket</h5>
                                <small class="text-muted">ID Request: <code>#<?= str_pad($request['id_request'], 4, '0', STR_PAD_LEFT) ?></code></small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>supplier/update/<?= $request['id_request'] ?>" class="tambah-form">
                                <div class="mb-4">
                                    <label for="nama_barang" class="form-label">Nama Barang (Bisa diisi Nama Barang)  <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_barang" id="nama_barang" class="form-control form-control-supplier" 
                                           value="<?= htmlspecialchars($request['nama_barang']) ?>" 
                                           <?= $canEditData ? 'required' : 'disabled' ?>>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="jumlah_barang" class="form-label">Jumlah Barang <span class="text-danger">*</span></label>
                                        <input type="number" name="jumlah_barang" id="jumlah_barang" class="form-control form-control-supplier" 
                                               value="<?= $request['jumlah_barang'] ?>" min="1"
                                               <?= $canEditData ? 'required' : 'disabled' ?>>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="berat" class="form-label">Berat Total (kg) <span class="text-danger">*</span></label>
                                        <input type="number" name="berat" id="berat" class="form-control form-control-supplier" step="0.01"
                                               value="<?= $request['berat'] ?>"
                                               <?= $canEditData ? 'required' : 'disabled' ?>>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Alamat Asal (Gudang/Pengambilan) <span class="text-danger">*</span></label>
                                    <textarea name="alamat_asal" class="form-control form-control-supplier" rows="2" 
                                              <?= $canEditData ? 'required' : 'disabled' ?>><?= htmlspecialchars($request['alamat_asal'] ?? '') ?></textarea>
                                    <small class="text-muted">Alamat lokasi pengambilan barang</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Alamat Tujuan (Penerima) <span class="text-danger">*</span></label>
                                    <textarea name="alamat_tujuan" class="form-control form-control-supplier" rows="3" 
                                              <?= $canEditData ? 'required' : 'disabled' ?>><?= htmlspecialchars($request['alamat_tujuan'] ?? '') ?></textarea>
                                    <small class="text-muted">Alamat lengkap penerima barang</small>
                                </div>

                                <div class="mb-4">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control form-control-supplier" rows="4"
                                              <?= $canEditData ? '' : 'disabled' ?>><?= htmlspecialchars($request['deskripsi'] ?? '') ?></textarea>
                                </div>

                                <?php if ($isAdmin): ?>
                                <div class="mb-4">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select form-control-supplier" 
                                            <?= $isFinal ? 'disabled' : '' ?> required>
                                        <option value="">Pilih Status</option>

                                        <?php if ($canEditData): ?>
                                            <option value="Menunggu Konfirmasi" <?= $status == 'Menunggu Konfirmasi' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                                            <option value="Dikonfirmasi" <?= $status == 'Dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                            <option value="Ditolak" <?= $status == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                        <?php endif; ?>

                                        <option value="Dikirim" <?= $status == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                        <?php if (!$isFinal): ?>
                                            <option value="Sampai ke Tujuan" <?= $status == 'Sampai ke Tujuan' ? 'selected' : '' ?>>Sampai ke Tujuan</option>
                                        <?php endif; ?>
                                    </select>
                                        
                                    <?php if ($isFinal): ?>
                                        <div class="mt-2">
                                            <small class="text-danger"><i class="bi bi-lock-fill"></i> Status sudah final. Data tidak dapat diubah lagi.</small>
                                        </div>
                                    <?php elseif ($status === 'Dikirim'): ?>
                                        <div class="mt-2">
                                            <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Data barang tidak dapat diubah lagi. Hanya status yang bisa diganti.</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($canEditData): ?>
                                    <div class="mb-4">
                                        <label class="form-label">Foto Paket (Opsional)</label>
                                        <input type="file" name="foto_paket" class="form-control" accept="image/*">
                                        <small class="text-muted">Max 2MB. Biarkan kosong jika tidak ingin ganti.</small>
                                        
                                        <?php if (!empty($request['foto_paket'])): ?>
                                            <div class="mt-3">
                                                <p><strong>Foto Saat Ini:</strong></p>
                                                <img src="<?= BASE_URL ?>uploads/paket/<?= htmlspecialchars($request['foto_paket']) ?>" 
                                                     class="img-thumbnail" style="max-height: 200px;" alt="Foto Paket">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                    
                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>supplier" class="btn btn-outline-secondary">
                                        Batal
                                    </a>
                                    <?php if (!$isFinal): ?>
                                        <button type="submit" class="btn btn-primary">
                                            Simpan Perubahan
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-primary" disabled>
                                            Tidak Dapat Disimpan
                                        </button>
                                    <?php endif; ?>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () { 
            <?php if ($flash_success): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: <?= json_encode($flash_success) ?>,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3554f5'
                }).then(() => {
                    window.location.href = '<?= BASE_URL ?>supplier';
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
        function confirmFinalStatus() {
            const select = document.getElementById('status');
            if (!select) return true;

            const newStatus = select.value;
            const currentStatus = '<?= $status ?>';
            
            if (newStatus === 'Dikirim' && !['Dikirim', 'Sampai ke Tujuan'].includes(currentStatus)) {
                return confirm("PERINGATAN!\n\nSetelah status menjadi 'Dikirim', Anda TIDAK DAPAT mengubah data barang lagi (nama, jumlah, berat, deskripsi).\n\nApakah Anda yakin?");
            }

            if (newStatus === 'Sampai ke Tujuan' && currentStatus !== 'Sampai ke Tujuan') {
                return confirm("PERINGATAN FINAL!\n\nSetelah status menjadi 'Sampai ke Tujuan', SEMUA DATA akan terkunci selamanya.\nTidak ada lagi yang bisa diubah.\n\nApakah Anda benar-benar yakin?");
            }
        
            return true;
        }
    </script>
</body>
</html>