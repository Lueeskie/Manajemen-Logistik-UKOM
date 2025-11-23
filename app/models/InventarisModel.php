<!-- inventarisModel.php -->
<?php

require_once __DIR__ . '/../../config/database.php';

class InventarisModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Fetch all inventaris (hanya yang belum dikirim)
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM inventaris_barang WHERE status != 'Delivered' ORDER BY nomor_resi, created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch histori barang yang sudah dikirim
    public function getHistori() {
        $stmt = $this->db->prepare("SELECT * FROM inventaris_barang WHERE status = 'Delivered' ORDER BY nomor_resi, updated_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add new inventaris
    public function create($data) {
        try {
            // id_resi diisi empty string karena NOT NULL
            $id_resi = ''; // Empty string untuk CHAR(3) NOT NULL
            
            error_log("Create inventaris: nomor_resi = " . $data['nomor_resi']);

            // Insert dengan id_resi empty string
            $stmt = $this->db->prepare("
                INSERT INTO inventaris_barang 
                (id_resi, nomor_resi, nama_barang, berat, jumlah, deskripsi, status) 
                VALUES 
                (:id_resi, :nomor_resi, :nama_barang, :berat, :jumlah, :deskripsi, :status)
            ");
            
            $stmt->bindParam(':id_resi', $id_resi, PDO::PARAM_STR);
            $stmt->bindParam(':nomor_resi', $data['nomor_resi'], PDO::PARAM_STR);
            $stmt->bindParam(':nama_barang', $data['nama_barang'], PDO::PARAM_STR);
            $stmt->bindParam(':berat', $data['berat']);
            $stmt->bindParam(':jumlah', $data['jumlah'], PDO::PARAM_INT);
            $stmt->bindParam(':deskripsi', $data['deskripsi'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            
            $result = $stmt->execute();
            
            if ($result) {
                error_log("Create inventaris SUCCESS for resi: " . $data['nomor_resi']);
            } else {
                error_log("Create inventaris FAILED - execute returned false");
                error_log("Error info: " . print_r($stmt->errorInfo(), true));
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Create inventaris EXCEPTION: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            error_log("Error Info: " . print_r($e->errorInfo, true));
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
            // id_resi diisi empty string
            $id_resi = '';
            
            $stmt = $this->db->prepare("
                UPDATE inventaris_barang 
                SET id_resi = :id_resi, 
                    nomor_resi = :nomor_resi, 
                    nama_barang = :nama_barang, 
                    berat = :berat, 
                    jumlah = :jumlah, 
                    deskripsi = :deskripsi, 
                    status = :status 
                WHERE id_barang = :id
            ");
            
            $stmt->bindParam(':id_resi', $id_resi, PDO::PARAM_STR);
            $stmt->bindParam(':nomor_resi', $data['nomor_resi'], PDO::PARAM_STR);
            $stmt->bindParam(':nama_barang', $data['nama_barang'], PDO::PARAM_STR);
            $stmt->bindParam(':berat', $data['berat']);
            $stmt->bindParam(':jumlah', $data['jumlah'], PDO::PARAM_INT);
            $stmt->bindParam(':deskripsi', $data['deskripsi'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
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

    // Kirim barang (batch update status ke 'Delivered')
    public function kirimBarang($ids) {
        try {
            if (empty($ids) || !is_array($ids)) {
                error_log("kirimBarang error: IDs empty or not array");
                return false;
            }

            error_log("kirimBarang: Processing " . count($ids) . " items");
            error_log("kirimBarang: IDs = " . print_r($ids, true));

            // Buat placeholder untuk prepared statement
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            // Try with updated_at first
            try {
                $sql = "UPDATE inventaris_barang SET status = 'Delivered', updated_at = NOW() WHERE id_barang IN ($placeholders)";
                error_log("kirimBarang SQL: " . $sql);
                
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute($ids);
                
                $rowCount = $stmt->rowCount();
                error_log("kirimBarang: Affected rows = " . $rowCount);
                
                return $result && $rowCount > 0;
            } catch (PDOException $e) {
                // If updated_at doesn't exist, try without it
                error_log("kirimBarang: Trying without updated_at. Error was: " . $e->getMessage());
                
                $sql = "UPDATE inventaris_barang SET status = 'Delivered' WHERE id_barang IN ($placeholders)";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute($ids);
                
                $rowCount = $stmt->rowCount();
                error_log("kirimBarang (no updated_at): Affected rows = " . $rowCount);
                
                return $result && $rowCount > 0;
            }
        } catch (PDOException $e) {
            error_log("Kirim barang error: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
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

    // Search nomor resi dari TABEL RESI untuk autocomplete
    public function searchResi($search = '') {
        try {
            error_log("searchResi called with search: '$search'");
            
            if (empty($search)) {
                // Ambil semua nomor resi dari tabel resi (limit 50)
                $sql = "SELECT nomor_resi FROM resi ORDER BY tanggal_dibuat DESC LIMIT 50";
                error_log("Executing SQL: " . $sql);
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
            } else {
                // Search nomor resi yang mengandung keyword
                $searchTerm = "%$search%";
                $sql = "SELECT nomor_resi FROM resi WHERE nomor_resi LIKE :search ORDER BY tanggal_dibuat DESC LIMIT 20";
                error_log("Executing SQL: " . $sql . " with search: " . $searchTerm);
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':search', $searchTerm);
                $stmt->execute();
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("searchResi found " . count($results) . " results");
            error_log("Results: " . print_r($results, true));
            
            return $results;
            
        } catch (PDOException $e) {
            error_log("Search resi error: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            
            // FALLBACK: Jika tabel resi tidak ada, coba dari inventaris_barang
            error_log("Trying fallback to inventaris_barang table");
            
            try {
                if (empty($search)) {
                    $stmt = $this->db->prepare("
                        SELECT DISTINCT nomor_resi 
                        FROM inventaris_barang 
                        WHERE nomor_resi IS NOT NULL AND nomor_resi != ''
                        ORDER BY created_at DESC 
                        LIMIT 50
                    ");
                    $stmt->execute();
                } else {
                    $searchTerm = "%$search%";
                    $stmt = $this->db->prepare("
                        SELECT DISTINCT nomor_resi 
                        FROM inventaris_barang 
                        WHERE nomor_resi LIKE :search 
                        AND nomor_resi IS NOT NULL 
                        AND nomor_resi != ''
                        ORDER BY created_at DESC 
                        LIMIT 20
                    ");
                    $stmt->bindParam(':search', $searchTerm);
                    $stmt->execute();
                }
                
                $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
                error_log("Fallback found " . count($results) . " results");
                
                return $results;
            } catch (PDOException $e2) {
                error_log("Fallback search resi error: " . $e2->getMessage());
                return [];
            }
        }
    }

    // Get items by nomor_resi (untuk lihat semua barang dalam 1 resi)
    public function getByNomorResi($nomor_resi) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM inventaris_barang WHERE nomor_resi = :nomor_resi ORDER BY created_at DESC");
            $stmt->bindParam(':nomor_resi', $nomor_resi);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get by nomor resi error: " . $e->getMessage());
            return [];
        }
    }

    // Get summary per resi (untuk statistik)
    public function getResiSummary() {
        try {
            $sql = "SELECT 
                        nomor_resi,
                        COUNT(*) as total_items,
                        SUM(jumlah) as total_quantity,
                        SUM(berat * jumlah) as total_weight,
                        MIN(created_at) as first_created,
                        MAX(created_at) as last_created
                    FROM inventaris_barang
                    WHERE status != 'Delivered'
                    GROUP BY nomor_resi
                    ORDER BY last_created DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get resi summary error: " . $e->getMessage());
            return [];
        }
    }
}
?>