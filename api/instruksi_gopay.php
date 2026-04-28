<?php
include 'koneksi.php';

// Proteksi Halaman Admin
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

// Logika Tambah Data
if (isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $nama_gambar   = $_FILES['gambar']['name'];
    $tmp_name      = $_FILES['gambar']['tmp_name'];
    $lokasi_simpan = 'img/' . $nama_gambar;

    if (!is_dir('img')) { mkdir('img', 0777, true); }

    if (move_uploaded_file($tmp_name, $lokasi_simpan)) {
        $query = "INSERT INTO alat (nama_alat, harga, stok, deskripsi, gambar) 
                  VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$nama_gambar')";
        mysqli_query($koneksi, $query);
        header("Location: kelola_alat.php");
        exit();
    }
}

// Logika Hapus Data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM alat WHERE id_alat='$id'");
    header("Location: kelola_alat.php");
    exit();
}
?>
<!-- Konten HTML Kelola Alat kamu di sini -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar via <?php echo htmlspecialchars($metode); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding-top: 50px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-qr { max-width: 400px; border-radius: 20px; border: none; }
        .qr-wrapper { background: white; border: 2px solid #eee; border-radius: 15px; padding: 20px; }
        .btn-check-status { border-radius: 12px; padding: 12px; transition: 0.3s; }
        .btn-check-status:hover { opacity: 0.9; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container text-center">
    <div class="card card-qr mx-auto shadow-sm p-4">
        <h5 class="fw-bold mb-3">Scan QR <?php echo htmlspecialchars($metode); ?></h5>
        
        <div class="qr-wrapper mb-3">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=085xxxxxxxxx" 
            class="img-fluid" alt="QR Code Pembayaran">
        </div>

        <p class="mb-1 text-muted">Total yang harus dibayar:</p>
        <h4 class="fw-bold mb-4" style="color: <?php echo $bg_color; ?>;">
            Rp <?php echo number_format($total, 0, ',', '.'); ?>
        </h4>
        
        <div class="alert alert-light border-0 small text-muted mb-4" style="background: #f8f9fa;">
            Buka aplikasi <strong><?php echo htmlspecialchars($metode); ?></strong>, pilih Scan/Bayar, lalu arahkan kamera ke QR di atas.
        </div>

        <button onclick="window.location.href='sukses.php?alat=<?= urlencode($alat) ?>&durasi=<?= urlencode($durasi) ?>&total=<?= urlencode($total) ?>&metode=<?= urlencode($metode) ?>'" 
                class="btn w-100 text-white fw-bold btn-check-status" 
                style="background-color: <?php echo $bg_color; ?>;">
            Cek Status Bayar
        </button>
        
        <a href="api/daftar_alat.php" class="btn btn-link btn-sm mt-3 text-decoration-none text-secondary">Batal Bayar</a>
    </div>
</div>

</body>
</html>