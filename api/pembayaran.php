<?php
include 'koneksi.php';

// Proteksi Cookie agar status login tetap stabil di Vercel maupun Localhost
$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>
        alert('Sesi habis, silakan login kembali.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

// Menangkap data kiriman dari pinjam.php menggunakan metode GET
$id_alat = isset($_GET['id']) ? $_GET['id'] : 0;
$durasi  = isset($_GET['hari']) ? $_GET['hari'] : 1;

// Ambil data alat dari database berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM alat WHERE id = '$id_alat'");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    // Jika data alat tidak ditemukan, kembalikan ke katalog
    header("Location: daftar_alat.php");
    exit();
}

$alat = $data['nama_alat'];
$harga_per_hari = $data['harga'];
$total_bayar = $durasi * $harga_per_hari;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding-top: 50px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .payment-card { max-width: 550px; margin: auto; border-radius: 20px; border: none; }
        .method-box { border: 2px solid #eee; border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; }
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .method-box { border-color: #2e7d32; background: #f1f8e9; box-shadow: 0 4px 10px rgba(46, 125, 50, 0.1); }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="card payment-card shadow p-4">
        <h3 class="fw-bold mb-4 text-center">Detail Pembayaran</h3>
        
        <div class="bg-light p-3 rounded-3 mb-4 border">
            <div class="d-flex justify-content-between mb-2">
                <span>Alat: <strong><?= htmlspecialchars($alat); ?></strong></span>
                <span><?= $durasi; ?> Hari</span>
            </div>
            <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                <span>Total Tagihan:</span>
                <span class="text-success">Rp <?= number_format($total_bayar, 0, ',', '.'); ?></span>
            </div>
        </div>

        <form action="Proses/simpan_pinjam.php" method="POST">
            
            <input type="hidden" name="id_alat" value="<?= $id_alat; ?>">
            <input type="hidden" name="nama_alat" value="<?= htmlspecialchars($alat); ?>"> 
            <input type="hidden" name="durasi" value="<?= $durasi; ?>">
            <input type="hidden" name="total" value="<?= $total_bayar; ?>">

            <h5 class="mb-3 fw-semibold">Pilih Metode Pembayaran</h5>

            <label class="w-100 mb-2">
                <input type="radio" name="metode" value="Transfer BCA" required checked>
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" width="60" class="me-3">
                    <div>
                        <strong>Transfer BCA</strong><br>
                        <small class="text-muted">8830-1234-567 a/n TERRALEASE</small>
                    </div>
                </div>
            </label>

            <label class="w-100 mb-2">
                <input type="radio" name="metode" value="Transfer Mandiri">
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" width="60" class="me-3">
                    <div>
                        <strong>Transfer Mandiri</strong><br>
                        <small class="text-muted">131-0012-3456 a/n TERRALEASE</small>
                    </div>
                </div>
            </label>

            <label class="w-100 mb-3">
                <input type="radio" name="metode" value="Gopay">
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" width="60" class="me-3">
                    <div>
                        <strong>GOPAY / QRIS</strong><br>
                        <small class="text-muted">0812-3456-7890 a/n TERRALEASE</small>
                    </div>
                </div>
            </label>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success py-3 fw-bold">Konfirmasi & Bayar Sekarang</button>
                <a href="daftar_alat.php" class="btn btn-light py-2 border">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>