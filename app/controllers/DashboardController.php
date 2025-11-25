<?php

require_once __DIR__ . '/../models/Resi.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../../config/config.php';

class DashboardController {
    private $resiModel;
    private $userModel;
    
    public function __construct() {
        $this->resiModel = new Resi();
        $this->userModel = new User();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $stats = $this->resiModel->getStats($userId);
        $recentResi = $this->resiModel->getRecentResi($userId);
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

        $active_page = 'dashboard';
        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/dashboard.php';
    }
}