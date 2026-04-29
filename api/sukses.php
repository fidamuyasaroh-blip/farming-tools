<?php

$alat   = $_GET['alat'] ?? 'Alat';
$durasi = $_GET['durasi'] ?? 0;
$total  = $_GET['total'] ?? 0;
$metode = $_GET['metode'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menunggu Konfirmasi - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding-top: 80px; }
        .card { max-width: 500px; margin: auto; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .pending-icon { width: 80px; height: 80px; background: #f39c12; color: white; font-size: 40px; display: flex; align-items: center; justify-content: center; border-radius: 15px; margin: 0 auto 20px; }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="card p-5">
        <div class="pending-icon">⏳</div>
        <h2 class="fw-bold mb-2">Menunggu Konfirmasi!</h2>
        <p class="text-secondary mb-4">Pembayaran kamu sedang diverifikasi oleh admin.</p>

        <div class="bg-light p-4 rounded-3 text-start mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Alat:</span>
                <strong><?php echo htmlspecialchars(ucfirst($alat)); ?></strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Durasi:</span>
                <strong><?php echo $durasi; ?> Hari</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Metode:</span>
                <strong><?php echo htmlspecialchars($metode); ?></strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold text-success fs-5">
                <span>Total Bayar:</span>
                <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="alert alert-warning text-start small">
            <strong>⚠️ Penting!</strong> Jika pembayaran tidak dikonfirmasi dalam 1x24 jam, pesanan akan dibatalkan otomatis.
        </div>

        <div class="d-grid gap-2">
            <a href="dashboard_user.php" class="btn btn-success py-2 fw-bold" style="border-radius: 12px;">
                Lihat Status Peminjaman
            </a>
            <a href="/api/daftar_alat.php" class="btn btn-outline-success py-2 fw-bold" style="border-radius: 12px;">
                Kembali ke Katalog
            </a>
        </div>
    </div>
</div>
</body>
</html>