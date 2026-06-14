<?php
if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$apiKey = "bc3409d9b7b6b4d78e0dddcb7c006423";
$url    = "https://webapi.bps.go.id/v1/api/data/domain/0000/var/2501/key/" . $apiKey . "/";

$data_tabel = [];
$api_error  = false;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 10,
]);
$response = curl_exec($ch);
$curl_err  = curl_error($ch);
curl_close($ch);

if (!$response || $curl_err) {
    $api_error = true;
} else {
    $json = json_decode($response, true);

    // Struktur API BPS: $json['datacontent'] berisi array nilai
    // $json['vervar'] berisi kategori, $json['turth'] berisi periode waktu
    if (isset($json['datacontent']) && isset($json['vervar'])) {
        $datacontent = $json['datacontent'];
        $categories  = $json['vervar'];

        // Ambil ID periode (bulan): biasanya ada di turth
        $periods = $json['turth'] ?? [];
        // Ambil 3 periode terakhir (Januari, Februari, Maret 2026)
        $period_ids = array_slice(array_keys($periods), -3, 3);

        foreach ($categories as $cat_id => $cat_label) {
            $row_data = ['label' => $cat_label, 'nilai' => []];
            foreach ($period_ids as $pid) {
                // Key format API BPS: var_id . domain . cat_id . period_id
                $key = '2501' . '0000' . $cat_id . $pid;
                $row_data['nilai'][] = isset($datacontent[$key])
                    ? number_format((float)$datacontent[$key], 2, ',', '.')
                    : '-';
            }
            $data_tabel[] = $row_data;
        }
    } else {
        $api_error = true;
    }
}

// Fallback data manual jika API gagal
if ($api_error || empty($data_tabel)) {
    $data_tabel = [
        ['label' => 'Produk Dari Besi Atau Baja',        'nilai' => ['100,00', '100,43', '100,99']],
        ['label' => 'Logam Mulia Dasar',                  'nilai' => ['176,40', '199,05', '198,01']],
        ['label' => 'Mesin & Perlengkapan Pertanian',     'nilai' => ['102,15', '103,20', '104,10']],
        ['label' => 'Peralatan & Perkakas Tangan',        'nilai' => ['101,00', '101,55', '102,30']],
        ['label' => 'Peralatan Pengangkutan & Kendaraan', 'nilai' => ['105,00', '106,80', '107,40']],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data IHPB BPS - TERRALEASE</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h2 { color: #1b5e20; margin-top: 0; }
        p.sub { color: #666; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background-color: #212529; color: white; padding: 12px 15px; text-align: left; }
        tbody td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) { background-color: #fcfcfc; }
        .nilai { font-weight: bold; color: #2e7d32; text-align: right; }
        .alert-api { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 10px 15px; margin-bottom: 15px; font-size: 0.9rem; }
        .btn-back { background-color: #495057; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; margin-top: 25px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Indeks Harga Perdagangan Besar (IHPB) 2026</h2>
    <p class="sub">Seksi Produk Logam, Mesin, dan Perlengkapannya (2023=100)</p>

    <?php if ($api_error): ?>
    <div class="alert-api">⚠️ API BPS sedang tidak tersedia. Menampilkan data referensi.</div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kelompok Komoditas</th>
                <th style="text-align:right;">Januari</th>
                <th style="text-align:right;">Februari</th>
                <th style="text-align:right;">Maret</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_tabel as $i => $item): ?>
            <tr>
                <td><?= $i + 1; ?></td>
                <td><strong><?= htmlspecialchars($item['label']); ?></strong></td>
                <td class="nilai"><?= $item['nilai'][0] ?? '-'; ?></td>
                <td class="nilai"><?= $item['nilai'][1] ?? '-'; ?></td>
                <td class="nilai"><?= $item['nilai'][2] ?? '-'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard_user.php" class="btn-back">← Kembali ke Dashboard</a>
</div>
</body>
</html>
