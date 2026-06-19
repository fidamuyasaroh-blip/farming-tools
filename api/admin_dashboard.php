<?php
require_once __DIR__ . '/koneksi.php';

// Proteksi halaman admin
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_COOKIE['username'] ?? 'Admin');

// Aksi: Set Lunas
if (isset($_GET['aksi']) && $_GET['aksi'] === 'set_lunas' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = mysqli_prepare($koneksi, "UPDATE peminjaman SET status = 'lunas' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Status berhasil diperbarui menjadi LUNAS!'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }
}

$result_pesanan = mysqli_query($koneksi, "SELECT * FROM peminjaman ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f3f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-bottom: 60px; }
        .sidebar { height: 100vh; background: #212529; color: white; padding-top: 20px; position: fixed; width: 220px; }
        .main-content { margin-left: 220px; padding: 40px; }
        .card-custom { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h5 class="text-center fw-bold mb-4 text-success">TERRALEASE</h5>
    <hr class="border-secondary">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="admin_dashboard.php" class="nav-link bg-success text-white fw-bold">Dashboard</a>
        </li>
        <li class="nav-item mb-2">
            <a href="kelola_alat.php" class="nav-link text-white">Kelola Alat</a>
        </li>
        <li class="nav-item mb-2">
            <a href="kelola_user.php" class="nav-link text-white">Kelola User</a>
        </li>
        <li class="nav-item mb-2">
            <a href="laporan.php" class="nav-link text-white">📊 Laporan</a>
        </li>
        <li class="nav-item mb-2">
            <a href="daftar_alat.php" class="nav-link text-white">Lihat Katalog</a>
        </li>
        <li class="nav-item mb-2">
            <a href="Proses/logout.php" class="nav-link text-danger">Logout</a>
        </li>
    </ul>
    <hr class="border-secondary">
    <div class="text-center text-muted small">Admin: <strong><?= $username; ?></strong></div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Dashboard Panel Admin</h2>
            <p class="text-muted mb-0">Kelola transaksi penyewaan alat pertanian.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="laporan.php" class="btn btn-outline-dark fw-bold px-4">
                📊 Laporan & Grafik
            </a>
            <a href="kelola_alat.php" class="btn btn-success fw-bold px-4">
                ➕ Tambah / Kelola Alat
            </a>
        </div>
    </div>

    <div class="card card-custom p-4">
        <h5 class="fw-bold mb-3 text-primary">📋 Daftar Transaksi User</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Alat</th>
                        <th>Durasi</th>
                        <th>Total Bayar</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_pesanan && mysqli_num_rows($result_pesanan) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result_pesanan)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('d M Y', strtotime($row['tanggal'] ?? 'now')); ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['username'] ?? '-'); ?></span></td>
                            <td><strong><?= htmlspecialchars($row['nama_alat'] ?? $row['alat'] ?? '-'); ?></strong></td>
                            <td><?= (int)($row['durasi'] ?? 0); ?> Hari</td>
                            <td class="text-success fw-bold">Rp <?= number_format((float)($row['total_bayar'] ?? $row['total'] ?? 0), 0, ',', '.'); ?></td>
                            <td><small><?= htmlspecialchars($row['metode_bayar'] ?? $row['metode'] ?? '-'); ?></small></td>
                            <td>
                                <?php if (strtolower($row['status'] ?? '') === 'lunas'): ?>
                                    <span class="badge bg-success rounded-pill px-3">Lunas</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (strtolower($row['status'] ?? '') !== 'lunas'): ?>
                                    <a href="admin_dashboard.php?aksi=set_lunas&id=<?= (int)$row['id']; ?>"
                                       onclick="return confirm('Set transaksi ini menjadi LUNAS?')"
                                       class="btn btn-sm btn-success fw-bold">✓ Set Lunas</a>
                                <?php else: ?>
                                    <span class="text-muted small">Selesai ✓</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>