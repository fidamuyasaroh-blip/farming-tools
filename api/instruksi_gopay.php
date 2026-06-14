<?php
// File ini menampilkan QR instruksi pembayaran GoPay/DANA/QRIS
$alat   = $_GET['alat']   ?? 'Alat';
$durasi = $_GET['durasi'] ?? 0;
$total  = $_GET['total']  ?? 0;
$metode = $_GET['metode'] ?? 'GoPay';

// Warna sesuai metode
$bg_color = match(strtoupper($metode)) {
    'GOPAY' => '#00AED6',
    'DANA'  => '#118EEA',
    default => '#00AED6',
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar via <?= htmlspecialchars($metode); ?> - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding-top: 50px; font-family: 'Segoe UI', sans-serif; }
        .card-qr { max-width: 400px; border-radius: 20px; border: none; }
        .qr-wrapper { background: white; border: 2px solid #eee; border-radius: 15px; padding: 20px; }
        .btn-check-status { border-radius: 12px; padding: 12px; transition: 0.3s; }
        .btn-check-status:hover { opacity: 0.9; transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="card card-qr mx-auto shadow-sm p-4">
        <h5 class="fw-bold mb-3">Scan QR <?= htmlspecialchars($metode); ?></h5>

        <div class="qr-wrapper mb-3">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TERRALEASE-<?= urlencode($total); ?>"
                 class="img-fluid" alt="QR Code Pembayaran">
        </div>

        <p class="mb-1 text-muted">Total yang harus dibayar:</p>
        <h4 class="fw-bold mb-4" style="color: <?= $bg_color; ?>;">
            Rp <?= number_format((float)$total, 0, ',', '.'); ?>
        </h4>

        <div class="alert alert-light border-0 small text-muted mb-4" style="background: #f8f9fa;">
            Buka aplikasi <strong><?= htmlspecialchars($metode); ?></strong>, pilih Scan/Bayar, lalu arahkan kamera ke QR di atas.
        </div>

        <a href="sukses.php?alat=<?= urlencode($alat) ?>&durasi=<?= urlencode($durasi) ?>&total=<?= urlencode($total) ?>&metode=<?= urlencode($metode) ?>"
           class="btn w-100 text-white fw-bold btn-check-status"
           style="background-color: <?= $bg_color; ?>;">
            Saya Sudah Bayar
        </a>

        <a href="daftar_alat.php" class="btn btn-link btn-sm mt-3 text-decoration-none text-secondary">Batal Bayar</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
