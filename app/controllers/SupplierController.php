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
        
        $isAdmin = ($_SESSION['role'] ?? '') === 'Admin';
        $isSupplier = ($_SESSION['role'] ?? '') === 'Supplier';

        if ($isAdmin) {
            $requests = $this->requestModel->getAllRequests();
        } elseif ($isSupplier) {
            $requests = $this->requestModel->getRequestsBySupplier($_SESSION['user_id']);
        } else {
            $requests = [];
            $_SESSION['flash_error'] = "Akses ditolak!";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

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
            $alamat_asal = trim($_POST['alamat_asal']);
            $alamat_tujuan = trim($_POST['alamat_tujuan']);

            if (empty($nama_barang) || $jumlah_barang <= 0 || $berat <= 0 || empty($alamat_asal) || empty($alamat_tujuan)) {
                $_SESSION['flash_error'] = "Semua field wajib diisi dengan benar!";
                header('Location: ' . BASE_URL . 'supplier/tambah');
                exit;
            }

            $foto_paket = null;
            if (!empty($_FILES['foto_paket']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/paket/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileName = $id_supplier . '_' . time() . '_' . basename($_FILES['foto_paket']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['foto_paket']['tmp_name'], $targetPath)) {
                    $foto_paket = $fileName;
                }
            }

            if ($this->requestModel->createRequest(
                $id_supplier, $nama_barang, $jumlah_barang, $berat, 
                $deskripsi, $alamat_asal, $alamat_tujuan, $foto_paket
            )) {
                $_SESSION['flash_success'] = "Request pengiriman berhasil dikirim!";
            } else {
                $_SESSION['flash_error'] = "Gagal menyimpan request!";
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

        $currentStatus = $request['status'] ?? 'Menunggu Konfirmasi';
        $newStatus = $_POST['status'] ?? $currentStatus;

        $canEditData = !in_array($currentStatus, ['Dikirim', 'Sampai ke Tujuan']);

        $data = ['status' => $newStatus];

        if ($canEditData) {
            $nama_barang = trim($_POST['nama_barang'] ?? '');
            $jumlah_barang = (int)($_POST['jumlah_barang'] ?? 0);
            $berat = (float)($_POST['berat'] ?? 0);
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            $alamat_asal = trim($_POST['alamat_asal'] ?? '');
            $alamat_tujuan = trim($_POST['alamat_tujuan'] ?? '');

            if (empty($nama_barang) || $jumlah_barang <= 0 || $berat <= 0 || empty($alamat_asal) || empty($alamat_tujuan)) {
                $_SESSION['flash_error'] = "Semua field wajib diisi dengan benar!";
                header('Location: ' . BASE_URL . 'supplier/edit/' . $requestId);
                exit;
            }

            $data['nama_barang'] = $nama_barang;
            $data['jumlah_barang'] = $jumlah_barang;
            $data['berat'] = $berat;
            $data['deskripsi'] = $deskripsi;
            $data['alamat_asal'] = $alamat_asal;
            $data['alamat_tujuan'] = $alamat_tujuan;
        }

        if ($canEditData && !empty($_FILES['foto_paket']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/paket/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = pathinfo($_FILES['foto_paket']['name'], PATHINFO_EXTENSION);
            $fileName = $request['id_supplier'] . '_' . $requestId . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['foto_paket']['tmp_name'], $targetPath)) {
                if (!empty($request['foto_paket'])) {
                    @unlink($uploadDir . $request['foto_paket']);
                }
                $data['foto_paket'] = $fileName;
            } else {
                $_SESSION['flash_error'] = "Gagal upload foto paket!";
                header('Location: ' . BASE_URL . 'supplier/edit/' . $requestId);
                exit;
            }
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

        $status = $request['status'] ?? 'Menunggu Konfirmasi';

        if ($status === 'Dikirim') {
            $_SESSION['flash_error'] = "Request barang sudah dalam perjalanan!";
            header('Location: ' . BASE_URL . 'supplier');
            exit;
        }

        if ($status === 'Sampai ke Tujuan') {
            $_SESSION['flash_error'] = "Request barang sudah sampai ke tujuan!";
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