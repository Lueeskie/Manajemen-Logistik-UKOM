<?php

require_once __DIR__ . '/../../config/database.php';

class InventarisModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Fetch all inventaris (hanya yang belum dikirim)
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM inventaris_barang WHERE status != 'Sent' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch histori barang yang sudah dikirim
    public function getHistori() {
        $stmt = $this->db->prepare("SELECT * FROM inventaris_barang WHERE status = 'Sent' ORDER BY updated_at DESC");
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
            error_log("Create inventaris error: " . $e->getMessage());
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

    // Kirim barang (batch update status ke 'Sent')
    public function kirimBarang($ids) {
        try {
            if (empty($ids) || !is_array($ids)) {
                return false;
            }

            // Buat placeholder untuk prepared statement
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            $stmt = $this->db->prepare("UPDATE inventaris_barang SET status = 'Sent', updated_at = NOW() WHERE id_barang IN ($placeholders)");
            
            return $stmt->execute($ids);
        } catch (PDOException $e) {
            error_log("Kirim barang error: " . $e->getMessage());
            return false;
        }
    }

    // Get multiple items by IDs (untuk validasi sebelum kirim)
    public function getByIds($ids) {
        try {
            if (empty($ids) || !is_array($ids)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->db->prepare("SELECT * FROM inventaris_barang WHERE id_barang IN ($placeholders)");
            $stmt->execute($ids);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get by IDs error: " . $e->getMessage());
            return [];
        }
    }

    // Search nomor resi dari tabel resi untuk autocomplete
    public function searchResi($search = '') {
        try {
            if (empty($search)) {
                // Ambil semua nomor resi (limit 50)
                $stmt = $this->db->prepare("SELECT nomor_resi FROM resi ORDER BY nomor_resi DESC LIMIT 50");
                $stmt->execute();
            } else {
                // Search nomor resi yang mengandung keyword
                $searchTerm = "%$search%";
                $stmt = $this->db->prepare("SELECT nomor_resi FROM resi WHERE nomor_resi LIKE :search ORDER BY nomor_resi DESC LIMIT 20");
                $stmt->bindParam(':search', $searchTerm);
                $stmt->execute();
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $results;
        } catch (PDOException $e) {
            error_log("Search resi error: " . $e->getMessage());
            return [];
        }
    }
}
?>