<?php
include 'koneksi.php';

// PERBAIKAN UTAMA: Membaca login menggunakan Cookie agar tidak mental ke login saat di Vercel
$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>
        alert('Sesi bermain Anda habis, silakan login kembali.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

// Menangkap data kiriman dari pinjam.php
$id_alat = isset($_GET['id']) ? $_GET['id'] : 0;
$durasi  = isset($_GET['hari']) ? $_GET['hari'] : 1;

// Ambil data dari database
$query = mysqli_query($koneksi, "SELECT * FROM alat WHERE id = '$id_alat'");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
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
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; padding-top: 50px; }
        .payment-card { max-width: 600px; margin: auto; border-radius: 20px; border: none; }
        .method-box { border: 2px solid #eee; border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; }
        input[type="radio"]:checked + .method-box { border-color: #2e7d32; background: #f1f8e9; border-width: 2px; }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="card payment-card shadow-sm p-4">
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
            <input type="hidden" name="durasi" value="<?= $durasi; ?>">
            <input type="hidden" name="total" value="<?= $total_bayar; ?>">

            <h5 class="mb-3 fw-semibold">Pilih Metode Pembayaran</h5>

            <label class="w-100 mb-2" style="cursor: pointer;">
                <input type="radio" name="metode" value="BCA" class="d-none" required checked>
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" width="60" class="me-3">
                    <div>
                        <strong>Transfer BCA</strong><br>
                        <small class="text-muted">8830-1234-567 a/n TERRALEASE</small>
                    </div>
                </div>
            </label>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success py-3 fw-bold">Konfirmasi & Bayar Sekarang</button>
                <a href="daftar_alat.php" class="btn btn-secondary py-2 fw-bold text-center text-decoration-none">Batal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>