<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: /api/login.php");
    exit();
}
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #212529; }
        .card-fitur { border: none; border-radius: 15px; transition: 0.3s; }
        .card-fitur:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .btn-custom { width: fit-content !important; padding: 8px 25px !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.html">TERRALEASE ADMIN</a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-info me-3" href="daftar_alat.php">
                            <i class="bi bi-person-bounding-box"></i> Masuk Sisi User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="api/Proses/logout.php" class="btn btn-danger btn-sm px-3" style="border-radius: 20px;">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-3">
        <div class="mb-4">
            <h2 class="fw-bold">Panel Kendali Admin</h2>
            <p class="text-muted">Kelola data sistem TERRALEASE di sini.</p>
        </div>

        <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-fitur bg-success text-white p-4 shadow-sm h-100">
                <div class="mb-3" style="font-size: 2.5rem;">👥</div>
                <h4 class="fw-bold">Kelola User</h4>
                <p class="opacity-75">Lihat dan atur data pelanggan yang terdaftar.</p>
                <div class="mt-auto">
                    <a href="api/kelola_user.php" class="btn btn-light fw-bold text-success btn-custom">
                        Buka Fitur →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-fitur bg-primary text-white p-4 shadow-sm h-100">
                <div class="mb-3" style="font-size: 2.5rem;">🚜</div>
                <h4 class="fw-bold">Kelola Alat</h4>
                <p class="opacity-75">Update stok, harga, atau tambah alat pertanian baru.</p>
                <div class="mt-auto">
                    <a href="api/kelola_alat.php" class="btn btn-light fw-bold text-primary btn-custom">
                        Buka Fitur →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-fitur bg-warning text-dark p-4 shadow-sm h-100">
                <div class="mb-3" style="font-size: 2.5rem;">📋</div>
                <h4 class="fw-bold">Riwayat Pemesanan</h4>
                <p class="opacity-75">Lihat semua transaksi peminjaman alat oleh pengguna.</p>
                <div class="mt-auto">
                    <a href="api/riwayat_pemesanan.php" class="btn btn-dark fw-bold text-white btn-custom">
                        Buka Fitur →
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>