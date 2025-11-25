<?php

require_once __DIR__ . '/../models/Kota.php';
require_once __DIR__ . '/../models/Resi.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/config.php';


class PengirimanController
{
    private $resiModel;
    private $userModel;
    private $kotaModel;

    public function __construct()
    {
        $this->resiModel = new Resi();
        $this->userModel = new User();
        $this->kotaModel = new Kota();
    }


    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? '';
        $isAdmin = $role === 'Admin';
        $isDriver = $role === 'Driver';

        if ($isAdmin || $isDriver) {
            $stats = $this->resiModel->getStatsAll();
            $detailResi = $this->resiModel->getDetailedResiAdmin();
        } else {
            $stats = $this->resiModel->getStats($userId);
            $detailResi = $this->resiModel->getDetailedResi($userId);
        }
        $user_info = $this->userModel->getProfile($userId);
        if (!$user_info) {
            $user_info = [
                'username' => $_SESSION['username'] ?? 'User',
                'email' => '',
                'telepon' => '',
                'alamat' => '',
                'foto' => ''
            ];
        }

        $active_page = 'pengiriman';
        
        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/pengiriman.php';
    }

    public function add()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }


        $userId = $_SESSION['user_id'];
        $user_info = $this->userModel->getProfile($userId);

        if (!$user_info) {
            $user_info = [
                'username' => $_SESSION['username'] ?? 'User',
                'email' => '',
                'telepon' => '',
                'alamat' => '',
                'foto' => ''
            ];
        }

        $listKota = $this->kotaModel->all();

        require __DIR__ . '/../views/tambah_resi.php';
    }


    public function store()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }



        $data = [
            'user_id' => $_SESSION['user_id'],
            'nomor_resi' => $this->inputTrim('nomor_resi'),
            'tanggal_dibuat' => $_POST['tanggal_dibuat'] ?? date('Y-m-d'),
            'nama_pengirim' => $this->inputTrim('nama_pengirim'),
            'telpon_pengirim' => $this->inputTrim('telpon_pengirim'),
            'kota_pengirim' => $this->inputTrim('kota_pengirim'),
            'origin_city_code' => $this->inputTrim('origin_city_code'),
            'alamat_asal' => $this->inputTrim('alamat_asal'),
            'nama_penerima' => $this->inputTrim('nama_penerima'),
            'telpon_penerima' => $this->inputTrim('telpon_penerima'),
            'kota_penerima' => $this->inputTrim('kota_penerima'),
            'destination_city_code' => $this->inputTrim('destination_city_code'),
            'alamat_tujuan' => $this->inputTrim('alamat_tujuan'),
            'jumlah_paket' => $this->inputInt('jumlah_paket', 0),
            'berat_total' => $this->inputFloatOrNull('berat_total'),
            'total_jarak' => $this->inputFloatOrNull('total_jarak'),
            'total_ongkir' => $this->inputFloatOrNull('total_ongkir'),
        ];

        if (empty($data['nomor_resi'])) {
            $generated = $this->generateResiCode($data['origin_city_code'], $data['destination_city_code']);
            if (!empty($generated)) {
                $data['nomor_resi'] = $generated;
            }
        }


        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $statusPembayaran = null;
        if ($paymentMethod === 'qris' && ($_POST['status_pembayaran'] ?? '') === 'Terbayar') {
            $statusPembayaran = 'Terbayar';
        }
        $data['status_pembayaran'] = $statusPembayaran;

        $required = ['nomor_resi', 'nama_pengirim', 'telpon_pengirim', 'kota_pengirim', 'origin_city_code', 'alamat_asal', 'nama_penerima', 'telpon_penerima', 'kota_penerima', 'destination_city_code', 'alamat_tujuan'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $_SESSION['flash_error'] = 'Lengkapi semua data wajib.';
                header('Location: ' . BASE_URL . 'pengiriman/tambah');
                exit;
            }
        }

        if ($data['jumlah_paket'] < 1) {
            $_SESSION['flash_error'] = 'Jumlah paket minimal 1.';
            header('Location: ' . BASE_URL . 'pengiriman/tambah');
            exit;
        }

        // Hitung otomatis jarak + ongkir berdasarkan kota asal/tujuan + berat (tarif per km + tarif per kg)
        $beratKg = 0.0;
        if ($data['berat_total'] !== null) {
            $beratKg = (float) $data['berat_total'];
        }
        $tarifPerKm = 200.0;
        $tarifPerKg = 5000.0;
        $hasilHitung = $this->hitungJarakOngkir($data['kota_pengirim'], $data['kota_penerima'], ORS_API_KEY, $tarifPerKm, $tarifPerKg, $beratKg);
        if ($hasilHitung === null) {
            $_SESSION['flash_error'] = 'Gagal menghitung jarak/ongkir. Cek nama kota atau API key.';
            header('Location: ' . BASE_URL . 'pengiriman/tambah');
            exit;
        }
        $data['total_jarak'] = $hasilHitung['jarak_km'];
        $data['total_ongkir'] = $hasilHitung['ongkir'];

        $resiId = $this->resiModel->add($data);
        // Status awal dicatat di tabel status_pengiriman
        if ($resiId > 0) {
            $this->resiModel->insertStatusPengiriman($resiId, (int) $data['user_id'], 'Dalam Proses');
        }

        if (strtolower($data['status_pembayaran']) === 'terbayar') {
            $_SESSION['flash_success'] = 'anda sukses melakukan pembayaran';
        } else {
            $_SESSION['flash_success'] = 'Resi berhasil ditambahkan. Pembayaran COD.';
        }
        header('Location: ' . BASE_URL . 'pengiriman');
    }

    //Endpoint AJAX untuk hitung jarak/ongkir saat user isi kota di form
    public function hitungJarakAjax()
    {
        session_start();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        $asal = trim($payload['asal'] ?? ($_POST['asal'] ?? ''));
        $tujuan = trim($payload['tujuan'] ?? ($_POST['tujuan'] ?? ''));

        if ($asal === '' || $tujuan === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Kota asal dan tujuan wajib diisi.']);
            exit;
        }

        
        $tarifPerKm = 200.0;
        $tarifPerKg = 5000.0;
        $beratKg = 0.0;
        if (isset($payload['berat'])) {
            $beratKg = (float) $payload['berat'];
        }
        $hasil = $this->hitungJarakOngkir($asal, $tujuan, ORS_API_KEY, $tarifPerKm, $tarifPerKg, $beratKg);
        if ($hasil === null) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menghitung jarak/ongkir. Coba cek kembali nama kota.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'jarak_km' => $hasil['jarak_km'],
            'ongkir' => $hasil['ongkir'],
            'jarak_text' => round($hasil['jarak_km'], 2) . ' km'
        ]);
        exit;
    }

    public function detail()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $id = 0;
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
        }
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Resi tidak ditemukan.';
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? '';
        $isAdmin = $role === 'Admin';
        $isDriver = $role === 'Driver';

        $resi = ($isAdmin || $isDriver)
            ? $this->resiModel->getByIdAdmin($id)
            : $this->resiModel->getById($id, $userId);
        if (!$resi) {
            $_SESSION['flash_error'] = 'Resi tidak ditemukan atau bukan milik Anda.';
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }

        $user_info = $this->userModel->getProfile($userId);
        if (!$user_info) {
            $user_info = [
                'username' => $_SESSION['username'] ?? 'User',
                'email' => '',
                'telepon' => '',
                'alamat' => '',
                'foto' => ''
            ];
        }

        require __DIR__ . '/../views/detail_resi.php';
    }

    public function delete()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $id = 0;
        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
        }
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Data tidak valid.';
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }


        $deleted = $this->resiModel->deleteById($id);
        if ($deleted) {
            $_SESSION['flash_success'] = 'Resi berhasil dihapus.';
        } else {
            $_SESSION['flash_error'] = 'Gagal hapus resi atau tidak ditemukan.';
        }
        header('Location: ' . BASE_URL . 'pengiriman');
    }

    public function edit()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Resi tidak ditemukan.';
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? '';
        $isAdmin = $role === 'Admin';
        $isDriver = $role === 'Driver';
        $resi = ($isAdmin || $isDriver)
            ? $this->resiModel->getByIdAdmin($id)
            : $this->resiModel->getById($id, $userId);

        if (!$resi) {
            $_SESSION['flash_error'] = 'Resi tidak ditemukan atau bukan milik Anda.';
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }

        $user_info = $this->userModel->getProfile($userId);
        if (!$user_info) {
            $user_info = [
                'username' => $_SESSION['username'] ?? 'User',
                'email' => '',
                'telepon' => '',
                'alamat' => '',
                'foto' => ''
            ];
        }

        // Ambil daftar kota untuk dropdown pada form edit
        $listKota = $this->kotaModel->all();

        require __DIR__ . '/../views/edit_resi.php';
    }

    public function update()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $role = $_SESSION['role'] ?? '';
        $isDriver = $role === 'Driver';

        $id = $this->inputInt('id', 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Data tidak valid.';
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }

        if ($isDriver) {
            $resi = $this->resiModel->getByIdAdmin($id);

            $statusBaru = trim($_POST['status_pengiriman'] ?? '');
            if ($statusBaru === '') {
                $statusBaru = $resi['status_pengiriman']; 
            }

            if ($statusBaru === $resi['status_pengiriman']) {
                $_SESSION['flash_success'] = 'Status pengiriman tidak berubah.';
            } else {
                $inserted = $this->resiModel->insertStatusPengiriman($id, (int) $_SESSION['user_id'], $statusBaru);
                if ($inserted) {
                    $_SESSION['flash_success'] = 'Status pengiriman diperbarui.';
                } else {
                    $_SESSION['flash_error'] = 'Gagal memperbarui status.';
                }
            }
            header('Location: ' . BASE_URL . 'pengiriman');
            exit;
        }

        $data = [
            'nomor_resi' => $this->inputTrim('nomor_resi'),
            'tanggal_dibuat' => $_POST['tanggal_dibuat'] ?? date('Y-m-d'),
            'nama_pengirim' => $this->inputTrim('nama_pengirim'),
            'telpon_pengirim' => $this->inputTrim('telpon_pengirim'),
            'kota_pengirim' => $this->inputTrim('kota_pengirim'),
            'origin_city_code' => $this->inputTrim('origin_city_code'),
            'alamat_asal' => $this->inputTrim('alamat_asal'),
            'nama_penerima' => $this->inputTrim('nama_penerima'),
            'telpon_penerima' => $this->inputTrim('telpon_penerima'),
            'kota_penerima' => $this->inputTrim('kota_penerima'),
            'destination_city_code' => $this->inputTrim('destination_city_code'),
            'alamat_tujuan' => $this->inputTrim('alamat_tujuan'),
            'jumlah_paket' => $this->inputInt('jumlah_paket', 0),
            'berat_total' => $this->inputFloatOrNull('berat_total'),
            'total_jarak' => $this->inputFloatOrNull('total_jarak'),
            'total_ongkir' => $this->inputFloatOrNull('total_ongkir'),
            'status_pembayaran' => $_POST['status_pembayaran'] ?? 'Belum Bayar',
        ];
        $statusBaru = trim($_POST['status_pengiriman'] ?? '');
        if (empty($data['status_pembayaran'])) {
            $data['status_pembayaran'] = 'Belum Bayar';
        }

        if (empty($data['nomor_resi'])) {
            $generated = $this->generateResiCode($data['origin_city_code'], $data['destination_city_code']);
            if (!empty($generated)) {
                $data['nomor_resi'] = $generated;
            }
        }

        // Validasi data wajib termasuk kode kota
        $required = ['nomor_resi', 'nama_pengirim', 'telpon_pengirim', 'kota_pengirim', 'origin_city_code', 'alamat_asal', 'nama_penerima', 'telpon_penerima', 'kota_penerima', 'destination_city_code', 'alamat_tujuan'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $_SESSION['flash_error'] = 'Lengkapi semua data wajib.';
                header('Location: ' . BASE_URL . 'pengiriman/edit?id=' . $id);
                exit;
            }
        }

        if ($data['jumlah_paket'] < 1) {
            $_SESSION['flash_error'] = 'Jumlah paket minimal 1.';
            header('Location: ' . BASE_URL . 'pengiriman/edit?id=' . $id);
            exit;
        }

        // Hitung ulang jarak/ongkir saat edit agar data konsisten
        $beratKg = 0.0;
        if ($data['berat_total'] !== null) {
            $beratKg = (float) $data['berat_total'];
        }
        $tarifPerKm = 200.0;
        $tarifPerKg = 5000.0;
        $hasilHitung = $this->hitungJarakOngkir($data['kota_pengirim'], $data['kota_penerima'], ORS_API_KEY, $tarifPerKm, $tarifPerKg, $beratKg);
        if ($hasilHitung === null) {
            $_SESSION['flash_error'] = 'Gagal menghitung jarak/ongkir saat update. Cek nama kota atau API key.';
            header('Location: ' . BASE_URL . 'pengiriman/edit?id=' . $id);
            exit;
        }
        $data['total_jarak'] = $hasilHitung['jarak_km'];
        $data['total_ongkir'] = $hasilHitung['ongkir'];

        $updated = $this->resiModel->updateById($id, $_SESSION['user_id'], $data);
        if (!$updated) {
            $_SESSION['flash_error'] = 'Gagal memperbarui resi.';
            header('Location: ' . BASE_URL . 'pengiriman');
            return;
        }

        // Tambah status baru jika diisi dan berbeda dari yang terbaru
        if ($statusBaru !== '') {
            $latest = $this->resiModel->getLatestStatus($id);
            if ($latest === null || $latest !== $statusBaru) {
                $this->resiModel->insertStatusPengiriman($id, (int) $_SESSION['user_id'], $statusBaru);
            }
        }

        $_SESSION['flash_success'] = 'Resi berhasil diperbarui.';
        header('Location: ' . BASE_URL . 'pengiriman');
    }




    private function inputTrim(string $key, string $default = ''): string
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        return trim($_POST[$key]);
    }

    private function inputInt(string $key, int $default = 0): int
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        return (int) $_POST[$key];
    }

    private function inputFloatOrNull(string $key): ?float
    {
        if (!isset($_POST[$key]) || $_POST[$key] === '') {
            return null;
        }
        return (float) $_POST[$key];
    }


    // ====== Helper ======

    // Step 0: Fungsi tunggal untuk hitung jarak + ongkir via OpenRouteService (tarif per km + tarif per kg)
    private function hitungJarakOngkir(string $asal, string $tujuan, string $apiKey, float $tarifPerKm = 200.0, float $tarifPerKg = 1000.0, float $beratKg = 0.0): ?array
    {
        $asal = trim($asal);
        $tujuan = trim($tujuan);
        if ($asal === '' || $tujuan === '' || $apiKey === '') {
            return null;
        }

        // Lakukan Geocode kota asal dan kode kota
        $geocode = function (string $city) use ($apiKey) {
            $url = 'https://api.openrouteservice.org/geocode/search?api_key=' . urlencode($apiKey) .
                '&text=' . urlencode($city);
            $ctx = stream_context_create(['http' => ['timeout' => 6]]);
            $json = @file_get_contents($url, false, $ctx);
            if ($json === false) {
                return null;
            }
            $data = json_decode($json, true);
            $coords = $data['features'][0]['geometry']['coordinates'] ?? null;
            if (!$coords || count($coords) < 2) {
                return null;
            }
            return ['lon' => (float) $coords[0], 'lat' => (float) $coords[1]];
        };

        $koorAsal = $geocode($asal);
        $koorTujuan = $geocode($tujuan);
        if (!$koorAsal || !$koorTujuan) {
            return null;
        }

        // Step 2: Hitung rute driving-car untuk dapatkan jarak meter

        //Kirim data ke API berbentuk array
        $payload = [
            'coordinates' => [
                [$koorAsal['lon'], $koorAsal['lat']],
                [$koorTujuan['lon'], $koorTujuan['lat']],
            ]
        ];
        $ch = curl_init('https://api.openrouteservice.org/v2/directions/driving-car');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: ' . $apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 12
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        if ($resp === false) {
            return null;
        }

        $data = json_decode($resp, true);
        $distMeters = $data['routes'][0]['summary']['distance'] ?? null;
        if (!is_numeric($distMeters)) {
            return null;
        }

        // Step 3: Konversi ke km dan kalikan tarif km + tarif kg
        // Bulatkan jarak ke bilangan bulat (tanpa desimal)
        $jarakM = ((float) $distMeters) / 1000;
        $jarakKm = round($jarakM, 0); 
        $biayaJarak = $jarakKm * $tarifPerKm;
        $biayaBerat = max(0, $beratKg) * $tarifPerKg;
        $ongkir = round($biayaJarak + $biayaBerat); 

        return [
            'jarak_km' => $jarakKm,
            'ongkir' => $ongkir
        ];
    }

    private function generateResiCode($originCode, $destCode)
    {
        $origin = strtoupper(trim($originCode ?? ''));
        $dest = strtoupper(trim($destCode ?? ''));
        if (empty($origin) || empty($dest)) {
            return '';
        }
        $letters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
        return $origin . $letters . $dest;
    }
}
