<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Resi - Manajemen Logistik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/form_tambah_resi.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php
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
                <a href="<?= BASE_URL ?>dashboard" class="sidebar-item">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= BASE_URL ?>pengiriman" class="sidebar-item active">
                    <i class="bi bi-box-seam"></i>
                    <span>Pengiriman</span>
                </a>
                <?php if ($isAdmin): ?>
                    <a href="#" class="sidebar-item">
                        <i class="bi bi-boxes"></i>
                        <span>Inventaris Barang</span>
                    </a>
                    <a href="#" class="sidebar-item">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>Supplier</span>
                    </a>
                    <a href="#" class="sidebar-item">
                        <i class="bi bi-people"></i>
                        <span>Pengguna</span>
                    </a>
                <?php endif; ?>
            </div>
        </aside>

        <main class="main-content">
            <div class="container-fluid px-4 py-4 page-panel">
                <div class="d-flex justify-content-end align-items-start gap-3 mb-4">
                    <div>
                        <a href="<?= BASE_URL ?>pengiriman" class="btn btn-light border">
                            Kembali
                        </a>
                        <button form="formTambahResi" type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Resi
                        </button>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-xxl-9 col-xl-10">
                        <div class="form-wrapper">
                            <form id="formTambahResi" method="POST" action="<?= BASE_URL ?>pengiriman/store" class="needs-validation" novalidate>
                                <div class="section-block">
                                    <div class="section-label">Informasi Resi</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="nomorResi" class="form-label">Nomor Resi</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="nomorResi" name="nomor_resi" placeholder="CTH: BDGABCJKT" disabled>
                                            </div>
                                            <small class="text-muted">Format: originCode + 3 huruf acak + destinationCode</small>
                                            <div class="invalid-feedback">Nomor resi wajib diisi.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tanggalDibuat" class="form-label">Tanggal Dibuat</label>
                                            <input type="date" class="form-control" id="tanggalDibuat" name="tanggal_dibuat" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Data Pengirim</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="namaPengirim" class="form-label">Nama Pengirim</label>
                                            <input type="text" class="form-control" id="namaPengirim" name="nama_pengirim" placeholder="Nama lengkap" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telponPengirim" class="form-label">No. Telepon</label>
                                            <input type="tel" class="form-control" id="telponPengirim" name="telpon_pengirim" placeholder="08xxxxxxxxxx" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kotaPengirim" class="form-label">Kota Asal</label>
                                            <input type="text" class="form-control" id="kotaPengirim" name="kota_pengirim" placeholder="Contoh: Bandung" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="originCityCode" class="form-label">Origin City Code</label>
                                            <input type="text" class="form-control" id="originCityCode" name="origin_city_code" placeholder="CTH: BDG" maxlength="10" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="alamatAsal" class="form-label">Alamat Lengkap</label>
                                            <textarea class="form-control" id="alamatAsal" name="alamat_asal" rows="3" placeholder="Tulis alamat penjemputan lengkap" required></textarea>
                                            <small class="text-muted">Sertakan detail patokan bila diperlukan.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Data Penerima</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="namaPenerima" class="form-label">Nama Penerima</label>
                                            <input type="text" class="form-control" id="namaPenerima" name="nama_penerima" placeholder="Nama lengkap" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telponPenerima" class="form-label">No. Telepon</label>
                                            <input type="text" class="form-control" id="telponPenerima" name="telpon_penerima" placeholder="08xxxxxxxxxx" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kotaPenerima" class="form-label">Kota Tujuan</label>
                                            <input type="text" class="form-control" id="kotaPenerima" name="kota_penerima" placeholder="Contoh: Surabaya" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="destinationCityCode" class="form-label">Destination City Code</label>
                                            <input type="text" class="form-control" id="destinationCityCode" name="destination_city_code" placeholder="CTH: SBY" maxlength="10" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="alamatTujuan" class="form-label">Alamat Tujuan</label>
                                            <textarea class="form-control" id="alamatTujuan" name="alamat_tujuan" rows="3" placeholder="Tulis alamat pengantaran lengkap" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Detail Pengiriman</div>
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label for="jumlahPaket" class="form-label">Jumlah Paket</label>
                                            <input type="number" min="1" class="form-control" id="jumlahPaket" name="jumlah_paket" placeholder="1" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="beratTotal" class="form-label">Berat Total (Kg)</label>
                                            <input type="number" step="0.01" class="form-control" id="beratTotal" name="berat_total" placeholder="0.00">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="totalJarak" class="form-label">Jarak (Km)</label>
                                            <input type="number" step="0.01" class="form-control" id="totalJarak" name="total_jarak" placeholder="0.00" readonly>
                                            <small class="text-muted">Diisi otomatis setelah kota asal & tujuan dimasukkan.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="totalOngkir" class="form-label">Estimasi Ongkir (Rp)</label>
                                            <input type="number" class="form-control" id="totalOngkir" name="total_ongkir" placeholder="0" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-block">
                                    <div class="section-label">Pembayaran</div>
                                    <div class="row g-4 align-items-center">
                                        <div class="col-md-6">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentQris" value="qris">
                                                    <label class="form-check-label" for="paymentQris">
                                                        QRIS (bayar sekarang)
                                                    </label>
                                                    <div class="text-muted small">Muncul modal QR statis, lalu konfirmasi pembayaran.</div>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="paymentCod" value="cod" checked>
                                                    <label class="form-check-label" for="paymentCod">
                                                        COD (Cash on Delivery)
                                                    </label>
                                                    <div class="text-muted small">Status pembayaran akan diset ke Belum Dibayar.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light h-100 d-flex align-items-center">
                                                <div>
                                                    <p class="mb-1 fw-semibold">QRIS Preview</p>
                                                    <div class="border rounded bg-white p-3 text-center">
                                                        <i class="bi bi-qr-code" style="font-size: 2rem;"></i>
                                                        <div class="small text-muted mt-2">Kode QR statis</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="status_pembayaran" id="statusPembayaran" value="">
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="reset" class="btn btn-primary">
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Simpan dan Proses
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                const dateInput = document.getElementById('tanggalDibuat');
                if (dateInput) {
                    const today = new Date().toISOString().split('T')[0];
                    dateInput.value = today;
                }

                const form = document.getElementById('formTambahResi');
                const statusInput = document.getElementById('statusPembayaran');
                const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
                const submitButtons = document.querySelectorAll('#formTambahResi button[type="submit"], button[type="submit"][form="formTambahResi"]');
                let qrisConfirmed = false;
                let isSubmitting = false;

                const getSelectedPayment = () => {
                    const checked = document.querySelector('input[name="payment_method"]:checked');
                    return checked ? checked.value : 'cod';
                };

                const setStatusPembayaran = (method) => {
                    if (!statusInput) return;
                    statusInput.value = method === 'qris' ? 'Terbayar' : '';
                };

                paymentRadios.forEach(radio => {
                    radio.addEventListener('change', () => {
                        qrisConfirmed = false;
                        setStatusPembayaran(radio.value);
                    });
                });
                setStatusPembayaran(getSelectedPayment());

                const originCodeInput = document.getElementById('originCityCode');
                const destCodeInput = document.getElementById('destinationCityCode');
                const nomorResiInput = document.getElementById('nomorResi');
                const btnGenerateResi = document.getElementById('btnGenerateResi');

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

                if (btnGenerateResi) {
                    btnGenerateResi.addEventListener('click', () => {
                        generateResi();
                    });
                }

                [originCodeInput, destCodeInput].forEach(input => {
                    if (input) {
                        input.addEventListener('change', generateResi);
                        input.addEventListener('blur', generateResi);
                    }
                });

                const showQrisPopup = () => {
                    const qrisImage = '<?= BASE_URL ?>assets/qris.png'; // ganti dengan path QRIS asli Anda
                    return Swal.fire({
                        title: 'QRIS Pembayaran',
                        html: `<div class="d-inline-block border rounded p-3 mb-3 bg-light">
                                <img src="${qrisImage}" alt="QRIS" class="img-fluid" style="max-width:220px;max-height:220px;object-fit:contain;">
                                <div class="small text-muted mt-2">QR statis - silakan scan</div>
                           </div>
                           <p class="mb-0 text-muted small">Setelah bayar, klik "Konfirmasi Pembayaran".</p>`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Konfirmasi Pembayaran',
                        cancelButtonText: 'Batal',
                        focusConfirm: false,
                        confirmButtonColor: '#3554f5',
                        cancelButtonColor: '#6c757d'
                    });
                };

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

                if (form) {
                    form.addEventListener('submit', async (e) => {
                        if (e.defaultPrevented) return;
                        if (isSubmitting) {
                            e.preventDefault();
                            return;
                        }
                        if (!form.checkValidity()) {
                            form.classList.add('was-validated');
                            e.preventDefault();
                            return;
                        }
                        const method = getSelectedPayment();
                        if (method === 'qris' && !qrisConfirmed) {
                            e.preventDefault();
                            setStatusPembayaran('qris');
                            const result = await showQrisPopup();
                            if (result.isConfirmed) {
                                qrisConfirmed = true;
                                setStatusPembayaran('qris');
                                disableSubmitButtons();
                                form.submit();
                            } else {
                                enableSubmitButtons();
                            }
                            return;
                        }
                        setStatusPembayaran(method);
                        disableSubmitButtons();
                    });
                }

                // Step A: Auto hitung jarak + ongkir saat kota asal & tujuan terisi
                const asalInput = document.getElementById('kotaPengirim');
                const tujuanInput = document.getElementById('kotaPenerima');
                const jarakInput = document.getElementById('totalJarak');
                const ongkirInput = document.getElementById('totalOngkir');
                const hitungUrl = '<?= BASE_URL ?>pengiriman/hitung-jarak';
                let hitungTimer = null;

                const hitungOtomatis = async () => {
                    const asal = (asalInput?.value || '').trim();
                    const tujuan = (tujuanInput?.value || '').trim();
                    if (!asal || !tujuan) return;
                    if (jarakInput) jarakInput.value = '...';
                    if (ongkirInput) ongkirInput.value = '';

                    try {
                        const res = await fetch(hitungUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ asal, tujuan })
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

                [asalInput, tujuanInput].forEach(input => {
                    if (input) {
                        input.addEventListener('change', scheduleHitung);
                        input.addEventListener('blur', scheduleHitung);
                    }
                });
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
