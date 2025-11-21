<?php

require_once __DIR__ . '/../models/InventarisModel.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';

class InventarisController {
    private $inventarisModel;
    private $userModel;
    
    public function __construct() {
        $this->inventarisModel = new InventarisModel();
        $this->userModel = new User();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $inventaris = $this->inventarisModel->getAll();

        // Ambil data user untuk foto profil
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

        // Untuk edit mode
        $edit_item = null;
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $edit_item = $this->inventarisModel->getById($_GET['id']);
            if (!$edit_item) {
                $_SESSION['flash_error'] = 'Barang tidak ditemukan!';
                header('Location: ' . BASE_URL . 'inventaris_barang');
                exit;
            }
        }

        require __DIR__ . '/../views/inventaris.php';
    }

    // Halaman histori barang
    public function histori() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $histori = $this->inventarisModel->getHistori();

        // Ambil data user untuk foto profil
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

        require __DIR__ . '/../views/inventaris_histori.php';
    }

    // API endpoint untuk autocomplete nomor resi
    public function getResiList() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        
        $search = $_GET['search'] ?? '';
        $resiList = $this->inventarisModel->searchResi($search);
        
        echo json_encode($resiList);
        exit;
    }

    // Handle add (POST)
    public function add() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_resi' => $_POST['id_resi'] ?? '',
                'nama_barang' => $_POST['nama_barang'] ?? '',
                'berat' => (float)($_POST['berat'] ?? 0.0),
                'jumlah' => (int)($_POST['jumlah'] ?? 0),
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'status' => $_POST['status'] ?? 'Stored'
            ];

            if (empty($data['nama_barang']) || empty($data['id_resi']) || $data['berat'] < 0 || $data['jumlah'] < 0) {
                $_SESSION['flash_error'] = 'Data tidak valid!';
            } elseif ($this->inventarisModel->create($data)) {
                $_SESSION['flash_success'] = 'Barang berhasil ditambahkan!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menambahkan barang! No Resi mungkin sudah digunakan.';
            }
        }
        header('Location: ' . BASE_URL . 'inventaris_barang');
        exit;
    }

    // Handle edit (POST)
    public function edit() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $data = [
                'id_resi' => $_POST['id_resi'] ?? '',
                'nama_barang' => $_POST['nama_barang'] ?? '',
                'berat' => (float)($_POST['berat'] ?? 0.0),
                'jumlah' => (int)($_POST['jumlah'] ?? 0),
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'status' => $_POST['status'] ?? 'Stored'
            ];

            if (empty($data['nama_barang']) || empty($data['id_resi']) || $data['berat'] < 0 || $data['jumlah'] < 0) {
                $_SESSION['flash_error'] = 'Data tidak valid!';
            } elseif ($this->inventarisModel->update($id, $data)) {
                $_SESSION['flash_success'] = 'Barang berhasil diupdate!';
            } else {
                $_SESSION['flash_error'] = 'Gagal mengupdate barang!';
            }
        }
        header('Location: ' . BASE_URL . 'inventaris_barang');
        exit;
    }

    // Handle delete
    public function delete() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            if ($this->inventarisModel->delete($id)) {
                $_SESSION['flash_success'] = 'Barang berhasil dihapus!';
            } else {
                $_SESSION['flash_error'] = 'Gagal menghapus barang!';
            }
        }
        header('Location: ' . BASE_URL . 'inventaris_barang');
        exit;
    }

    // Handle kirim barang (batch)
    public function kirim() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selectedIds = $_POST['selected_items'] ?? [];

            if (empty($selectedIds)) {
                $_SESSION['flash_error'] = 'Pilih minimal 1 barang untuk dikirim!';
            } else {
                // Validasi bahwa ID yang dipilih ada dan belum dikirim
                $items = $this->inventarisModel->getByIds($selectedIds);
                
                if (empty($items)) {
                    $_SESSION['flash_error'] = 'Barang yang dipilih tidak valid!';
                } else {
                    // Kirim barang
                    if ($this->inventarisModel->kirimBarang($selectedIds)) {
                        $count = count($selectedIds);
                        $_SESSION['flash_success'] = "$count barang berhasil dikirim ke histori!";
                    } else {
                        $_SESSION['flash_error'] = 'Gagal mengirim barang!';
                    }
                }
            }
        }

        header('Location: ' . BASE_URL . 'inventaris_barang');
        exit;
    }
}
?>