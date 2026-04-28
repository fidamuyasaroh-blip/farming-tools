<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/api/koneksi.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$query = mysqli_query($koneksi, "SELECT * FROM alat WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Alat tidak ditemukan!'); window.location.href='/api/daftar_alat.php';</script>";
    exit();
}

$nama     = $data['nama_alat'];
$harga    = number_format($data['harga'], 0, ',', '.');
$gambar   = $data['gambar'];
$stok     = $data['stok'];
$deskripsi = $data['deskripsi'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail - <?= $nama ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .detail-card { border-radius: 20px; overflow: hidden; border: none; }
        .img-container { background-color: white; min-height: 400px; display: flex; align-items: center; justify-content: center; }
        .img-container img { max-height: 350px; object-fit: contain; }
    </style>
</head>
<body class="p-4 p-md-5">

    <div class="container">
        <a href="/api/daftar_alat.php" class="btn btn-success mb-4 shadow-sm" style="border-radius: 50px; padding: 10px 25px;">
            ← Kembali ke Katalog
        </a>

        <div class="card detail-card shadow-lg">
            <div class="row g-0">
                <div class="col-md-6 img-container p-4">
                    <?php if($gambar && file_exists('img/'.$gambar)): ?>
                        <img src="img/<?= $gambar ?>" class="img-fluid" alt="<?= $nama ?>">
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <img src="https://via.placeholder.com/300x300?text=Tanpa+Gambar" class="img-fluid mb-2">
                            <p>Gambar tidak tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 p-4 p-lg-5 bg-white">
                    <h1 class="fw-bold mb-3"><?= $nama ?></h1>
                    
                    <div class="mb-4">
                        <h4 class="text-secondary fw-semibold">Deskripsi Produk</h4>
                        <p class="text-muted" style="line-height: 1.8;">
                            <?= nl2br($deskripsi); ?>
                        </p>
                    </div>

                    <div class="mb-4">
                        <p class="mb-1 text-secondary">Ketersediaan Unit:</p>
                        <span class="badge <?= $stok > 0 ? 'bg-success' : 'bg-danger'; ?> fs-6 px-3">
                            <?= $stok ?> Unit Tersedia
                        </span>
                    </div>

                    <div class="bg-light p-4 rounded-4 mb-4" style="border: 1px solid #eee;">
                        <small class="text-uppercase fw-bold text-secondary">Biaya Sewa</small>
                        <h2 class="text-success fw-bold mb-0">
                            Rp <?= $harga ?> <span class="fs-5 text-secondary fw-normal">/ hari</span>
                        </h2>
                    </div>

                    <?php if ($stok > 0): ?>
                        <a href="/api/pinjam.php?id=<?= $id ?>" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-3" style="border-radius: 12px; background-color: #2e7d32;">
                            Pinjam Sekarang
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg w-100 fw-bold py-3" style="border-radius: 12px;" disabled>
                            Stok Habis
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>