<?php

require_once __DIR__ . '/../models/Resi.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';


class PengirimanController
{
    private $resiModel;
    private $userModel;

    public function __construct()
    {
        $this->resiModel = new Resi();
        $this->userModel = new User();
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $stats = $this->resiModel->getStats($userId);
        $detailResi = $this->resiModel->getDetailedResi($userId);
        $user_info = $this->userModel->getProfile($userId);
        if (!$user_info) {
            $user_info = [
                'username' => $_SESSION['username'] ?? 'User',
                'email' => '',
                'telepon' => '',
                'alamat' => '',
                'foto' => ''
            ];
        }

        require __DIR__ . '/../views/pengiriman.php';
    }

    public function create()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user_info = $this->userModel->getProfile($userId);

        if (!$user_info) {
            $user_info = [
                'username' => $_SESSION['username'] ?? 'User',
                'email' => '',
                'telepon' => '',
                'alamat' => '',
                'foto' => ''
            ];
        }

        require __DIR__ . '/../views/tambah_resi.php';
    }


    public function store()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $data = [
            'user_id' => $_SESSION['user_id'],
            'nomor_resi' => trim($_POST['nomor_resi'] ?? ''),
            'tanggal_dibuat' => $_POST['tanggal_dibuat'] ?? date('Y-m-d'),
            'nama_pengirim' => trim($_POST['nama_pengirim'] ?? ''),
            'telpon_pengirim' => trim($_POST['telpon_pengirim'] ?? ''),
            'kota_pengirim' => trim($_POST['kota_pengirim'] ?? ''),
            'alamat_asal' => trim($_POST['alamat_asal'] ?? ''),
            'nama_penerima' => trim($_POST['nama_penerima'] ?? ''),
            'telpon_penerima' => trim($_POST['telpon_penerima'] ?? ''),
            'kota_penerima' => trim($_POST['kota_penerima'] ?? ''),
            'alamat_tujuan' => trim($_POST['alamat_tujuan'] ?? ''),
            'jumlah_paket' => isset($_POST['jumlah_paket']) ? (int) $_POST['jumlah_paket'] : 0,
            'berat_total' => $_POST['berat_total'] !== '' ? (float) $_POST['berat_total'] : null,
            'total_jarak' => $_POST['total_jarak'] !== '' ? (float) $_POST['total_jarak'] : null,
            'total_ongkir' => $_POST['total_ongkir'] !== '' ? (float) $_POST['total_ongkir'] : null,
        ];
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $statusPembayaran = null;
        if ($paymentMethod === 'qris' && ($_POST['status_pembayaran'] ?? '') === 'Terbayar') {
            $statusPembayaran = 'Terbayar';
        }
        $data['status_pembayaran'] = $statusPembayaran;

        $required = ['nomor_resi', 'nama_pengirim', 'telpon_pengirim', 'kota_pengirim', 'alamat_asal', 'nama_penerima', 'telpon_penerima', 'kota_penerima', 'alamat_tujuan'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $_SESSION['flash_error'] = 'Lengkapi semua data wajib.';
                header('Location: ' . BASE_URL . 'pengiriman/tambah');
                exit;
            }
        }

        if ($data['jumlah_paket'] < 1) {
            $_SESSION['flash_error'] = 'Jumlah paket minimal 1.';
            header('Location: ' . BASE_URL . 'pengiriman/tambah');
            exit;
        }

        $this->resiModel->create($data);

        if (strtolower($data['status_pembayaran']) === 'terbayar') {
            $_SESSION['flash_success'] = 'anda sukses melakukan pembayaran';
        } else {
            $_SESSION['flash_success'] = 'Resi berhasil ditambahkan. Pembayaran COD.';
        }
        header('Location: ' . BASE_URL . 'pengiriman');
    }

    public function delete()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Data tidak valid.';
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }

        $deleted = $this->resiModel->deleteById($id, $_SESSION['user_id']);
        if ($deleted) {
            $_SESSION['flash_success'] = 'Resi berhasil dihapus.';
        } else {
            $_SESSION['flash_error'] = 'Gagal hapus resi atau tidak ditemukan.';
        }
        header('Location: ' . BASE_URL . 'pengiriman');
    }
}
