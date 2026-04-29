<?php

// Ambil semua data dari URL agar bisa dikirim lagi ke halaman sukses
$alat   = $_GET['alat'] ?? 'Alat';
$durasi = $_GET['durasi'] ?? 0;
$total  = $_GET['total'] ?? 0;
$metode = $_GET['metode'] ?? 'BCA';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Instruksi BCA - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; padding-top: 50px; }
        .instruction-card { max-width: 500px; border-radius: 15px; border: none; }
        .copy-btn { font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="card mx-auto shadow-sm p-4 instruction-card">
        <h4 class="fw-bold text-center mb-4">Instruksi Transfer</h4>
        
        <p class="text-center text-muted mb-1">Silakan transfer sesuai nominal berikut:</p>
        <h2 class="text-center text-success fw-bold mb-4">Rp <?php echo number_format($total, 0, ',', '.'); ?></h2>
        
        <div class="bg-light p-3 rounded mb-4 border">
            <small class="text-muted d-block">Nomor Rekening BCA:</small>
            <div class="d-flex justify-content-between align-items-center">
                <strong class="fs-4 text-primary">8830 1234 567</strong>
                <button class="btn btn-sm btn-outline-primary copy-btn" onclick="alert('Nomor rekening disalin!')">Salin</button>
            </div>
            <small class="mt-2 d-block text-secondary">Atas Nama: <strong>TERRALEASE OFFICIAL</strong></small>
        </div>

        <div class="alert alert-warning small py-2">
            <strong>Penting!</strong> Pastikan nominal sesuai agar sistem dapat memverifikasi pembayaran Anda.
        </div>

        <div class="d-grid gap-2">
            <a href="sukses.php?alat=<?= urlencode($alat) ?>&durasi=<?= $durasi ?>&total=<?= $total ?>&metode=<?= $metode ?>" 
               class="btn btn-success py-3 fw-bold" style="border-radius: 12px;">
                Saya Sudah Bayar
            </a>
            <a href="/api/daftar_alat.php" class="btn btn-link text-secondary text-decoration-none">Batal</a>
        </div>
    </div>
</div>

</body>
</html>