<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';

class ProfileController
{
    private $userModel;

    public function __construct()
    {
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

    public function update()
    {
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
        $currentProfile = $this->userModel->getProfile($userId);

        if (!$currentProfile) {
            $_SESSION['flash_error'] = "Data profil tidak ditemukan.";
            header('Location: ' . BASE_URL . 'profile');
            exit;
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'telepon' => trim($_POST['telepon'] ?? ''),
            'alamat' => trim($_POST['alamat'] ?? '')
        ];

        if (empty($data['username'])) {
            $_SESSION['flash_error'] = "Username tidak boleh kosong.";
            header('Location: ' . BASE_URL . 'profile?edit=true');
            exit;
        }

        if (empty($data['telepon']) && empty($data['alamat'])) {
            $_SESSION['flash_error'] = "Minimal isi satu field selain username.";
            header('Location: ' . BASE_URL . 'profile?edit=true');
            exit;
        }

        $hasNewImage = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($hasNewImage) {
            $uploadResult = $this->handleFotoUpload($_FILES['foto'], $userId);

            if (!$uploadResult['success']) {
                $_SESSION['flash_error'] = $uploadResult['message'];
                header('Location: ' . BASE_URL . 'profile?edit=true');
                exit;
            }

            $data['foto'] = $uploadResult['filename'];

            if (!empty($currentProfile['foto'])) {
                $oldImagePath = __DIR__ . '/../../public/uploads/profile/' . $currentProfile['foto'];
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }
        }

        if ($this->userModel->updateProfile($userId, $data)) {
            $_SESSION['flash_success'] = "Profil berhasil diperbarui!";
            $_SESSION['username'] = $data['username'];
        } else {
            $_SESSION['flash_error'] = "Gagal memperbarui profil. Coba lagi.";
        }

        header('Location: ' . BASE_URL . 'profile');
        exit;
    }

    public function changePassword()
    {
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

    private function handleFotoUpload(array $file, $userId)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Gagal mengunggah gambar. Coba lagi.'
            ];
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2 MB

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WEBP.'
            ];
        }

        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'message' => 'Ukuran gambar maksimal 2MB.'
            ];
        }

        $uploadDir = __DIR__ . '/../../public/uploads/profile/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $sanitizedOriginal = preg_replace('/[^a-zA-Z0-9-_\.]/', '-', strtolower($file['name']));
        $extension = strtolower(pathinfo($sanitizedOriginal, PATHINFO_EXTENSION));
        $baseName = pathinfo($sanitizedOriginal, PATHINFO_FILENAME);

        if (empty($extension)) {
            $extension = $mimeType === 'image/png' ? 'png' : ($mimeType === 'image/webp' ? 'webp' : 'jpg');
        }

        $fileName = $baseName . '_' . $userId . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'message' => 'Gagal menyimpan gambar ke server.'
            ];
        }

        return [
            'success' => true,
            'filename' => $fileName
        ];
    }
}
