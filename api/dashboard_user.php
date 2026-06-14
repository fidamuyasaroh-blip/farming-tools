<?php
include 'koneksi.php';

if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_COOKIE['username'];
$username_esc = mysqli_real_escape_string($koneksi, $username);

// Total peminjaman
$q_total_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman WHERE username='$username_esc'");
$total_pinjam   = mysqli_fetch_assoc($q_total_pinjam)['total'] ?? 0;

// Total pengeluaran — coba kolom total_bayar, fallback ke total
$total_pengeluaran = 0;
$q_total = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM peminjaman WHERE username='$username_esc'");
if ($q_total) {
    $row_total = mysqli_fetch_assoc($q_total);
    $total_pengeluaran = $row_total['total'] ?? 0;
}

// Riwayat peminjaman
$query = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE username='$username_esc' ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f0f4f8; padding-top: 80px; }
        .navbar { background-color: #2e7d32; }
        .stat-card { border: none; border-radius: 16px; padding: 24px; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .card-shortcut { border: none; border-radius: 16px; transition: 0.3s; }
        .card-shortcut:hover { transform: translateY(-5px); box-shadow: 0 10px 24px rgba(0,0,0,0.15); }
        .table-card { border: none; border-radius: 16px; overflow: hidden; }
        .badge-metode { font-size: 0.75rem; padding: 5px 12px; border-radius: 8px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/index.html">🌿 TERRALEASE</a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link text-white fw-semibold" href="daftar_alat.php">Katalog</a></li>
                    <li class="nav-item"><span class="nav-link text-light">Hai, <strong><?= htmlspecialchars($username); ?></strong></span></li>
                    <li class="nav-item"><a href="Proses/logout.php" class="btn btn-outline-warning btn-sm fw-bold px-3">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="mb-4">
            <div style="font-size: 1.5rem; font-weight: 800;">Selamat Datang, <?= htmlspecialchars($username) ?>! 👋</div>
            <div style="color: #6b7280; font-size: 0.9rem;">Kelola penyewaan alat pertanianmu dari sini.</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #2e7d32, #66bb6a);">
                    <div style="font-size: 2rem;">📦</div>
                    <div class="text-white mt-2">
                        <div style="font-size: 2rem; font-weight: 800;"><?= $total_pinjam ?></div>
                        <div style="font-size: 0.85rem; opacity: 0.85;">Total Peminjaman</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card shadow-sm" style="background: linear-gradient(135deg, #1565c0, #64b5f6);">
                    <div style="font-size: 2rem;">💰</div>
                    <div class="text-white mt-2">
                        <div style="font-size: 1.5rem; font-weight: 800;">Rp <?= number_format($total_pengeluaran, 0, ',', '.'); ?></div>
                        <div style="font-size: 0.85rem; opacity: 0.85;">Total Pengeluaran</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <a href="daftar_alat.php" class="text-decoration-none">
                    <div class="card card-shortcut bg-success text-white p-4 shadow-sm text-center">
                        <div style="font-size: 2.5rem;">🚜</div>
                        <h5 class="fw-bold mt-2 mb-0">Lihat Katalog Alat</h5>
                        <small class="opacity-75">Sewa alat pertanian sekarang</small>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="data_ihpb_bps.php" class="text-decoration-none">
                    <div class="card card-shortcut text-white p-4 shadow-sm text-center" style="background: #1565c0; border-radius: 16px;">
                        <div style="font-size: 2.5rem;">📊</div>
                        <h5 class="fw-bold mt-2 mb-0">Statistik Pertanian</h5>
                        <small style="color: rgba(255,255,255,0.75);">Lihat data IHPB terkini</small>
                    </div>
                </a>
            </div>
        </div>

        <h5 class="fw-bold mb-3">📋 Riwayat Peminjaman Saya</h5>
        <div class="card table-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Alat</th>
                                <th>Durasi</th>
                                <th>Total Bayar</th>
                                <th>Metode</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td class="ps-4"><?= $no++; ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_alat'] ?? $row['alat'] ?? '-'); ?></td>
                                    <td><?= (int)($row['durasi'] ?? 0); ?> hari</td>
                                    <td class="text-success fw-bold">Rp <?= number_format($row['total_bayar'] ?? $row['total'] ?? 0, 0, ',', '.'); ?></td>
                                    <td>
                                        <?php
                                        $metode = $row['metode_bayar'] ?? $row['metode'] ?? 'BCA';
                                        $warna  = match(strtoupper($metode)) {
                                            'BCA'            => 'bg-primary',
                                            'DANA'           => 'bg-info',
                                            'GOPAY'          => 'bg-success',
                                            'TRANSFER MANDIRI' => 'bg-warning text-dark',
                                            default          => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $warna; ?> badge-metode"><?= htmlspecialchars($metode); ?></span>
                                    </td>
                                    <td class="text-muted small"><?= date('d M Y', strtotime($row['tanggal'] ?? 'now')); ?></td>
                                    <td>
                                        <?php if (strtolower($row['status'] ?? '') === 'lunas'): ?>
                                            <span class="badge bg-success">Sukses ✓</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div style="font-size: 3rem;">📭</div>
                                        <p class="mt-2">Kamu belum pernah meminjam alat.</p>
                                        <a href="daftar_alat.php" class="btn btn-success btn-sm px-4">Mulai Sewa Sekarang</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
