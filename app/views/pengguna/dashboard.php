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
    <title>Kelola Pengguna - Astral Express</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                            <h1 class="h3 fw-bold mb-2"><i class="bi bi-people me-2"></i>Kelola Pengguna</h1>
                            <p class="text-muted mb-0">Kelola data staff dan akun pengguna</p>
                        </div>
                        <div>
                            <a href="<?= BASE_URL ?>pengguna/tambah" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Pengguna
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
                                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Daftar Pengguna/Staff</h5>
                                <div class="search-box">
                                    <input type="text" id="searchInput" class="form-control form-control-search" placeholder="Cari username atau email...">
                                    <i class="bi bi-search"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($users)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="userTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Telepon</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $u): ?>
                                            <tr>
                                                <td><code>#<?= str_pad($u['id'], 4, '0', STR_PAD_LEFT) ?></code></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="avatar-small">
                                                            <i class="bi bi-person-fill"></i>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-500"><?= htmlspecialchars($u['username']) ?></p>
                                                            <small class="text-muted"><?= htmlspecialchars($u['email'] ?? '-') ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($u['email']) ?></td>
                                                <td>
                                                    <?php
                                                    $role = $u['role'] ?? 'user';
                                                    $roleClass = $role == 'Admin' ? 'danger' : ($role == 'Supplier' ? 'warning' : 'info');
                                                    $roleIcon = $role == 'Admin' ? 'shield-check' : ($role == 'Supplier' ? 'box-seam' : 'person');
                                                    ?>
                                                    <span class="badge bg-<?= $roleClass ?>-soft">
                                                        <i class="bi bi-<?= $roleIcon ?> me-1"></i><?= ucfirst($role) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($u['telepon'] ?? '-') ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-action" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-gear"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="<?= BASE_URL ?>pengguna/edit/<?= $u['id'] ?>">
                                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" onclick="hapusUser(<?= $u['id'] ?>); return false;">
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
                                    <p class="text-muted mb-0">Belum ada pengguna terdaftar</p>
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
        function hapusUser(userId) {
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: 'Apakah Anda yakin ingin menghapus pengguna ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= BASE_URL ?>pengguna/hapus/' + userId;
                }
            });
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('#userTable tbody tr');
            
            tableRows.forEach(row => {
                let username = row.cells[1].textContent.toLowerCase();
                let email = row.cells[2].textContent.toLowerCase();
                let role = row.cells[3].textContent.toLowerCase();
                
                if (username.includes(searchValue) || email.includes(searchValue) || role.includes(searchValue)) {
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