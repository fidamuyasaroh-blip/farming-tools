<?php
// Menggunakan Cookie agar lebih stabil membaca status login
include 'koneksi.php';

$username = $_COOKIE['username'] ?? null;

// Jika tidak ada cookie login, minta login dulu
if (!$username) {
    echo "<script>
        alert('Silakan login terlebih dahulu!');
        window.location.href = 'login.php';
    </script>";
    exit();
}

$id_alat = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil data alat untuk ditampilkan namanya
$query = mysqli_query($koneksi, "SELECT * FROM alat WHERE id = '$id_alat'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: daftar_alat.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Sewa - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .confirm-card { width: 100%; max-width: 500px; background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="confirm-card text-center">
    <h2 class="fw-bold mb-1">Konfirmasi Sewa</h2>
    <p class="text-muted mb-4">Anda akan menyewa: <span class="badge bg-success fs-6"><?= htmlspecialchars($data['nama_alat']); ?></span></p>

    <form action="pembayaran.php" method="POST">
        <input type="hidden" name="id" value="<?= $id_alat; ?>">
        
        <div class="mb-4 text-start">
            <label class="form-label fw-semibold">Lama Sewa (Hari)</label>
            <input type="number" name="hari" class="form-num form-control form-control-lg text-center" value="1" min="1" required>
            <small class="text-muted">Minimal penyewaan adalah 1 hari.</small>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success btn-lg fw-bold py-3">Lanjut ke Pembayaran</button>
            <a href="daftar_alat.php" class="btn btn-warning btn-lg fw-bold py-3 text-white">Batal</a>
        </div>
    </form>
</div>

</body>
</html>