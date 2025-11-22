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

        $active_page = 'supplier';

        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/supplier/dashboard.php';
    }
    public function tambahForm() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $active_page = 'supplier';

        require __DIR__ . '/../views/header.php';
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

    public function editForm($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $requestId = (int)$id;
        $request = $this->requestModel->getRequestById($requestId);

        if (!$request || $request['id_supplier'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Request tidak ditemukan atau akses ditolak!";
            header('Location: ' . BASE_URL . 'supplier');
            exit;
        }

        $active_page = 'supplier';

        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/supplier/edit-request.php';
    }

    public function updateRequest($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'supplier');
            exit;
        }
    
        $requestId = (int)$id;
        $request = $this->requestModel->getRequestById($requestId);
    
        if (!$request || $request['id_supplier'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Akses ditolak!";
            header('Location: ' . BASE_URL . 'supplier');
            exit;
        }

        $data = [
            'nama_barang'    => trim($_POST['nama_barang']),
            'jumlah_barang'  => (int)$_POST['jumlah_barang'],
            'berat'          => (float)$_POST['berat'],
            'deskripsi'      => trim($_POST['deskripsi'] ?? ''),
            'status'         => $_POST['status'] ?? 'Menunggu Konfirmasi'
        ];
    
        if (empty($data['nama_barang']) || $data['jumlah_barang'] <= 0 || $data['berat'] <= 0) {
            $_SESSION['flash_error'] = "Data tidak valid!";
            header('Location: ' . BASE_URL . 'supplier/edit/' . $requestId);
            exit;
        }
    
        if ($this->requestModel->updateRequest($requestId, $data)) {
            $_SESSION['flash_success'] = "Request berhasil diperbarui!";
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui request!";
        }
    
        header('Location: ' . BASE_URL . 'supplier');
        exit;
    }

    public function hapus($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $requestId = (int)$id;
        $request = $this->requestModel->getRequestById($requestId);

        if (!$request || $request['id_supplier'] != $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Request tidak ditemukan atau akses ditolak!";
            header('Location: ' . BASE_URL . 'supplier');
            exit;
        }

        if ($this->requestModel->deleteRequest($requestId)) {
            $_SESSION['flash_success'] = "Request berhasil dihapus!";
        } else {
            $_SESSION['flash_error'] = "Gagal menghapus request!";
        }

        header('Location: ' . BASE_URL . 'supplier');
        exit;
    }
}