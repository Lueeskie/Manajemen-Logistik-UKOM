<?php

// ========== CONFIG ========== 
$ORS_API_KEY = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImE0OGI0OGEyMWUwMzRmNTQ5MmYzOWVmOGYzZDVkMjQ3IiwiaCI6Im11cm11cjY0In0=";


// ========== FUNGSI GEOCODING (cari koordinat kota) ==========
function getCoordinates($city, $apiKey)
{
    $url = "https://api.openrouteservice.org/geocode/search?api_key=$apiKey&text=" . urlencode($city);

    $json = file_get_contents($url);
    $data = json_decode($json, true);

    if (!isset($data["features"][0]["geometry"]["coordinates"])) {
        return null;
    }

    $lon = $data["features"][0]["geometry"]["coordinates"][0];
    $lat = $data["features"][0]["geometry"]["coordinates"][1];

    return ["lat" => $lat, "lon" => $lon];
}


// ========== FUNGSI HITUNG JARAK MENGGUNAKAN ORS ROUTING ==========
function getDistanceORS($startLat, $startLon, $endLat, $endLon, $apiKey)
{
    $url = "https://api.openrouteservice.org/v2/directions/driving-car";

    $postData = [
        "coordinates" => [
            [(float)$startLon, (float)$startLat],
            [(float)$endLon,   (float)$endLat]
        ]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: $apiKey"
        ],
        CURLOPT_POSTFIELDS => json_encode($postData)
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    if (!isset($data["routes"][0]["summary"]["distance"])) {
        return null;
    }

    // hasil dalam meter → ubah ke km
    return $data["routes"][0]["summary"]["distance"] / 1000;
}


// ========== LOGIKA KETIKA SUBMIT ==========
$hasil = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $asal = $_POST["asal"];
    $tujuan = $_POST["tujuan"];

    // Step 1: Dapatkan koordinat
    $koorAsal   = getCoordinates($asal, $ORS_API_KEY);
    $koorTujuan = getCoordinates($tujuan, $ORS_API_KEY);

    if (!$koorAsal || !$koorTujuan) {
        $hasil = "Gagal mendapatkan koordinat kota.";
    } else {
        // Step 2: Hitung jarak
        $jarak = getDistanceORS(
            $koorAsal["lat"],   $koorAsal["lon"],
            $koorTujuan["lat"], $koorTujuan["lon"],
            $ORS_API_KEY
        );

        if (!$jarak) {
            $hasil = "Gagal menghitung jarak.";
        } else {
            // Step 3: Hitung ongkir (contoh)
            $tarif_per_km = 3500;
            $ongkir = $jarak * $tarif_per_km;

            $hasil = [
                "jarak" => $jarak,
                "ongkir" => $ongkir,
                "asal" => $asal,
                "tujuan" => $tujuan
            ];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Test Hitung Jarak ORS</title>
</head>
<body>

<h1>Test Hitung Jarak (OpenRouteService)</h1>

<form method="POST">
    <label>Kota Asal:</label><br>
    <input type="text" name="asal" placeholder="" required><br><br>

    <label>Kota Tujuan:</label><br>
    <input type="text" name="tujuan" placeholder="" required><br><br>

    <button type="submit">Hitung Jarak</button>
</form>


<hr>

<?php if ($hasil): ?>
    <?php if (is_string($hasil)): ?>
        <p style="color:red;"><?php echo $hasil; ?></p>
    <?php else: ?>
        <h2>Hasil:</h2>
        <p><b>Asal:</b> <?= $hasil["asal"] ?></p>
        <p><b>Tujuan:</b> <?= $hasil["tujuan"] ?></p>
        <p><b>Jarak:</b> <?= round($hasil["jarak"], 2) ?> km</p>
        <p><b>Ongkir (3500/km):</b> Rp <?= number_format($hasil["ongkir"], 0, ',', '.') ?></p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
