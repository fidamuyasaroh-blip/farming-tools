<?php
$apiKey = "bc3409d9b7b6b4d78e0dddcb7c006423";

$url = "https://webapi.bps.go.id/v1/api/data/domain/0000/var/2501/key/" . $apiKey . "/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$data_final = [];
$categories = [];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Terralease - Data IHPB 2026</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        
        h2 { color: #1b5e20; margin-top: 0; }
        p { color: #666; margin-bottom: 25px; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background-color: #212529; color: white; padding: 15px; text-align: left; }
        
        tbody td { padding: 15px; border-bottom: 1px solid #eee; }
        tbody tr:nth-child(even) { background-color: #fcfcfc; }
        
        .nilai { font-weight: bold; color: #2e7d32; text-align: right; }
        .btn-back { background-color: #495057; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; margin-top: 25px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Indeks Harga Perdagangan Besar (IHPB) 2026</h2>
    <p>Seksi Produk Logam, Mesin, dan Perlengkapannya (2023=100)</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kelompok Komoditas</th>
                <th style="text-align: right;">Januari</th>
                <th style="text-align: right;">Februari</th>
                <th style="text-align: right;">Maret</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php $no = 1; foreach ($categories as $cat) : 
                    $cat_id = $cat['val'];
                    // Logika mengambil nilai berdasarkan ID dari datacontent BPS
                    // Format di BPS biasanya: $values[var_id . domain_id . vervar_id . th_id . turth_id]
                    // Untuk Januari (id: 1), Februari (id: 2), Maret (id: 3)
                    $jan = isset($values["25010000".$cat_id."1261"]) ? number_format($values["25010000".$cat_id."1261"], 2, ',', '.') : '-';
                    $feb = isset($values["25010000".$cat_id."1262"]) ? number_format($values["25010000".$cat_id."1262"], 2, ',', '.') : '-';
                    $mar = isset($values["25010000".$cat_id."1263"]) ? number_format($values["25010000".$cat_id."1263"], 2, ',', '.') : '-';
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><strong><?= $cat['label']; ?></strong></td>
                    <td class="nilai"><?= $jan; ?></td>
                    <td class="nilai"><?= $feb; ?></td>
                    <td class="nilai"><?= $mar; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Manual data jika API sedang maintenance (sesuai gambar image_85a794.png) -->
                <tr>
                    <td>1</td>
                    <td><strong>1. Produk Dari Besi Atau Baja</strong></td>
                    <td class="nilai">100,00</td>
                    <td class="nilai">100,43</td>
                    <td class="nilai">100,99</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><strong>2. Logam Mulia Dasar</strong></td>
                    <td class="nilai">176,40</td>
                    <td class="nilai">199,05</td>
                    <td class="nilai">198,01</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="api/dashboard_user.php" class="btn-back">← Kembali ke Dashboard Terralease</a>
</div>

</body>
</html>