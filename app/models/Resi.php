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
        $total = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Hitung status berdasarkan tabel status_pengiriman
        $stmt = $this->db->prepare("
            SELECT sp.status, COUNT(*) AS total
            FROM status_pengiriman sp
            INNER JOIN (
                SELECT id_resi, MAX(id_status) AS max_status
                FROM status_pengiriman
                GROUP BY id_resi
            ) latest ON latest.max_status = sp.id_status
            JOIN resi r ON sp.id_resi = r.id
            WHERE r.user_id = :user_id
            GROUP BY sp.status
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $counts = ['Terkirim' => 0, 'Dalam Proses' => 0, 'Tertunda' => 0];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $status = $row['status'];
            if (isset($counts[$status])) {
                $counts[$status] = (int) $row['total'];
            }
        }

        return [
            'total' => $total,
            'terkirim' => $counts['Terkirim'],
            'proses' => $counts['Dalam Proses'],
            'tertunda' => $counts['Tertunda']
        ];
    }



    public function getStatsAll()
    {
        $tot = (int) $this->db->query("SELECT COUNT(*) AS total FROM resi")->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->query("
            SELECT sp.status, COUNT(*) AS total
            FROM status_pengiriman sp
            INNER JOIN (
                SELECT id_resi, MAX(id_status) AS max_status
                FROM status_pengiriman
                GROUP BY id_resi
            ) latest ON latest.max_status = sp.id_status
            GROUP BY sp.status
        ");
        $counts = ['Terkirim' => 0, 'Dalam Proses' => 0, 'Tertunda' => 0];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $status = $row['status'];
            if (isset($counts[$status])) {
                $counts[$status] = (int) $row['total'];
            }
        }

        return [
            'total' => $tot,
            'terkirim' => $counts['Terkirim'],
            'proses' => $counts['Dalam Proses'],
            'tertunda' => $counts['Tertunda']
        ];
    }


    public function getRecentResi($userId, $limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT r.id, r.nomor_resi, r.nama_penerima, r.alamat_tujuan,
                   (SELECT sp.status FROM status_pengiriman sp WHERE sp.id_resi = r.id ORDER BY sp.id_status DESC LIMIT 1) AS status_pengiriman,
                   r.status_pembayaran, r.tanggal_dibuat
            FROM resi r
            WHERE r.user_id = :user_id 
            ORDER BY r.tanggal_dibuat DESC LIMIT :limit
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getDetailedResi($userId)
    {
        $query = "SELECT r.id, r.nomor_resi, r.nama_pengirim, r.telpon_pengirim, r.kota_pengirim, r.alamat_asal,
                         r.nama_penerima, r.telpon_penerima, r.kota_penerima, r.alamat_tujuan,
                         r.jumlah_paket, r.berat_total, r.total_jarak, r.total_ongkir,
                         (SELECT sp.status FROM status_pengiriman sp WHERE sp.id_resi = r.id ORDER BY sp.id_status DESC LIMIT 1) AS status_pengiriman,
                         r.status_pembayaran, r.tanggal_dibuat, r.origin_city_code, r.destination_city_code
                 FROM resi r
                 WHERE r.user_id = :user_id
                 ORDER BY r.tanggal_dibuat DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getDetailedResiAdmin()
    {
        $query = "SELECT r.id, r.user_id, r.nomor_resi, r.nama_pengirim, r.telpon_pengirim, r.kota_pengirim, r.alamat_asal,
                         r.nama_penerima, r.telpon_penerima, r.kota_penerima, r.alamat_tujuan,
                         r.jumlah_paket, r.berat_total, r.total_jarak, r.total_ongkir,
                         (SELECT sp.status FROM status_pengiriman sp WHERE sp.id_resi = r.id ORDER BY sp.id_status DESC LIMIT 1) AS status_pengiriman,
                         r.status_pembayaran, r.tanggal_dibuat, r.origin_city_code, r.destination_city_code
                 FROM resi r
                 ORDER BY r.tanggal_dibuat DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById($id, $userId)
    {
        $query = "SELECT r.id, r.nomor_resi, r.nama_pengirim, r.telpon_pengirim, r.kota_pengirim, r.alamat_asal,
                         r.nama_penerima, r.telpon_penerima, r.kota_penerima, r.alamat_tujuan,
                         r.jumlah_paket, r.berat_total, r.total_jarak, r.total_ongkir,
                         (SELECT sp.status FROM status_pengiriman sp WHERE sp.id_resi = r.id ORDER BY sp.id_status DESC LIMIT 1) AS status_pengiriman,
                         r.status_pembayaran, r.tanggal_dibuat, r.origin_city_code, r.destination_city_code
                 FROM resi r
                 WHERE r.id = :id AND r.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByIdAdmin($id)
    {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   (SELECT sp.status FROM status_pengiriman sp WHERE sp.id_resi = r.id ORDER BY sp.id_status DESC LIMIT 1) AS status_pengiriman
            FROM resi r
            WHERE r.id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add(array $data)
    {
        $withStatus = !empty($data['status_pembayaran']);

        $columns = "user_id, nomor_resi, nama_pengirim, telpon_pengirim, kota_pengirim, origin_city_code, alamat_asal,
                    nama_penerima, telpon_penerima, kota_penerima, destination_city_code, alamat_tujuan,
                    jumlah_paket, berat_total, total_jarak, total_ongkir, tanggal_dibuat";
        $placeholders = ":user_id, :nomor_resi, :nama_pengirim, :telpon_pengirim, :kota_pengirim, :origin_city_code, :alamat_asal,
                    :nama_penerima, :telpon_penerima, :kota_penerima, :destination_city_code, :alamat_tujuan,
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
        $stmt->bindValue(':origin_city_code', $data['origin_city_code']);
        $stmt->bindValue(':alamat_asal', $data['alamat_asal']);
        $stmt->bindValue(':nama_penerima', $data['nama_penerima']);
        $stmt->bindValue(':telpon_penerima', $data['telpon_penerima']);
        $stmt->bindValue(':kota_penerima', $data['kota_penerima']);
        $stmt->bindValue(':destination_city_code', $data['destination_city_code']);
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
        return (int) $this->db->lastInsertId();
    }

    public function updateById($id, $userId, array $data)
    {
        $sql = "UPDATE resi SET
                    nomor_resi = :nomor_resi,
                    nama_pengirim = :nama_pengirim,
                    telpon_pengirim = :telpon_pengirim,
                    kota_pengirim = :kota_pengirim,
                    alamat_asal = :alamat_asal,
                    nama_penerima = :nama_penerima,
                    telpon_penerima = :telpon_penerima,
                    kota_penerima = :kota_penerima,
                    origin_city_code = :origin_city_code,
                    destination_city_code = :destination_city_code,
                    alamat_tujuan = :alamat_tujuan,
                    jumlah_paket = :jumlah_paket,
                    berat_total = :berat_total,
                    total_jarak = :total_jarak,
                    total_ongkir = :total_ongkir,
                    status_pembayaran = :status_pembayaran
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nomor_resi', $data['nomor_resi']);
        $stmt->bindValue(':nama_pengirim', $data['nama_pengirim']);
        $stmt->bindValue(':telpon_pengirim', $data['telpon_pengirim']);
        $stmt->bindValue(':kota_pengirim', $data['kota_pengirim']);
        $stmt->bindValue(':alamat_asal', $data['alamat_asal']);
        $stmt->bindValue(':nama_penerima', $data['nama_penerima']);
        $stmt->bindValue(':telpon_penerima', $data['telpon_penerima']);
        $stmt->bindValue(':kota_penerima', $data['kota_penerima']);
        $stmt->bindValue(':origin_city_code', $data['origin_city_code']);
        $stmt->bindValue(':destination_city_code', $data['destination_city_code']);
        $stmt->bindValue(':alamat_tujuan', $data['alamat_tujuan']);
        $stmt->bindValue(':jumlah_paket', $data['jumlah_paket'], PDO::PARAM_INT);
        $stmt->bindValue(':berat_total', $data['berat_total']);
        $stmt->bindValue(':total_jarak', $data['total_jarak']);
        $stmt->bindValue(':total_ongkir', $data['total_ongkir']);
        $stmt->bindValue(':status_pembayaran', $data['status_pembayaran']);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function deleteById($id)
    {
        try {
            $this->db->beginTransaction();

            // Hapus status terkait terlebih dahulu agar tidak melanggar FK
            $stmtStatus = $this->db->prepare("DELETE FROM status_pengiriman WHERE id_resi = :id_resi");
            $stmtStatus->bindValue(':id_resi', $id, PDO::PARAM_INT);
            $stmtStatus->execute();

            $stmt = $this->db->prepare("DELETE FROM resi WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function insertStatusPengiriman(int $resiId, int $userId, string $status): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO status_pengiriman (id_resi, id_user, status)
            VALUES (:id_resi, :id_user, :status)
        ");
        $stmt->bindValue(':id_resi', $resiId, PDO::PARAM_INT);
        $stmt->bindValue(':id_user', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status);
        return $stmt->execute();
    }

    public function getLatestStatus(int $resiId): ?string
    {
        $stmt = $this->db->prepare("
            SELECT status 
            FROM status_pengiriman 
            WHERE id_resi = :id_resi 
            ORDER BY id_status DESC 
            LIMIT 1
        ");
        $stmt->bindValue(':id_resi', $resiId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['status'] : null;
    }
}
