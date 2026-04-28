<?php
session_start();
include 'koneksi.php'; 

$id_alat = isset($_GET['id']) ? $_GET['id'] : 0;

$query = mysqli_query($koneksi, "SELECT * FROM alat WHERE id = '$id_alat'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: /api/daftar_alat.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Sewa - <?= $data['nama_alat'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { border-radius: 20px; border: none; max-width: 500px; width: 100%; }
    </style>
</head>
<body>

<div class="card shadow p-4">
    <h2 class="fw-bold mb-1">Konfirmasi Sewa</h2>
    <p class="text-muted mb-4">Anda akan menyewa: <span class="badge bg-success fs-6"><?= $data['nama_alat'] ?></span></p>

    <form action="pembayaran.php" method="GET">
        <!-- Kirim ID alat ke halaman pembayaran nanti -->
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <div class="mb-3">
            <label class="form-label fw-bold">Lama Sewa (Hari)</label>
            <input type="number" name="hari" class="form-control form-control-lg" placeholder="Masukkan jumlah hari" min="1" required>
            <small class="text-muted">Minimal penyewaan adalah 1 hari.</small>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-success btn-lg fw-bold py-3">Lanjut ke Pembayaran</button>
            <a href="api/detail.php?id=<?= $data['id'] ?>" class="btn btn-warning btn-lg fw-bold text-white py-2">Batal</a>
        </div>
    </form>
</div>

</body>
</html>