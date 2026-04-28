<?php
// Gunakan pengecekan yang lebih fleksibel untuk Cookie di Vercel
$role = $_COOKIE['role'] ?? null;
$username = $_COOKIE['username'] ?? 'Admin';

// Jika role tidak ada ATAU bukan admin, tendang ke login
if (!$role || $role !== "admin") {
    header("Location: login.php");
    exit();
}

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
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        
        body { background: #f4f6f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .navbar { background-color: #1a1d20; }
        .card-fitur { 
            border: none; 
            border-radius: 20px; 
            transition: all 0.3s ease; 
            background: #ffffff;
        }
        .card-fitur:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; 
        }
        .btn-custom { 
            border-radius: 12px; 
            padding: 10px 20px; 
            font-weight: 600;
            width: 100%;
        }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="../index.html">
                TERRALEASE <span class="text-white">ADMIN</span>
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="nav-link text-light me-3 small">
                            <i class="bi bi-person-circle"></i> <strong><?= htmlspecialchars($username); ?></strong>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-info btn-sm px-3 me-2" href="daftar_alat.php" style="border-radius: 10px;">
                            <i class="bi bi-megaphone"></i> Sisi User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="Proses/logout.php" class="btn btn-danger btn-sm px-3" style="border-radius: 10px;">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-5">
            <div class="col-md-8">
                <h2 class="fw-bold text-dark">Panel Kendali Utama</h2>
                <p class="text-muted">Kelola operasional Terralease Wonogiri secara efisien.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Kelola User -->
            <div class="col-md-4">
                <div class="card card-fitur p-4 shadow-sm h-100">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h4 class="fw-bold">Kelola User</h4>
                    <p class="text-muted small mb-4">Atur data pelanggan dan hak akses akun mereka.</p>
                    <div class="mt-auto">
                        <a href="kelola_user.php" class="btn btn-success btn-custom">Buka Manajemen</a>
                    </div>
                </div>
            </div>

            <!-- Kelola Alat -->
            <div class="col-md-4">
                <div class="card card-fitur p-4 shadow-sm h-100">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-tools"></i>
                    </div>
                    <h4 class="fw-bold">Kelola Alat</h4>
                    <p class="text-muted small mb-4">Tambah, hapus, atau update stok alat pertanian.</p>
                    <div class="mt-auto">
                        <a href="kelola_alat.php" class="btn btn-primary btn-custom">Buka Inventaris</a>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pemesanan -->
            <div class="col-md-4">
                <div class="card card-fitur p-4 shadow-sm h-100">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <h4 class="fw-bold">Transaksi</h4>
                    <p class="text-muted small mb-4">Pantau riwayat sewa dan status pengembalian alat.</p>
                    <div class="mt-auto">
                        <a href="riwayat_pemesanan.php" class="btn btn-warning btn-custom">Lihat Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>