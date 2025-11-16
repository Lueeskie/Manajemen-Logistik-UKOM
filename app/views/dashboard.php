<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login');
    exit;
}

$flash_success = '';
if (isset($_SESSION['flash_success'])) {
    $flash_success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

$shipments = isset($shipments) ? $shipments : [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Manajemen Logistik</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #3554f5;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .role-pill {
            background: rgba(53, 84, 245, 0.1);
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
            margin-right: 16px;
        }

        .logout-link {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
        }

        .dashboard-container {
            padding: 32px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        }

        thead {
            background: #f1f5f9;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #64748b;
        }

        th,
        td {
            padding: 16px 20px;
            border-bottom: 1px solid;
            text-align: left;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .empty-state {
            text-align: center;
            padding: 32px 16px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="brand">AstralExpress Dashboard</div>
        <div>
            <span class="role-pill"><?= htmlspecialchars($_SESSION['role']) ?></span>
            <a href="<?= BASE_URL ?>logout" class="logout-link">Logout</a>
        </div>
    </header>

    <div class="dashboard-container">
        <h2>Daftar Pengiriman</h2>
        <p>Total pengiriman: <strong><?= count($shipments) ?></strong></p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Resi</th>
                    <th>Pengirim</th>
                    <th>Penerima</th>
                    <th>Jumlah & Berat</th>
                    <th>Ongkos Kirim</th>
                    <th>Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($shipments)) : ?>
                    <?php foreach ($shipments as $index => $resi) : ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($resi['nomor_resi']) ?></strong><br>
                                <small>Dicatat oleh: <?= htmlspecialchars($resi['username'] ?? '-') ?></small>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($resi['nama_pengirim']) ?></div>
                                <small><?= htmlspecialchars($resi['alamat_asal']) ?></small>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($resi['nama_penerima']) ?></div>
                                <small><?= htmlspecialchars($resi['alamat_tujuan']) ?></small>
                            </td>
                            <td>
                                <?= (int)$resi['jumlah_paket'] ?> paket<br>
                                <small><?= number_format($resi['berat_total'], 2, ',', '.') ?> Kg</small>
                            </td>
                            <td>Rp <?= number_format((int)$resi['ongkos_kirim'], 0, ',', '.') ?></td>
                            <td><?= date('d M Y H:i', strtotime($resi['tanggal_dibuat'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada data pengiriman yang tercatat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        <?php if (!empty($flash_success)) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: <?= json_encode($flash_success) ?>,
                confirmButtonColor: '#0d6efd'
            });
        <?php endif; ?>
    </script>
</body>

</html>