<?php

require_once __DIR__ . '/../../config/database.php';

class Resi
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getStats($userId)
    {
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

    public function getRecentResi($userId, $limit = 10)
    {
        $stmt = $this->db->prepare("SELECT id, nomor_resi, nama_penerima, alamat_tujuan, status_pengiriman, status_pembayaran, tanggal_dibuat 
                                    FROM resi WHERE user_id = :user_id 
                                    ORDER BY tanggal_dibuat DESC LIMIT :limit");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDetailedResi($userId)
    {
        $query = "SELECT id, nomor_resi, nama_pengirim, telpon_pengirim, kota_pengirim, alamat_asal,
                         nama_penerima, telpon_penerima, kota_penerima, alamat_tujuan,
                         jumlah_paket, berat_total, total_jarak, total_ongkir,
                         status_pengiriman, status_pembayaran, tanggal_dibuat,origin_city_code,destination_city_code
                  FROM resi
                  WHERE user_id = :user_id
                  ORDER BY tanggal_dibuat DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $withStatus = !empty($data['status_pembayaran']);

        $columns = "user_id, nomor_resi, nama_pengirim, telpon_pengirim, kota_pengirim, alamat_asal,
                    nama_penerima, telpon_penerima, kota_penerima, alamat_tujuan,
                    jumlah_paket, berat_total, total_jarak, total_ongkir, tanggal_dibuat";
        $placeholders = ":user_id, :nomor_resi, :nama_pengirim, :telpon_pengirim, :kota_pengirim, :alamat_asal,
                    :nama_penerima, :telpon_penerima, :kota_penerima, :alamat_tujuan,
                    :jumlah_paket, :berat_total, :total_jarak, :total_ongkir, :tanggal_dibuat";

        if ($withStatus) {
            $columns .= ", status_pembayaran";
            $placeholders .= ", :status_pembayaran";
        }

        $sql = "INSERT INTO resi ($columns) VALUES ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nomor_resi', $data['nomor_resi']);
        $stmt->bindValue(':nama_pengirim', $data['nama_pengirim']);
        $stmt->bindValue(':telpon_pengirim', $data['telpon_pengirim']);
        $stmt->bindValue(':kota_pengirim', $data['kota_pengirim']);
        $stmt->bindValue(':alamat_asal', $data['alamat_asal']);
        $stmt->bindValue(':nama_penerima', $data['nama_penerima']);
        $stmt->bindValue(':telpon_penerima', $data['telpon_penerima']);
        $stmt->bindValue(':kota_penerima', $data['kota_penerima']);
        $stmt->bindValue(':alamat_tujuan', $data['alamat_tujuan']);
        $stmt->bindValue(':jumlah_paket', $data['jumlah_paket'], PDO::PARAM_INT);
        $stmt->bindValue(':berat_total', $data['berat_total']);
        $stmt->bindValue(':total_jarak', $data['total_jarak']);
        $stmt->bindValue(':total_ongkir', $data['total_ongkir']);
        $stmt->bindValue(':tanggal_dibuat', $data['tanggal_dibuat']);

        if ($withStatus) {
            $stmt->bindValue(':status_pembayaran', $data['status_pembayaran']);
        }

        $stmt->execute();
    }

    public function deleteById($id, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM resi WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
