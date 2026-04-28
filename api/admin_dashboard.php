<?php
// Cek login menggunakan Cookie (lebih stabil untuk Vercel/Serverless)
if (!isset($_COOKIE['role']) || $_COOKIE['role'] != "admin") {
    // Jika bukan admin, arahkan ke login.php di folder yang sama
    header("Location: login.php"); 
    exit();
}

$username = $_COOKIE['username'] ?? 'Admin';
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .navbar { background-color: #1a1d20; }
        .card-fitur { border: none; border-radius: 15px; transition: 0.3s; }
        .card-fitur:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .btn-custom { width: fit-content !important; padding: 8px 25px !important; border-radius: 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="../index.html">TERRALEASE <span class="text-white">ADMIN</span></a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="nav-link text-light me-3 small">Login sebagai: <strong><?= htmlspecialchars($username); ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-info me-3" href="daftar_alat.php">
                            <i class="bi bi-eye"></i> Lihat Sisi User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="Proses/logout.php" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 20px;">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="mb-5">
            <h2 class="fw-bold">Panel Kendali Utama</h2>
            <p class="text-muted">Selamat datang kembali, silakan kelola data sistem TERRALEASE.</p>
            <hr style="width: 50px; border: 2px solid #198754;">
        </div>

        <div class="row g-4">
            <!-- Kelola User -->
            <div class="col-md-4">
                <div class="card card-fitur bg-white p-4 shadow-sm h-100 border-start border-success border-4">
                    <div class="mb-3 text-success" style="font-size: 2.5rem;"><i class="bi bi-people-fill"></i></div>
                    <h4 class="fw-bold text-dark">Kelola User</h4>
                    <p class="text-muted small">Lihat, edit, dan atur data pelanggan yang terdaftar di database.</p>
                    <div class="mt-auto">
                        <a href="kelola_user.php" class="btn btn-success fw-bold btn-custom">
                            Buka Fitur →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kelola Alat -->
            <div class="col-md-4">
                <div class="card card-fitur bg-white p-4 shadow-sm h-100 border-start border-primary border-4">
                    <div class="mb-3 text-primary" style="font-size: 2.5rem;"><i class="bi bi-gear-wide-connected"></i></div>
                    <h4 class="fw-bold text-dark">Kelola Alat</h4>
                    <p class="text-muted small">Update stok, ubah harga, atau tambah koleksi alat pertanian baru.</p>
                    <div class="mt-auto">
                        <a href="kelola_alat.php" class="btn btn-primary fw-bold btn-custom">
                            Buka Fitur →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pemesanan -->
            <div class="col-md-4">
                <div class="card card-fitur bg-white p-4 shadow-sm h-100 border-start border-warning border-4">
                    <div class="mb-3 text-warning" style="font-size: 2.5rem;"><i class="bi bi-cart-check-fill"></i></div>
                    <h4 class="fw-bold text-dark">Riwayat Pesanan</h4>
                    <p class="text-muted small">Pantau semua transaksi peminjaman alat yang dilakukan pengguna.</p>
                    <div class="mt-auto">
                        <a href="riwayat_pemesanan.php" class="btn btn-warning fw-bold text-dark btn-custom">
                            Buka Fitur →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>