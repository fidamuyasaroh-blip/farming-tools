<?php
// PERBAIKAN UTAMA: Karena file ini ada di folder api/, panggil koneksi.php langsung tanpa '../'
require_once 'koneksi.php';

// Ambil data user dari Cookie
$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>
        alert('Silakan login terlebih dahulu!');
        window.location.href = 'login.php';
    </script>";
    exit();
}

// Ambil data riwayat berdasarkan username pembeli
// Kolom pencariannya disesuaikan dengan database kamu yaitu 'nama'
$query = "SELECT * FROM peminjaman WHERE nama = '$username' ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-responsive { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Riwayat Pemesanan Anda</h2>
            <p class="text-muted">Berikut adalah daftar alat pertanian yang sedang atau telah Anda sewa</p>
        </div>
        <a href="daftar_alat.php" class="btn btn-success fw-bold">← Kembali ke Katalog</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal Sewa</th>
                    <th>Nama Alat</th>
                    <th>Durasi</th>
                    <th>Total Bayar</th>
                    <th>Metode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) : ?>
                    <?php $no = 1; while($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td><strong><?= htmlspecialchars($row['alat']); ?></strong></td>
                            <td><?= htmlspecialchars($row['durasi']); ?> Hari</td>
                            <td class="text-success fw-bold">Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['metode']); ?></span></td>
                            <td>
                                <?php if ($row['status'] == 'lunas') : ?>
                                    <span class="badge bg-success px-3 py-2">Lunas</span>
                                <?php else : ?>
                                    <span class="badge bg-warning text-dark px-3 py-2">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Anda belum memiliki riwayat pemesanan alat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>