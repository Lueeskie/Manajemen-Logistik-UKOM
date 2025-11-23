<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        $users = $this->userModel->getAllUsers();
        $active_page = 'pengguna';

        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/pengguna/dashboard.php';
    }

    public function tambah() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        
        $roles = $this->userModel->getAllRoles();

        $active_page = 'pengguna';

        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/pengguna/tambah-pengguna.php';
    }

    public function store() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $id_role = (int)$_POST['id_role'];

            if (empty($username) || empty($email) || empty($password)) {
                $_SESSION['flash_error'] = "Semua field wajib diisi!";
                header('Location: ' . BASE_URL . 'pengguna/tambah');
                exit;
            }

            $check = Database::getInstance()->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $check->execute([$email, $username]);
            if ($check->fetch()) {
                $_SESSION['flash_error'] = "Email atau username sudah digunakan!";
                header('Location: ' . BASE_URL . 'pengguna/tambah');
                exit;
            }

            $stmt = Database::getInstance()->prepare("
                INSERT INTO users (username, email, password, id_role) 
                VALUES (?, ?, ?, ?)
            ");
            if ($stmt->execute([$username, $email, $password, $id_role])) {
                $_SESSION['flash_success'] = "Pengguna berhasil ditambahkan!";
            } else {
                $_SESSION['flash_error'] = "Gagal menambah pengguna!";
            }
            header('Location: ' . BASE_URL . 'pengguna');
            exit;
        }
    }
    public function edit($id) {
        session_start();

        $userId = (int)$id;
        $user = $this->userModel->getProfile($userId);
        if (!$user) {
            $_SESSION['flash_error'] = "Pengguna tidak ditemukan!";
            header('Location: ' . BASE_URL . 'pengguna');
            exit;
        }

        $roles = $this->userModel->getAllRoles();

        $active_page = 'pengguna';

        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/pengguna/edit-pengguna.php';
    }

    public function update($id) {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'pengguna');
            exit;
        }

        $userId = (int)$id;

        $data = [
            'username' => trim($_POST['username']),
            'id_role'  => (int)$_POST['id_role'],
            'telepon'  => trim($_POST['telepon'] ?? ''),
            'alamat'   => trim($_POST['alamat'] ?? '')
        ];

        if (empty($data['id_role'])) {
            $_SESSION['flash_error'] = "Role wajib dipilih!";
            header('Location: ' . BASE_URL . 'pengguna/edit/' . $userId);
            exit;
        }

        $password_baru = $_POST['password'] ?? '';
        if (!empty($password_baru)) {
            if (strlen($password_baru) < 6) {
                $_SESSION['flash_error'] = "Password baru minimal 6 karakter!";
                header('Location: ' . BASE_URL . 'pengguna/edit/' . $userId);
                exit;
            }
            $data['password'] = $password_baru;
        }

        if ($this->userModel->updateUser($userId, $data)) {
            $_SESSION['flash_success'] = "Data pengguna berhasil diperbarui!";
            if ($userId == $_SESSION['user_id']) {
                $_SESSION['username'] = $data['username'];
            }
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui data.";
        }

        header('Location: ' . BASE_URL . 'pengguna/edit/' . $userId);
        exit;
    }
    public function hapus($id) {
        session_start();

        $userId = (int)$id;

        if ($userId == $_SESSION['user_id']) {
            $_SESSION['flash_error'] = "Kamu tidak bisa menghapus akun sendiri!";
            header('Location: ' . BASE_URL . 'pengguna');
            exit;
        }

        $stmt = Database::getInstance()->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$userId])) {
            $_SESSION['flash_success'] = "Pengguna berhasil dihapus!";
        } else {
            $_SESSION['flash_error'] = "Gagal menghapus pengguna!";
        }

        header('Location: ' . BASE_URL . 'pengguna');
        exit;
    }
}