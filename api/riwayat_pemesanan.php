<?php
include 'koneksi.php';

// Proteksi Halaman Admin
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

$query_riwayat = mysqli_query($koneksi, "SELECT * FROM peminjaman ORDER BY tanggal DESC");
?>
<!-- Konten HTML Riwayat Pemesanan kamu di sini -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pemesanan - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #212529; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark shadow-sm px-4 py-3">
        <span class="navbar-brand fw-bold text-success">TERRALEASE <span class="text-white">ADMIN</span></span>
        <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">← Kembali ke Dashboard</a>
    </nav>

    <div class="container mt-5">
        <h2 class="fw-bold mb-1">Riwayat Pemesanan</h2>
        <p class="text-muted mb-4">Semua transaksi peminjaman alat pertanian</p>

        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
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
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query_riwayat)): 
                        ?>
                        <tr>
                            <td class="ps-4"><?= $no++; ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($row['nama_alat']); ?></td>
                            <td><?= $row['durasi']; ?> hari</td>
                            <td class="text-success fw-bold">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-primary"><?= $row['metode_bayar']; ?></span></td>
                            <td class="small text-muted"><?= date('d M Y, H:i', strtotime($row['tanggal'])); ?></td>
                            <td class="text-center">
                                <?php if($row['status'] == 'lunas' || $row['status'] == 'Sukses'): ?>
                                    <span class="badge bg-success px-3 py-2">✓ Sukses</span>
                                <?php else: ?>
                                    <a href="Proses/konfirmasi_bayar.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm" style="border-radius: 8px;">
                                        ✓ Konfirmasi
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>