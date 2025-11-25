<?php

$user_info = $user_info ?? [
    'username' => $_SESSION['username'] ?? 'User',
    'email' => '',
    'telepon' => '',
    'alamat' => '',
    'foto' => ''
];

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';

if (isset($_SESSION['flash_success'])) {
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    unset($_SESSION['flash_error']);
}

$isEditMode = isset($_GET['edit']) && $_GET['edit'] == 'true';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Manajemen Logistik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
        <main class="main-content">
            <div class="container-fluid px-4 py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 fw-bold mb-2">Profil Pengguna</h1>
                                <p class="text-muted mb-0">Kelola informasi profil Anda</p>
                            </div>
                            <div>
                                <?php if (!$isEditMode): ?>
                                    <a href="<?= BASE_URL ?>profile?edit=true" class="btn btn-primary">
                                        <i class="bi bi-pencil me-2"></i>Edit Profil
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($flash_success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert"> 
                        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($flash_success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flash_error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($flash_error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="profile-card">
                            <div class="avatar-container">
                                <div class="avatar-large">
                                    <?php $hasFoto = !empty($user_info['foto']); ?>
                                    <img src="<?php echo $hasFoto ? BASE_URL . 'uploads/profile/' . htmlspecialchars($user_info['foto']) : ''; ?>" alt="Foto Profil" id="avatarPreview" class="<?php echo $hasFoto ? '' : 'd-none'; ?>">
                                    <i class="bi bi-person-fill <?php echo $hasFoto ? 'd-none' : ''; ?>" id="avatarFallback"></i>
                                </div>
                                <?php if ($isEditMode): ?>
                                    <div class="avatar-upload">
                                        <label for="foto" class="avatar-upload-btn" title="Pilih foto baru">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h3 class="mt-3 mb-1"><?php echo htmlspecialchars($user_info['username']); ?></h3>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($user_info['email']); ?></p>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Pribadi</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!$isEditMode): ?>
                                    <div class="profile-info">
                                        <div class="info-item">
                                            <label class="info-label">Username</label>
                                            <p class="info-value"><?php echo htmlspecialchars($user_info['username']); ?></p>
                                        </div>
                                        <div class="info-item">
                                            <label class="info-label">Email</label>
                                            <p class="info-value"><?php echo htmlspecialchars($user_info['email']); ?></p>
                                        </div>
                                        <div class="info-item">
                                            <label class="info-label">Telepon</label>
                                            <p class="info-value"><?php echo !empty($user_info['telepon']) ? htmlspecialchars($user_info['telepon']) : '-'; ?></p>
                                        </div>
                                        <div class="info-item">
                                            <label class="info-label">Alamat</label>
                                            <p class="info-value"><?php echo !empty($user_info['alamat']) ? htmlspecialchars($user_info['alamat']) : '-'; ?></p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <form method="POST" action="<?= BASE_URL ?>profile/update" class="profile-form" enctype="multipart/form-data">
                                        <input type="file" class="d-none" id="foto" name="foto" accept="image/*">

                                        <div class="mb-4">
                                            <label class="form-label d-block">Foto Profil</label>
                                            <p class="text-muted small mb-1">Klik ikon kamera pada foto untuk mengganti gambar.</p>
                                            <span class="text-muted small" id="avatar-file-name">
                                                <?php echo !empty($user_info['foto']) ? htmlspecialchars($user_info['foto']) : 'Belum ada file dipilih'; ?>
                                            </span>
                                        </div>

                                        <div class="mb-4">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control form-control-profile" id="username" name="username" value="<?php echo htmlspecialchars($user_info['username']); ?>" placeholder="Masukkan username">
                                        </div>
                                
                                        <div class="mb-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control form-control-profile" id="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" placeholder="Email tidak dapat diubah" disabled>
                                            <small class="text-muted">Email tidak dapat diubah</small>
                                        </div>
                                
                                        <div class="mb-4">
                                            <label for="telepon" class="form-label">Telepon</label>
                                            <input type="tel" class="form-control form-control-profile" id="telepon" name="telepon" value="<?php echo htmlspecialchars($user_info['telepon'] ?? ''); ?>" placeholder="Masukkan telepon">
                                        </div>
                                
                                        <div class="mb-4">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <textarea class="form-control form-control-profile" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat Anda"><?php echo htmlspecialchars($user_info['alamat'] ?? ''); ?></textarea>
                                        </div>
                                
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                                            </button>
                                            <a href="<?= BASE_URL ?>profile" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle me-2"></i>Batal
                                            </a>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$isEditMode): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="content-card">
                                <div class="card-header">
                                    <h5 class="mb-0">Keamanan Akun</h5>
                                </div>
                                <div class="card-body">
                                    <div class="security-item">
                                        <div class="security-info">
                                            <h6 class="mb-1">Ubah Password</h6>
                                            <p class="text-muted mb-0">Perbarui password Anda untuk menjaga keamanan akun</p>
                                        </div>
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                            <i class="bi bi-key me-2"></i>Ubah Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-custom">
                <div class="modal-header border-bottom-light">
                    <h5 class="modal-title"><i class="bi bi-key me-2"></i>Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= BASE_URL ?>profile/change-password">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control form-control-profile" id="currentPassword" name="current_password" placeholder="Masukkan password saat ini" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Password Baru</label>
                            <input type="password" class="form-control form-control-profile" id="newPassword" name="new_password" placeholder="Masukkan password baru" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control form-control-profile" id="confirmPassword" name="confirm_password" placeholder="Konfirmasi password baru" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function initProfilePage() {
            const avatarInput = document.getElementById('foto');
            const avatarPreview = document.getElementById('avatarPreview');
            const avatarFallback = document.getElementById('avatarFallback');
            const avatarFileName = document.getElementById('avatar-file-name');

            if (avatarInput) {
                avatarInput.addEventListener('change', function() {
                    const file = this.files && this.files[0] ? this.files[0] : null;
                    if (!file) {
                        return;
                    }

                    if (avatarFileName) {
                        avatarFileName.textContent = file.name;
                    }

                    if (avatarPreview) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                            avatarPreview.classList.remove('d-none');
                        };
                        reader.readAsDataURL(file);
                    }

                    if (avatarFallback) {
                        avatarFallback.classList.add('d-none');
                    }
                });
            }

            <?php if (!empty($flash_success)): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: <?= json_encode($flash_success) ?>,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3554f5'
                }).then(() => {
                    location.reload();
                });
            <?php endif; ?>

            <?php if (!empty($flash_error)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: <?= json_encode($flash_error) ?>,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            <?php endif; ?>

            const changePasswordForm = document.querySelector('form[action*="change-password"]');

            if (changePasswordForm) {
                changePasswordForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const actionUrl = this.getAttribute('action');

                    fetch(actionUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                                modal.hide();
                                changePasswordForm.reset();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan komunikasi dengan server',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initProfilePage);
        } else {
            initProfilePage();
        }
    </script>
</body>

</html>
