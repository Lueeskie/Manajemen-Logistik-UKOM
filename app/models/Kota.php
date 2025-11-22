<?php

require_once __DIR__ . '/../../config/database.php';

class Kota
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all() {
        $sql = "SELECT id_kota, nama FROM kota ORDER BY nama ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

