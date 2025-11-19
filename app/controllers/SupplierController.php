<?php
require_once __DIR__ . '/../models/RequestModel.php';
require_once __DIR__ . '/../../config/config.php';

class SupplierController {
    private $requestModel;

    public function __construct() {
        $this->requestModel = new RequestModel();
    }

    public function dashboard() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $requests = $this->requestModel->getRequestsBySupplier($userId);
        require __DIR__ . '/../views/supplier/dashboard.php';
    }
    public function tambahForm() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        require __DIR__ . '/../views/supplier/tambah-request.php';
    }

    public function tambahRequest() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_supplier = $_SESSION['user_id'];
            $nama_barang = trim($_POST['nama_barang']);
            $jumlah_barang = (int)$_POST['jumlah_barang'];
            $berat = (float)$_POST['berat'];
            $deskripsi = trim($_POST['deskripsi'] ?? '');

            if ($this->requestModel->createRequest($id_supplier, $nama_barang, $jumlah_barang, $berat, $deskripsi)) {
                $_SESSION['flash_success'] = "Permintaan pengiriman berhasil dikirim! Admin akan segera memproses.";
            } else {
                $_SESSION['flash_error'] = "Gagal menyimpan data. Coba lagi.";
            }
            header('Location: ' . BASE_URL . 'supplier');
            exit;
        }
    }
}