<?php
// PERBAIKAN JALUR MUTLAK: Menggunakan __DIR__ agar file koneksi langsung terbaca di folder api/
require_once __DIR__ . '/koneksi.php';

// Cek cookie untuk proteksi halaman admin (opsional, sesuaikan dengan sistem login adminmu)
$username = $_COOKIE['username'] ?? null;

// Mengambil semua data transaksi dari tabel peminjaman
$query = "SELECT * FROM peminjaman ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Gagal mengambil data database: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f1f3f5; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { height: 100vh; background: #212529; color: white; padding-top: 20px; position: fixed; width: 240px; }
        .main-content { margin-left: 240px; padding: 40px; }
        .card-custom { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center fw-bold mb-4 text-success">TERRALEASE</h4>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="admin_dashboard.php" class="nav-link active bg-success text-white fw-bold"> Kelola Pesanan</a>
        </li>
        <li class="nav-item mb-2">
            <a href="daftar_alat.php" class="nav-link text-white"> Lihat Katalog</a>
        </li>
    </ul>
    <hr>
    <div class="text-center text-muted small">Logged in as: <strong><?= htmlspecialchars($username ?? 'Admin'); ?></strong></div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Dashboard Manajemen Pesanan</h2>
            <p class="text-muted">Konfirmasi pembayaran dan kelola status sewa alat pertanian user disini.</p>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan (Username)</th>
                        <th>Nama Alat</th>
                        <th>Durasi</th>
                        <th>Total Bayar</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th class="text-center">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($result)) : 
                            $tampil_id      = $row['id'];
                            $tampil_user    = $row['username'] ?? 'User';
                            $tampil_alat    = $row['alat'] ?? 'Alat Pertanian';
                            $tampil_total   = $row['total'] ?? 0;
                            $tampil_tanggal = $row['tanggal'] ?? date('Y-m-d');
                            $tampil_metode  = $row['metode'] ?? 'BCA';
                            $tampil_durasi  = $row['durasi'] ?? 1;
                            $tampil_status  = $row['status'] ?? 'belum lunas';
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= date('d M Y', strtotime($tampil_tanggal)); ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($tampil_user); ?></span></td>
                                <td><strong><?= htmlspecialchars($tampil_alat); ?></strong></td>
                                <td><?= htmlspecialchars($tampil_durasi); ?> Hari</td>
                                <td class="text-success fw-bold">Rp <?= number_format((float)$tampil_total, 0, ',', '.'); ?></td>
                                <td><small class="fw-semibold text-secondary"><?= htmlspecialchars($tampil_metode); ?></small></td>
                                <td>
                                    <?php if (strtolower($tampil_status) == 'lunas') : ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2">Lunas</span>
                                    <?php else : ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Belum Lunas</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (strtolower($tampil_status) != 'lunas') : ?>
                                        <button onclick="prosesLunas('<?= $tampil_id; ?>', '<?= htmlspecialchars($tampil_user); ?>')" class="btn btn-sm btn-outline-success fw-bold px-3">
                                            ✓ Set Lunas
                                        </button>
                                    <?php else : ?>
                                        <span class="text-muted small">Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada transaksi sewa yang masuk ke sistem.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Fungsi trigger ketika admin menekan tombol "Set Lunas"
function prosesLunas(idTransaksi, namaUser) {
    if (confirm("Apakah Anda yakin ingin memverifikasi transaksi ID #" + idTransaksi + " atas nama " + namaUser + " menjadi LUNAS?")) {
        // Alihkan ke file aksi update status (opsional jika kamu ingin membuat file update_status.php nanti)
        alert("Fitur update status siap dihubungkan ke file Proses backend!");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>