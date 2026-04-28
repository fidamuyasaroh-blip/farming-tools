<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    $_SESSION['redirect_after_login'] = 'daftar_alat.php';
    echo "<script>
        alert('Maaf, Anda harus login terlebih dahulu untuk mengakses katalog!');
        window.location.href = 'login.php';
    </script>";
    exit();
}

$query = "SELECT * FROM alat";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 100px; }
        .card { transition: transform 0.2s; border: none; border-radius: 15px; overflow: hidden; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .card-img-top { height: 200px; object-fit: cover; }
        .text-description {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 3rem; 
        }
        .navbar-brand { letter-spacing: 1px; }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top" style="background-color: #2e7d32;">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="index.html">TERRALEASE</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="index.html">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-white fw-semibold px-3 active" href="daftar_alat.php">Katalog</a></li>
                    <li class="nav-item">
                        <span class="nav-link text-light me-2">Halo, <strong><?= $_SESSION['username']; ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-warning btn-sm fw-bold px-3" href="Proses/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- TOMBOL KEMBALI -->
    <div class="container mt-3">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="api/admin_dashboard.php" class="btn btn-outline-success btn-sm px-3">
                ← Kembali ke Dashboard Admin
            </a>
        <?php else: ?>
            <a href="api/dashboard_user.php" class="btn btn-outline-success btn-sm px-3">
                ← Kembali ke Dashboard
            </a>
        <?php endif; ?>
    </div>

    <!-- KONTEN KATALOG -->
    <div class="container mb-5">
        <div class="text-center mb-5 mt-4">
            <h2 class="fw-bold">Katalog Alat Pertanian</h2>
            <p class="text-muted">Pilih alat modern untuk hasil tani yang maksimal</p>
            <hr style="width: 100px; height: 4px; background: #2e7d32; margin: auto; opacity: 1;">
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0) : ?>
                <?php while($row = mysqli_fetch_assoc($result)) : ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm">
                        <img src="img/<?= htmlspecialchars($row['gambar']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama_alat']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-1 text-dark"><?= htmlspecialchars($row['nama_alat']); ?></h5>
                            <p class="card-text text-muted small text-description mb-3">
                                <?= htmlspecialchars($row['deskripsi']); ?>
                            </p>
                            <div class="mt-auto">
                                <p class="card-text text-success fw-bold fs-5 mb-1">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?> <span class="text-muted small fw-normal">/ hari</span>
                                </p>
                                <p class="card-text text-secondary mb-3" style="font-size: 0.85rem;">
                                    Stok: <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['stok']); ?> unit</span>
                                </p>
                                <a href="api/detail.php?id=<?= $row['id']; ?>" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5">
                    <div class="mb-3" style="font-size: 4rem;">📦</div>
                    <h4 class="text-muted">Belum ada alat di katalog.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>