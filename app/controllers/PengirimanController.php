<?php

require_once __DIR__ . '/../models/Resi.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';


class PengirimanController
{
    private $resiModel;
    private $userModel;

    public function __construct()
    {
        $this->resiModel = new Resi();
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
        $stats = $this->resiModel->getStats($userId);
        $detailResi = $this->resiModel->getDetailedResi($userId);
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

        require __DIR__ . '/../views/pengiriman.php';
    }
}
