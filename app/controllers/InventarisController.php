<!-- inventarisController.php -->
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
        // Bersihkan output buffer sebelumnya
        if (ob_get_level()) {
            ob_clean();
        }
        
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        
        try {
            $search = $_GET['search'] ?? '';
            $resiList = $this->inventarisModel->searchResi($search);
            
            echo json_encode($resiList);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }

    // Handle add (POST)
    public function add() {
        error_log("=== ADD METHOD CALLED ===");
        
        session_start();
        if (!isset($_SESSION['user_id'])) {
            error_log("Add: User not logged in");
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        error_log("Add: User ID = " . $_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Add: POST method confirmed");
            error_log("Add: POST data = " . print_r($_POST, true));
            
            $data = [
                'nomor_resi' => trim($_POST['nomor_resi'] ?? ''),
                'nama_barang' => trim($_POST['nama_barang'] ?? ''),
                'berat' => (float)($_POST['berat'] ?? 0.0),
                'jumlah' => (int)($_POST['jumlah'] ?? 0),
                'deskripsi' => trim($_POST['deskripsi'] ?? ''),
                'status' => $_POST['status'] ?? 'Stored'
            ];

            error_log("Add: Processed data = " . print_r($data, true));

            // Validasi data
            if (empty($data['nomor_resi'])) {
                $_SESSION['flash_error'] = 'Nomor resi harus diisi!';
                error_log("Add: VALIDATION ERROR - Nomor resi kosong");
            } elseif (empty($data['nama_barang'])) {
                $_SESSION['flash_error'] = 'Nama barang harus diisi!';
                error_log("Add: VALIDATION ERROR - Nama barang kosong");
            } elseif ($data['berat'] < 0) {
                $_SESSION['flash_error'] = 'Berat tidak boleh negatif!';
                error_log("Add: VALIDATION ERROR - Berat negatif");
            } elseif ($data['jumlah'] <= 0) {
                $_SESSION['flash_error'] = 'Jumlah harus lebih dari 0!';
                error_log("Add: VALIDATION ERROR - Jumlah tidak valid");
            } else {
                error_log("Add: Validation passed, calling model->create()");
                
                // Simpan ke database
                try {
                    $result = $this->inventarisModel->create($data);
                    error_log("Add: Model->create() returned = " . ($result ? 'TRUE' : 'FALSE'));
                    
                    if ($result) {
                        $_SESSION['flash_success'] = 'Barang "' . htmlspecialchars($data['nama_barang']) . '" berhasil ditambahkan ke resi ' . htmlspecialchars($data['nomor_resi']) . '!';
                        error_log("Add: SUCCESS - Barang ditambahkan");
                    } else {
                        $_SESSION['flash_error'] = 'Gagal menambahkan barang! Silakan coba lagi.';
                        error_log("Add: FAILED - Database insert failed");
                    }
                } catch (Exception $e) {
                    $_SESSION['flash_error'] = 'Error: ' . $e->getMessage();
                    error_log("Add: EXCEPTION - " . $e->getMessage());
                }
            }
        } else {
            error_log("Add: NOT POST method, method = " . $_SERVER['REQUEST_METHOD']);
        }
        
        error_log("Add: Redirecting to inventaris_barang");
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
                'nomor_resi' => $_POST['nomor_resi'] ?? '',
                'nama_barang' => $_POST['nama_barang'] ?? '',
                'berat' => (float)($_POST['berat'] ?? 0.0),
                'jumlah' => (int)($_POST['jumlah'] ?? 0),
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'status' => $_POST['status'] ?? 'Stored'
            ];

            if (empty($data['nama_barang']) || empty($data['nomor_resi']) || $data['berat'] < 0 || $data['jumlah'] < 0) {
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

            // Debug: Log selected IDs
            error_log("Selected IDs: " . print_r($selectedIds, true));

            if (empty($selectedIds)) {
                $_SESSION['flash_error'] = 'Pilih minimal 1 barang untuk dikirim!';
            } else {
                // Validasi bahwa ID yang dipilih ada dan belum dikirim
                $items = $this->inventarisModel->getByIds($selectedIds);
                
                error_log("Found items: " . count($items));
                
                if (empty($items)) {
                    $_SESSION['flash_error'] = 'Barang yang dipilih tidak valid!';
                } else {
                    // Kirim barang
                    $result = $this->inventarisModel->kirimBarang($selectedIds);
                    
                    error_log("Kirim barang result: " . ($result ? 'SUCCESS' : 'FAILED'));
                    
                    if ($result) {
                        $count = count($selectedIds);
                        $_SESSION['flash_success'] = "$count barang berhasil dikirim ke histori!";
                    } else {
                        $_SESSION['flash_error'] = 'Gagal mengirim barang! Silakan cek error log.';
                    }
                }
            }
        }

        header('Location: ' . BASE_URL . 'inventaris_barang');
        exit;
    }
}
?>