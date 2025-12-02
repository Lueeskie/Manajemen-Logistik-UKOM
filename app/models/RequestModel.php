<?php
require_once __DIR__ . '/../../config/database.php';

class RequestModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
    //masukin data ke database/tabel barang_request
    public function createRequest($id_supplier, $nama_barang, $jumlah_barang, $berat, 
                                 $deskripsi = '', $alamat_asal = '', $alamat_tujuan = '', $foto_paket = null) {
        $stmt = $this->db->prepare("INSERT INTO barang_request 
            (id_supplier, nama_barang, jumlah_barang, berat, deskripsi, 
             alamat_asal, alamat_tujuan, foto_paket) 
            VALUES (:id_supplier, :nama_barang, :jumlah_barang, :berat, :deskripsi,
                    :alamat_asal, :alamat_tujuan, :foto_paket)");
        //mengubah placeholder id_supplier dengan value/nilai dari $id_supllier yang sudah diisi user
        $stmt->bindParam(':id_supplier', $id_supplier);
        $stmt->bindParam(':nama_barang', $nama_barang);
        $stmt->bindParam(':jumlah_barang', $jumlah_barang);
        $stmt->bindParam(':berat', $berat);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':alamat_asal', $alamat_asal);
        $stmt->bindParam(':alamat_tujuan', $alamat_tujuan);
        $stmt->bindParam(':foto_paket', $foto_paket);

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
                                    ORDER BY br.created_at ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRequestById($id) {
        $stmt = $this->db->prepare("SELECT * FROM barang_request WHERE id_request = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRequest($id, $data) {
        $fields = [];
        $params = [];
        
        if (isset($data['nama_barang'])) {
            $fields[] = 'nama_barang = ?';
            $params[] = $data['nama_barang'];
        }
        if (isset($data['jumlah_barang'])) {
            $fields[] = 'jumlah_barang = ?';
            $params[] = $data['jumlah_barang'];
        }
        if (isset($data['berat'])) {
            $fields[] = 'berat = ?';
            $params[] = $data['berat'];
        }
        if (isset($data['deskripsi'])) {
            $fields[] = 'deskripsi = ?';
            $params[] = $data['deskripsi'];
        }
        if (isset($data['status'])) {
            $fields[] = 'status = ?';
            $params[] = $data['status'];
        }
        if (isset($data['alamat_asal'])) {
            $fields[] = 'alamat_asal = ?';
            $params[] = $data['alamat_asal'];
        }
        if (isset($data['alamat_tujuan'])) {
            $fields[] = 'alamat_tujuan = ?';
            $params[] = $data['alamat_tujuan'];
        }
        if (isset($data['foto_paket'])) {
            $fields[] = 'foto_paket = ?';
            $params[] = $data['foto_paket'];
        }

        if (empty($fields)) return false;

        $params[] = $id;
        $query = "UPDATE barang_request SET " . implode(', ', $fields) . " WHERE id_request = ?";

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function deleteRequest($id) {
        $stmt = $this->db->prepare("DELETE FROM barang_request WHERE id_request = ?");
        return $stmt->execute([$id]);
    }
}