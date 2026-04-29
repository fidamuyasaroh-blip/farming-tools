<?php
// Menggunakan koneksi dan mengecek login via Cookie agar stabil di Vercel
include 'koneksi.php';

if (!isset($_COOKIE['username'])) {
    setcookie("redirect_after_login", "daftar_alat.php", time() + 3600, "/");
    echo "<script>
        alert('Maaf, Anda harus login terlebih dahulu untuk mengakses katalog!');
        window.location.href = 'login.php';
    </script>";
    exit();
}

$username = $_COOKIE['username'];
$role = $_COOKIE['role'] ?? 'user';

// Ambil data alat dari database
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
        body { background-color: #f8f9fa; padding-top: 100px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card { transition: transform 0.2s; border: none; border-radius: 15px; overflow: hidden; height: 100%; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .card-img-top { height: 200px; object-fit: cover; background-color: #eee; }
        .text-description {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 3rem; 
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top" style="background-color: #2e7d32;">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="../index.html">TERRALEASE</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link text-white fw-semibold px-3" href="../index.html">Beranda</a></li>
                    <li class="nav-item">
                        <span class="nav-link text-light me-2">Halo, <strong><?= htmlspecialchars($username); ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-warning btn-sm fw-bold px-3" href="Proses/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <?php if ($role == 'admin'): ?>
            <a href="admin_dashboard.php" class="btn btn-outline-success btn-sm px-3">← Dashboard Admin</a>
        <?php else: ?>
            <a href="dashboard_user.php" class="btn btn-outline-success btn-sm px-3">← Dashboard User</a>
        <?php endif; ?>
    </div>

    <div class="container mb-5">
        <div class="text-center mb-5 mt-4">
            <h2 class="fw-bold">Katalog Alat Pertanian</h2>
            <p class="text-muted">Pilih alat modern untuk hasil tani yang maksimal</p>
            <hr style="width: 60px; border: 2px solid #2e7d32; margin: auto;">
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0) : ?>
                <?php while($row = mysqli_fetch_assoc($result)) : 
                    
                    // --- LOGIKA PENGAMAN (FALLBACK) ---
                    // Jika di database nama filenya masih salah, kode ini akan memperbaikinya otomatis saat ditampilkan
                    $nama_file = $row['gambar'];
                    if ($row['nama_alat'] == 'Seeder') $nama_file = 'seeder.jpg';
                    if ($row['nama_alat'] == 'Fertilizer Spreader') $nama_file = 'Fertilizer-Spreader.jpg';
                    if ($row['nama_alat'] == 'Rotavator') $nama_file = 'Rotavator_1_63037605e0.jpg';
                    if ($row['nama_alat'] == 'Cultivator') $nama_file = 'Cultivator.jpg';
                    if ($row['nama_alat'] == 'Mesin Modern') $nama_file = 'Mesin-pertanian-modern.jpg';
                    // ----------------------------------
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm">
                        <!-- PATH GAMBAR: Keluar dari api/ lalu masuk ke img/ -->
                        <img src="/img/<?= htmlspecialchars($nama_file); ?>" 
                            class="card-img-top" 
                            alt="<?= htmlspecialchars($row['nama_alat']); ?>"
                            onerror="this.onerror=null;this.src='https://placehold.co/600x400?text=Gambar+Tidak+Ada';">
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
                                <a href="detail.php?id=<?= $row['id']; ?>" class="btn btn-success w-100 fw-bold py-2">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">Belum ada alat di katalog database.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>