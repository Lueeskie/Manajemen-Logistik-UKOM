    <?php
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    $role = $_SESSION['role'] ?? '';
    $isAdmin = $role === 'Admin';
    $isDriver = $role === 'Driver';
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
        <style>
            .btn-action {
                border: none;
                transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.05s ease;
            }

            .btn-action:hover {
                box-shadow: 0 0.4rem 1rem rgba(0, 0, 0, 0.08);
            }

            .btn-action:active {
                transform: translateY(1px);
            }

            .btn-action:focus {
                outline: none;
                box-shadow: 0 0 0 0.15rem rgba(53, 84, 245, 0.25);
            }

            .btn-action-primary {
                background-color: #3554f5;
                color: #fff;
            }

            .btn-action-primary:hover {
                background-color: #2d46c5;
                color: #fff;
            }

            .btn-action-warning {
                background-color: #f59f00;
                color: #1f1f1f;
            }

            .btn-action-warning:hover {
                background-color: #d98900;
                color: #1f1f1f;
            }

            .btn-action-danger {
                background-color: #e74c3c;
                color: #fff;
            }

            .btn-action-danger:hover {
                background-color: #c44133;
                color: #fff;
            }
        </style>
    </head>

    <body>
        <?php
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        $flashError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        $stats = $stats ?? ['total' => 0, 'terkirim' => 0, 'proses' => 0, 'tertunda' => 0];
        ?>
            <main class="main-content">

                <div class="container-fluid px-4 py-4">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
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


                    <?php if (!$isDriver): ?>
                        <div class="d-flex justify-content-end mb-4">
                            <a href="<?= BASE_URL ?>pengiriman/tambah">
                                <button class="btn btn-primary btn-sm shadow-sm">
                                    <i class="bi bi-plus-lg me-2"></i>Pengiriman Baru
                                </button>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="content-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Daftar Pengiriman</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-sticky-first" style="min-width: 1500px;">
                                    <thead>
                                        <tr>
                                            <th>Nomor Resi</th>
                                            <th style="min-width: 120px;">Nama Pengirim</th>
                                            <th>Telp Pengirim</th>
                                            <th>Kota Asal</th>
                                            <th>origin city code</th>
                                            <th style="min-width: 300px;">Alamat Asal</th>
                                            <th style="min-width: 400px;">Nama Penerima</th>
                                            <th>Telp Penerima</th>
                                            <th>Kota Tujuan</th>
                                            <th>destination city code</th>
                                            <th style="min-width: 300px;">Alamat Tujuan</th>
                                            <th>Paket</th>
                                            <th>Berat (Kg)</th>
                                            <th>Jarak (Km)</th>
                                            <th style="min-width: 170px;">Ongkir</th>
                                            <th>Status Pengiriman</th>
                                            <th>Status Pembayaran</th>
                                            <th style="min-width: 170px;">Tanggal</th>
                                            <th style="min-width: 220px;">Aksi</th>
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
                                                    <td><?php echo htmlspecialchars($row['origin_city_code']); ?></td>
                                                    <td><?php echo htmlspecialchars($alamatAsalShort); ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_penerima']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['telpon_penerima']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kota_penerima']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['destination_city_code']); ?></td>
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
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <a href="<?= BASE_URL ?>pengiriman/detail?id=<?php echo $row['id']; ?>" class="btn btn-action btn-action-primary d-flex align-items-center justify-content-center gap-2 px-3 py-2 shadow-sm" title="Detail">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <?php if ($isAdmin || $isDriver): ?>
                                                                <a href="<?= BASE_URL ?>pengiriman/edit?id=<?php echo $row['id']; ?>" class="btn btn-action btn-action-warning d-flex align-items-center justify-content-center gap-2 px-3 py-2 shadow-sm" title="Edit">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if ($isAdmin): ?>
                                                                <form action="<?= BASE_URL ?>pengiriman/delete" method="POST" class="m-0 form-delete-resi">
                                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                                    <button type="submit" class="btn btn-action btn-action-danger d-flex align-items-center justify-content-center gap-2 px-3 py-2 shadow-sm" title="Hapus">
                                                                        <i class="bi bi-trash3"></i>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="19" class="text-center text-muted py-4">
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
        <?php if (!empty($flashSuccess)) : ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '<?= addslashes($flashSuccess) ?>',
                    timer: 2500,
                    showConfirmButton: false
                });
            </script>
        <?php endif; ?>
        <?php if (!empty($flashError)) : ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '<?= addslashes($flashError) ?>'
                });
            </script>
        <?php endif; ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const deleteForms = document.querySelectorAll('.form-delete-resi');
                deleteForms.forEach(form => {
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Hapus resi ini?',
                            text: 'Tindakan ini tidak dapat dibatalkan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e74c3c',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    </body>

    </html>
