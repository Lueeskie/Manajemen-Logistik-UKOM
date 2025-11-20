<?php

require_once __DIR__ . '/../../config/database.php';

class InventarisModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Fetch all inventaris
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM inventaris_barang ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add new inventaris
    public function create($data) {
        try {
            // Cek apakah id_resi sudah ada (opsional, untuk hindari duplikat)
            $checkStmt = $this->db->prepare("SELECT id_barang FROM inventaris_barang WHERE id_resi = :id_resi");
            $checkStmt->bindParam(':id_resi', $data['id_resi']);
            $checkStmt->execute();
            if ($checkStmt->fetch()) {
                return false; // Sudah ada
            }

            $stmt = $this->db->prepare("INSERT INTO inventaris_barang (id_resi, nama_barang, berat, jumlah, deskripsi, status) VALUES (:id_resi, :nama_barang, :berat, :jumlah, :deskripsi, :status)");
            $stmt->bindParam(':id_resi', $data['id_resi']);
            $stmt->bindParam(':nama_barang', $data['nama_barang']);
            $stmt->bindParam(':berat', $data['berat']);
            $stmt->bindParam(':jumlah', $data['jumlah']);
            $stmt->bindParam(':deskripsi', $data['deskripsi']);
            $stmt->bindParam(':status', $data['status']);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Create inventaris error: " . $e->getMessage()); // Log untuk debug
            return false;
        }
    }

    // Get by ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM inventaris_barang WHERE id_barang = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    // Update inventaris
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("UPDATE inventaris_barang SET id_resi = :id_resi, nama_barang = :nama_barang, berat = :berat, jumlah = :jumlah, deskripsi = :deskripsi, status = :status WHERE id_barang = :id");
            $stmt->bindParam(':id_resi', $data['id_resi']);
            $stmt->bindParam(':nama_barang', $data['nama_barang']);
            $stmt->bindParam(':berat', $data['berat']);
            $stmt->bindParam(':jumlah', $data['jumlah']);
            $stmt->bindParam(':deskripsi', $data['deskripsi']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update inventaris error: " . $e->getMessage());
            return false;
        }
    }

    // Delete inventaris
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM inventaris_barang WHERE id_barang = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Delete inventaris error: " . $e->getMessage());
            return false;
        }
    }
}
?>