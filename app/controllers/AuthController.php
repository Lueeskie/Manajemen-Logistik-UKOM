<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($username, $password);
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header('Location: ' . BASE_URL . 'dashboard');
                exit;
            } else {
                $error = "Username atau password salah!";
                require __DIR__ . '/../views/login.php';
            }
        } else {
            require __DIR__ . '/../views/login.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ' . BASE_URL . 'login');
        exit;
    }
}