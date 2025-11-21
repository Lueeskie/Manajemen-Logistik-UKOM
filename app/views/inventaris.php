<?php
// Extract flash messages
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Edit mode handling
$edit_item = $edit_item ?? null;
$edit_mode = !empty($edit_item);
if ($edit_mode) {
    $id_resi = $edit_item['id_resi'] ?? '';
    $nama_barang = $edit_item['nama_barang'] ?? '';
    $berat = $edit_item['berat'] ?? 0.00;
    $jumlah = $edit_item['jumlah'] ?? 0;
    $deskripsi = $edit_item['deskripsi'] ?? '';
    $status = $edit_item['status'] ?? 'Stored';
} else {
    $id_resi = $nama_barang = $deskripsi = '';
    $berat = 0.00;
    $jumlah = 0;
    $status = 'Stored';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Barang - Manajemen Logistik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/inventaris.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <a href="<?= BASE_URL ?>inventaris_barang" class="sidebar-item active"><i class="bi bi-boxes"></i><span>Inventaris Barang</span></a>
                <a href="<?= BASE_URL ?>inventaris_barang/histori" class="sidebar-item"><i class="bi bi-clock-history"></i><span>Histori Pengiriman</span></a>
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
                                <h1 class="h3 fw-bold mb-2">Inventaris Barang</h1>
                                <p class="text-muted mb-0">Kelola stok barang dan inventaris di sini</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= BASE_URL ?>inventaris_barang/histori" class="btn btn-outline-primary">
                                    <i class="bi bi-clock-history me-2"></i>Histori Pengiriman
                                </a>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInventaris">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Barang
                                </button>
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

                <div class="row g-3">
                    <div class="col-12">
                        <div class="card content-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Daftar Inventaris Barang</h5>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Cari barang...">
                                    <button class="btn btn-outline-secondary btn-sm" id="refreshBtn">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Form untuk kirim barang -->
                                <form method="POST" action="<?= BASE_URL ?>inventaris_barang/kirim" id="formKirimBarang">
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllBtn">
                                                <i class="bi bi-check-square me-1"></i>Pilih Semua
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                                                <i class="bi bi-square me-1"></i>Batal Pilih
                                            </button>
                                        </div>
                                        <div>
                                            <span class="badge bg-secondary" id="selectedCount">0 dipilih</span>
                                            <button type="submit" class="btn btn-sm btn-success ms-2" id="kirimBtn" disabled>
                                                <i class="bi bi-send me-1"></i>Kirim Barang
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="inventarisTable">
                                            <thead>
                                                <tr>
                                                    <th width="40">
                                                        <input type="checkbox" class="form-check-input" id="checkAll">
                                                    </th>
                                                    <th>ID</th>
                                                    <th>No Resi</th>
                                                    <th>Nama Barang</th>
                                                    <th>Berat (kg)</th>
                                                    <th>Jumlah</th>
                                                    <th>Deskripsi</th>
                                                    <th>Status</th>
                                                    <th>Tanggal Dibuat</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($inventaris)): ?>
                                                    <?php foreach ($inventaris as $row): ?>
                                                        <?php
                                                        $status_color = match($row['status']) {
                                                            'Stored' => 'success',
                                                            'Delivered' => 'primary',
                                                            'Returned' => 'warning',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="form-check-input item-checkbox" 
                                                                    name="selected_items[]" 
                                                                    value="<?= $row['id_barang'] ?>">
                                                            </td>
                                                            <td><?= htmlspecialchars($row['id_barang']) ?></td>
                                                            <td><?= htmlspecialchars($row['id_resi']) ?></td>
                                                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                                            <td><?= number_format($row['berat'], 2) ?></td>
                                                            <td><?= number_format($row['jumlah']) ?></td>
                                                            <td><?= htmlspecialchars(substr($row['deskripsi'], 0, 50)) . (strlen($row['deskripsi']) > 50 ? '...' : '') ?></td>
                                                            <td><span class="badge bg-<?= $status_color ?>-light text-<?= $status_color ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                                                            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                                            <td>
                                                                <a href="<?= BASE_URL ?>inventaris_barang?action=edit&id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                                <a href="<?= BASE_URL ?>inventaris_barang?action=delete&id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus <?= htmlspecialchars($row['nama_barang']) ?>?')">
                                                                    <i class="bi bi-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="10" class="text-center text-muted py-4">
                                                            <i class="bi bi-boxes fs-1 mb-3"></i><br>
                                                            Belum ada data inventaris barang
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah/Edit Inventaris -->
    <div class="modal fade" id="modalInventaris" tabindex="-1" aria-labelledby="modalInventarisLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInventarisLabel"><?= $edit_mode ? 'Edit' : 'Tambah' ?> Inventaris Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?= BASE_URL ?>inventaris_barang" id="formInventaris">
                    <input type="hidden" name="action" value="<?= $edit_mode ? 'edit' : 'add' ?>">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="id" value="<?= $edit_item['id_barang'] ?>">
                    <?php endif; ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_resi" class="form-label">No Resi</label>
                            <input type="text" 
                                class="form-control" 
                                id="id_resi" 
                                name="id_resi" 
                                value="<?= htmlspecialchars($id_resi) ?>" 
                                required 
                                maxlength="50"
                                list="resiList"
                                autocomplete="off"
                                placeholder="Ketik untuk mencari nomor resi...">
                            <datalist id="resiList">
                                <!-- Options akan diisi via JavaScript -->
                            </datalist>
                            <small class="text-muted">Ketik untuk mencari nomor resi yang sudah ada</small>
                        </div>
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($nama_barang) ?>" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="berat" class="form-label">Berat (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="berat" name="berat" value="<?= $berat ?>" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?= $jumlah ?>" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" maxlength="60"><?= htmlspecialchars($deskripsi) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Stored" <?= ($status === 'Stored') ? 'selected' : '' ?>>Stored</option>
                                <option value="Delivered" <?= ($status === 'Delivered') ? 'selected' : '' ?>>Delivered</option>
                                <option value="Returned" <?= ($status === 'Returned') ? 'selected' : '' ?>>Returned</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($edit_mode): ?>
                // Auto-show modal saat edit
                new bootstrap.Modal(document.getElementById('modalInventaris')).show();
            <?php endif; ?>

            // Autocomplete untuk No Resi
            const idResiInput = document.getElementById('id_resi');
            const resiDatalist = document.getElementById('resiList');
            let resiCache = [];

            // Load initial resi list
            function loadResiList(search = '') {
                fetch('<?= BASE_URL ?>inventaris_barang/get-resi?search=' + encodeURIComponent(search))
                    .then(response => response.json())
                    .then(data => {
                        resiCache = data;
                        updateDatalist(data);
                    })
                    .catch(error => console.error('Error loading resi:', error));
            }

            // Update datalist options
            function updateDatalist(data) {
                resiDatalist.innerHTML = '';
                data.forEach(resi => {
                    const option = document.createElement('option');
                    option.value = resi;
                    resiDatalist.appendChild(option);
                });
            }

            // Load resi list on input focus
            idResiInput.addEventListener('focus', function() {
                if (resiCache.length === 0) {
                    loadResiList();
                }
            });

            // Search as user types
            let searchTimeout;
            idResiInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const search = this.value;
                    if (search.length >= 1) {
                        loadResiList(search);
                    }
                }, 300); // Debounce 300ms
            });

            // Checkbox functionality
            const checkAll = document.getElementById('checkAll');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectedCount = document.getElementById('selectedCount');
            const kirimBtn = document.getElementById('kirimBtn');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const deselectAllBtn = document.getElementById('deselectAllBtn');

            function updateSelectedCount() {
                const checked = document.querySelectorAll('.item-checkbox:checked').length;
                selectedCount.textContent = `${checked} dipilih`;
                kirimBtn.disabled = checked === 0;
                
                // Update checkAll state
                if (checked === 0) {
                    checkAll.checked = false;
                    checkAll.indeterminate = false;
                } else if (checked === itemCheckboxes.length) {
                    checkAll.checked = true;
                    checkAll.indeterminate = false;
                } else {
                    checkAll.checked = false;
                    checkAll.indeterminate = true;
                }
            }

            // Check all functionality
            checkAll.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedCount();
            });

            // Individual checkbox
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });

            // Select all button
            selectAllBtn.addEventListener('click', function() {
                itemCheckboxes.forEach(cb => cb.checked = true);
                updateSelectedCount();
            });

            // Deselect all button
            deselectAllBtn.addEventListener('click', function() {
                itemCheckboxes.forEach(cb => cb.checked = false);
                updateSelectedCount();
            });

            // Form submit confirmation
            document.getElementById('formKirimBarang').addEventListener('submit', function(e) {
                const checked = document.querySelectorAll('.item-checkbox:checked').length;
                if (checked === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Pilih minimal 1 barang untuk dikirim!'
                    });
                    return false;
                }

                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Pengiriman',
                    text: `Kirim ${checked} barang ke histori?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            // Search client-side
            document.getElementById('searchInput').addEventListener('keyup', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('#inventarisTable tbody tr');
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

            // Initial count
            updateSelectedCount();
        });
    </script>
</body>
</html>