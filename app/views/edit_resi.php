<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resi - Manajemen Logistik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/form_tambah_resi.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    $role = $_SESSION['role'] ?? '';
    $isDriver = $role === 'Driver';
    $disableNonStatus = $isDriver ? 'disabled' : '';
    $flashSuccess = $_SESSION['flash_success'] ?? null;
    $flashError = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    ?>
    <nav class="navbar navbar-expand-lg bg-white navbar-dashboard">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
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
                                        alt="<?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>"
                                        class="avatar-img">
                                <?php else: ?>
                                    <i class="bi bi-person-fill"></i>
                                <?php endif; ?>
                            </div>
                            <span class="d-none d-md-inline"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>profile"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="sidebar-menu">
                <a href="<?= BASE_URL ?>dashboard" class="sidebar-item">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>pengiriman" class="sidebar-item active">
                    <i class="bi bi-box-seam"></i>
                    <span>Pengiriman</span>
                </a>
                <a href="#" class="sidebar-item disabled">
                    <i class="bi bi-boxes"></i>
                    <span>Inventaris Barang</span>
                </a>
                <a href="#" class="sidebar-item disabled">
                    <i class="fa-solid fa-warehouse"></i>
                    <span>Supplier</span>
                </a>
                <a href="#" class="sidebar-item disabled">
                    <i class="bi bi-people"></i>
                    <span>Pengguna</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <div class="container-fluid px-4 py-4 page-panel">
                <div class="d-flex justify-content-end align-items-start gap-3 mb-4">
                    <div>
                        <a href="<?= BASE_URL ?>pengiriman" class="btn btn-light border">
                            Kembali
                        </a>
                        <button form="formEditResi" type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-xxl-9 col-xl-10">
                        <div class="form-wrapper">
                            <form id="formEditResi" method="POST" action="<?= BASE_URL ?>pengiriman/update" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $resi['id']; ?>">
                                <div class="section-block">
                                    <div class="section-label">Informasi Resi</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="nomorResi" class="form-label">Nomor Resi</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="nomorResi" name="nomor_resi" value="<?php echo htmlspecialchars($resi['nomor_resi']); ?>" readonly <?php echo $disableNonStatus; ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tanggalDibuat" class="form-label">Tanggal Dibuat</label>
                                            <input type="date" class="form-control" id="tanggalDibuat" value="<?php echo htmlspecialchars($resi['tanggal_dibuat']); ?>" readonly <?php echo $disableNonStatus; ?>>
                                            <input type="hidden" name="tanggal_dibuat" value="<?php echo htmlspecialchars($resi['tanggal_dibuat']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Data Pengirim</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="namaPengirim" class="form-label">Nama Pengirim</label>
                                            <input type="text" class="form-control" id="namaPengirim" name="nama_pengirim" value="<?php echo htmlspecialchars($resi['nama_pengirim']); ?>" <?php echo $disableNonStatus; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telponPengirim" class="form-label">No. Telepon</label>
                                            <input type="tel" class="form-control" id="telponPengirim" name="telpon_pengirim" value="<?php echo htmlspecialchars($resi['telpon_pengirim']); ?>" <?php echo $disableNonStatus; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kotaPengirim" class="form-label">Kota Asal</label>
                                            <input list="listKotaAsal" class="form-control" id="kotaPengirim" name="kota_pengirim" value="<?php echo htmlspecialchars($resi['kota_pengirim']); ?>" placeholder="Pilih kota" autocomplete="off" <?php echo $disableNonStatus; ?> required>
                                            <datalist id="listKotaAsal">
                                                <?php if (!empty($listKota)): ?>
                                                    <?php foreach ($listKota as $kota): ?>
                                                        <option value="<?= htmlspecialchars($kota['nama']) ?>" data-code="<?= htmlspecialchars($kota['id_kota']) ?>"></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </datalist>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="originCityCode" class="form-label">Origin City Code</label>
                                            <input type="text" class="form-control" id="originCityCode" name="origin_city_code" value="<?php echo htmlspecialchars($resi['origin_city_code']); ?>" maxlength="10" required readonly <?php echo $disableNonStatus; ?>>
                                        </div>
                                        <div class="col-12">
                                            <label for="alamatAsal" class="form-label">Alamat Lengkap</label>
                                            <textarea class="form-control" id="alamatAsal" name="alamat_asal" rows="3" <?php echo $disableNonStatus; ?> required><?php echo htmlspecialchars($resi['alamat_asal']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Data Penerima</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="namaPenerima" class="form-label">Nama Penerima</label>
                                            <input type="text" class="form-control" id="namaPenerima" name="nama_penerima" value="<?php echo htmlspecialchars($resi['nama_penerima']); ?>" <?php echo $disableNonStatus; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telponPenerima" class="form-label">No. Telepon</label>
                                            <input type="text" class="form-control" id="telponPenerima" name="telpon_penerima" value="<?php echo htmlspecialchars($resi['telpon_penerima']); ?>" <?php echo $disableNonStatus; ?> required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kotaPenerima" class="form-label">Kota Tujuan</label>
                                            <input list="listKotaTujuan" class="form-control" id="kotaPenerima" name="kota_penerima" value="<?php echo htmlspecialchars($resi['kota_penerima']); ?>" placeholder="Pilih kota" autocomplete="off" <?php echo $disableNonStatus; ?> required>
                                            <datalist id="listKotaTujuan">
                                                <?php if (!empty($listKota)): ?>
                                                    <?php foreach ($listKota as $k): ?>
                                                        <option value="<?= htmlspecialchars($k['nama']) ?>" data-code="<?= htmlspecialchars($k['id_kota']) ?>"></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </datalist>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="destinationCityCode" class="form-label">Destination City Code</label>
                                            <input type="text" class="form-control" id="destinationCityCode" name="destination_city_code" value="<?php echo htmlspecialchars($resi['destination_city_code']); ?>" maxlength="10" required readonly <?php echo $disableNonStatus; ?>>
                                        </div>
                                        <div class="col-12">
                                            <label for="alamatTujuan" class="form-label">Alamat Tujuan</label>
                                            <textarea class="form-control" id="alamatTujuan" name="alamat_tujuan" rows="3" <?php echo $disableNonStatus; ?> required><?php echo htmlspecialchars($resi['alamat_tujuan']); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Detail Pengiriman</div>
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label for="jumlahPaket" class="form-label">Jumlah Paket</label>
                                            <input type="number" min="1" class="form-control" id="jumlahPaket" name="jumlah_paket" value="<?php echo htmlspecialchars($resi['jumlah_paket']); ?>" <?php echo $disableNonStatus; ?> required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="beratTotal" class="form-label">Berat Total (Kg)</label>
                                            <input type="number" min="1" step="0.01" class="form-control" id="beratTotal" name="berat_total" value="<?php echo htmlspecialchars($resi['berat_total']); ?>" <?php echo $disableNonStatus; ?> required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="totalJarak" class="form-label">Jarak (Km)</label>
                                            <input type="number" step="0.01" class="form-control" id="totalJarak" name="total_jarak" value="<?php echo htmlspecialchars($resi['total_jarak']); ?>" readonly <?php echo $disableNonStatus; ?>>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="totalOngkir" class="form-label">Ongkir (Rp)</label>
                                            <input type="number" class="form-control" id="totalOngkir" name="total_ongkir" value="<?php echo htmlspecialchars($resi['total_ongkir']); ?>" readonly <?php echo $disableNonStatus; ?>>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Status</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Status Pengiriman</label>
                                            <select class="form-select" name="status_pengiriman">
                                                <?php
                                                $statusPengirimanOptions = ['Dalam Proses', 'Terkirim', 'Tertunda'];
                                                foreach ($statusPengirimanOptions as $opt) :
                                                    $selected = strtolower($resi['status_pengiriman'] ?? '') === strtolower($opt) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo $opt; ?>" <?php echo $selected; ?>><?php echo $opt; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status Pembayaran</label>
                                            <select class="form-select" name="status_pembayaran" <?php echo $disableNonStatus; ?>>
                                                <?php
                                                $statusPembayaranOptions = ['Belum Bayar', 'Terbayar'];
                                                foreach ($statusPembayaranOptions as $opt) :
                                                    $selected = strtolower($resi['status_pembayaran'] ?? '') === strtolower($opt) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo $opt; ?>" <?php echo $selected; ?>><?php echo $opt; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <a href="<?= BASE_URL ?>pengiriman" class="btn btn-light border">Batal</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('formEditResi');
            const submitButtons = document.querySelectorAll('#formEditResi button[type="submit"], button[type="submit"][form="formEditResi"]');
            let isSubmitting = false;

            const originCodeInput = document.getElementById('originCityCode');
            const destCodeInput = document.getElementById('destinationCityCode');
            const nomorResiInput = document.getElementById('nomorResi');
            const kotaAsalInput = document.getElementById('kotaPengirim');
            const kotaTujuanInput = document.getElementById('kotaPenerima');
            const jarakInput = document.getElementById('totalJarak');
            const ongkirInput = document.getElementById('totalOngkir');
            const beratInput = document.getElementById('beratTotal');
            const asalInput = document.getElementById('kotaPengirim');
            const tujuanInput = document.getElementById('kotaPenerima');
            const hitungUrl = '<?= BASE_URL ?>pengiriman/hitung-jarak';
            let hitungTimer = null;

            const disableSubmitButtons = () => {
                isSubmitting = true;
                submitButtons.forEach(btn => {
                    btn.dataset.prevText = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = 'Menyimpan...';
                });
            };

            const enableSubmitButtons = () => {
                isSubmitting = false;
                submitButtons.forEach(btn => {
                    if (btn.dataset.prevText) {
                        btn.innerHTML = btn.dataset.prevText;
                    }
                    btn.disabled = false;
                });
            };

            const randomLetters = (len = 3) => {
                const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                let out = '';
                for (let i = 0; i < len; i++) {
                    out += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
                }
                return out;
            };

            const generateResi = () => {
                if (!nomorResiInput) return;
                const ori = (originCodeInput?.value || '').trim().toUpperCase();
                const dest = (destCodeInput?.value || '').trim().toUpperCase();
                if (!ori || !dest) return;
                nomorResiInput.value = `${ori}${randomLetters(3)}${dest}`;
            };

            [originCodeInput, destCodeInput].forEach(input => {
                if (input) {
                    input.addEventListener('change', generateResi);
                    input.addEventListener('blur', generateResi);
                }
            });

            // Ambil kode kota dari datalist dan isi code + trigger resi/jarak
            const findCodeByName = (inputEl) => {
                if (!inputEl) return '';
                const val = (inputEl.value || '').trim();
                if (!val) return '';
                const listId = inputEl.getAttribute('list');
                const listEl = listId ? document.getElementById(listId) : null;
                if (!listEl) return '';
                const option = Array.from(listEl.options).find(opt => opt.value.trim().toLowerCase() === val.toLowerCase());
                return option ? (option.dataset.code || '') : '';
            };

            const syncKotaCode = () => {
                const codeAsal = findCodeByName(kotaAsalInput);
                const codeTujuan = findCodeByName(kotaTujuanInput);
                if (originCodeInput) originCodeInput.value = codeAsal;
                if (destCodeInput) destCodeInput.value = codeTujuan;
                generateResi();
            };

            [kotaAsalInput, kotaTujuanInput].forEach(input => {
                if (input) {
                    input.addEventListener('change', () => {
                        syncKotaCode();
                        scheduleHitung();
                    });
                    input.addEventListener('blur', () => {
                        syncKotaCode();
                        scheduleHitung();
                    });
                }
            });

            const hitungOtomatis = async () => {
                const asal = (asalInput?.value || '').trim();
                const tujuan = (tujuanInput?.value || '').trim();
                const berat = parseFloat(beratInput?.value || '0') || 0;
                if (!asal || !tujuan) return;
                if (jarakInput) jarakInput.value = '...';
                if (ongkirInput) ongkirInput.value = '';
                try {
                    const res = await fetch(hitungUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            asal,
                            tujuan,
                            berat
                        })
                    });
                    const data = await res.json();
                    if (!res.ok || data.success !== true) {
                        throw new Error(data.message || 'Gagal menghitung jarak');
                    }
                    if (jarakInput) jarakInput.value = parseFloat(data.jarak_km).toFixed(2);
                    if (ongkirInput) ongkirInput.value = Math.round(data.ongkir);
                } catch (err) {
                    console.error(err);
                    if (jarakInput) jarakInput.value = '';
                    if (ongkirInput) ongkirInput.value = '';
                }
            };

            const scheduleHitung = () => {
                clearTimeout(hitungTimer);
                hitungTimer = setTimeout(hitungOtomatis, 400);
            };

            [asalInput, tujuanInput, beratInput].forEach(input => {
                if (input) {
                    input.addEventListener('change', scheduleHitung);
                    input.addEventListener('blur', scheduleHitung);
                }
            });

            // Sinkron awal untuk data edit
            syncKotaCode();
            scheduleHitung();

            if (form) {
                form.addEventListener('submit', (e) => {
                    if (isSubmitting) {
                        e.preventDefault();
                        return;
                    }
                    if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        e.preventDefault();
                        return;
                    }
                    disableSubmitButtons();
                });
            }
        });
    </script>
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
</body>

</html>
