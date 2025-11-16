<?php

require_once __DIR__ . '/../../config/database.php';

class Resi
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil seluruh data resi untuk ditampilkan di dashboard.
     */
    public function getAll()
    {
        $query = "SELECT r.*, u.username 
            FROM resi r 
            LEFT JOIN users u ON r.user_id = u.id 
            ORDER BY r.tanggal_dibuat DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
