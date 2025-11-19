<?php
require_once __DIR__ . '/../../config/database.php';

class RequestModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createRequest($id_supplier, $nama_barang, $jumlah_barang, $berat, $deskripsi = '') {
        $stmt = $this->db->prepare("INSERT INTO barang_request 
            (id_supplier, nama_barang, jumlah_barang, berat, deskripsi) 
            VALUES (:id_supplier, :nama_barang, :jumlah_barang, :berat, :deskripsi)");
        
        $stmt->bindParam(':id_supplier', $id_supplier);
        $stmt->bindParam(':nama_barang', $nama_barang);
        $stmt->bindParam(':jumlah_barang', $jumlah_barang);
        $stmt->bindParam(':berat', $berat);
        $stmt->bindParam(':deskripsi', $deskripsi);

        return $stmt->execute();
    }

    public function getRequestsBySupplier($id_supplier) {
        $stmt = $this->db->prepare("
            SELECT * FROM barang_request 
            WHERE id_supplier = :id_supplier 
            ORDER BY created_at ASC, id_request ASC
        ");
        $stmt->bindParam(':id_supplier', $id_supplier);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRequests() {
        $stmt = $this->db->prepare("SELECT br.*, u.username, u.email 
                                    FROM barang_request br 
                                    JOIN users u ON br.id_supplier = u.id 
                                    ORDER BY br.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}