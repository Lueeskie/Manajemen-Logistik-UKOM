<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';

class ProfileController {
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

        $userId = $_SESSION['user_id'];
        $user_info = $this->userModel->getProfile($userId);
        if (!$user_info) {
            $_SESSION['flash_error'] = "Data profil tidak ditemukan.";
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }
        
        $isEditMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
        $flash_success = $_SESSION['flash_success'] ?? '';
        $flash_error = $_SESSION['flash_error'] ?? '';
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require __DIR__ . '/../views/profile.php';
    }

    public function update() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $data = [
            'telepon' => trim($_POST['telepon'] ?? ''),
            'alamat' => trim($_POST['alamat'] ?? '')
        ];

        if (empty($data['telepon']) && empty($data['alamat'])) {
            $_SESSION['flash_error'] = "Minimal isi satu field.";
            header('Location: ' . BASE_URL . 'profile?edit=true');
            exit;
        }

        if ($this->userModel->updateProfile($userId, $data)) {
            $_SESSION['flash_success'] = "Profil berhasil diperbarui!";
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui profil. Coba lagi.";
        }

        header('Location: ' . BASE_URL . 'profile');
        exit;
    }

    public function changePassword() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Method tidak diizinkan'
            ]);
            exit;
        }

        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Semua field harus diisi'
            ]);
            exit;
        }
        
        if ($newPassword !== $confirmPassword) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Konfirmasi password tidak cocok!'
            ]);
            exit;
        }

        if ($this->userModel->changePassword($userId, $currentPassword, $newPassword)) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Password berhasil diubah!'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Password saat ini salah atau gagal mengubah!'
            ]);
        }
        exit;
    }
}