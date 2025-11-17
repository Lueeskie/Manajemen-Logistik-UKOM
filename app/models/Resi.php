<?php

require_once __DIR__ . '/../../config/database.php';

class Resi {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getStats($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM resi WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM resi WHERE user_id = :user_id AND status_pengiriman = 'Terkirim'");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $terkirim = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM resi WHERE user_id = :user_id AND status_pengiriman = 'Dalam Proses'");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $proses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM resi WHERE user_id = :user_id AND status_pengiriman = 'Tertunda'");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $tertunda = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'total' => $total,
            'terkirim' => $terkirim,
            'proses' => $proses,
            'tertunda' => $tertunda
        ];
    }

    public function getRecentResi($userId, $limit = 10) {
        $stmt = $this->db->prepare("SELECT id, nomor_resi, nama_penerima, alamat_tujuan, status_pengiriman, status_pembayaran, tanggal_dibuat 
                                    FROM resi WHERE user_id = :user_id 
                                    ORDER BY tanggal_dibuat DESC LIMIT :limit");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}