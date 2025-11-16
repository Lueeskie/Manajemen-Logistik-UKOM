<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        session_start(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header('Location: ' . BASE_URL . 'dashboard');
                exit;
            } else {
                $error = "Email atau password salah!";
                require __DIR__ . '/../views/login.php';
            }
        } else {
            require __DIR__ . '/../views/login.php';
        }
    }

    public function register() {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $agree_terms = isset($_POST['agreeTerms']);

            if (empty($username) || empty($email) || empty($password)) {
                $error = "Semua field harus diisi!";
                require __DIR__ . '/../views/register.php';
                return;
            }

            if ($password !== $confirm_password) {
                $error = "Password konfirmasi tidak cocok!";
                require __DIR__ . '/../views/register.php';
                return;
            }

            if (!$agree_terms) {
                $error = "Anda harus setuju dengan syarat & ketentuan!";
                require __DIR__ . '/../views/register.php';
                return;
            }

            if ($this->userModel->register($username, $email, $password)) {
                $_SESSION['flash_success'] = "Akun berhasil dibuat! Silakan login.";
                header('Location: ' . BASE_URL . 'login');
                exit;
            } else {
                $error = "Username atau email sudah terdaftar! Coba yang lain.";
                require __DIR__ . '/../views/register.php';
            }
        } else {
            require __DIR__ . '/../views/register.php';
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}
