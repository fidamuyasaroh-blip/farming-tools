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

// Aksi: Tambah Alat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_alat'])) {
    $nama_alat = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = (float)$_POST['harga'];
    $stok      = (int)$_POST['stok'];
    $gambar    = mysqli_real_escape_string($koneksi, $_POST['gambar']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $q = "INSERT INTO alat (nama_alat, harga, stok, gambar, deskripsi) VALUES ('$nama_alat', '$harga', '$stok', '$gambar', '$deskripsi')";
    if (mysqli_query($koneksi, $q)) {
        echo "<script>alert('Alat berhasil ditambahkan!'); window.location.href='admin_dashboard.php?tab=tambah-alat';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal menambah alat: " . mysqli_error($koneksi) . "');</script>";
    }
}

$result_pesanan = mysqli_query($koneksi, "SELECT * FROM peminjaman ORDER BY id DESC");
$result_alat    = mysqli_query($koneksi, "SELECT * FROM alat ORDER BY id DESC");
$tab_aktif      = $_GET['tab'] ?? 'transaksi';
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
        .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 600; padding: 12px 20px; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom: 3px solid #198754; background: none; }
        .img-preview-katalog { width: 110px; height: 80px; object-fit: cover; border-radius: 8px; }
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
            <a href="kelola_user.php" class="nav-link text-white">Kelola User</a>
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
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Dashboard Panel Admin</h2>
        <p class="text-muted">Kelola transaksi penyewaan dan katalog alat pertanian.</p>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <button class="nav-link <?= $tab_aktif === 'transaksi' ? 'active' : ''; ?>"
                    data-bs-toggle="tab" data-bs-target="#transaksi-content" type="button">
                📋 Kelola Transaksi
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?= $tab_aktif === 'tambah-alat' ? 'active' : ''; ?>"
                    data-bs-toggle="tab" data-bs-target="#tambah-alat-content" type="button">
                ➕ Tambah Alat
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAB TRANSAKSI -->
        <div class="tab-pane fade <?= $tab_aktif === 'transaksi' ? 'show active' : ''; ?>" id="transaksi-content">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 text-primary">Daftar Transaksi User</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th><th>Tanggal</th><th>User</th><th>Alat</th>
                                <th>Durasi</th><th>Total Bayar</th><th>Metode</th><th>Status</th><th>Aksi</th>
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

        <!-- TAB TAMBAH ALAT -->
        <div class="tab-pane fade <?= $tab_aktif === 'tambah-alat' ? 'show active' : ''; ?>" id="tambah-alat-content">
            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold mb-3 text-success">Form Tambah Alat</h5>
                        <form action="admin_dashboard.php" method="POST">
                            <input type="hidden" name="tambah_alat" value="1">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Alat</label>
                                <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Combine Harvester" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Harga Sewa / Hari (Rp)</label>
                                <input type="number" name="harga" class="form-control" placeholder="150000" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Stok</label>
                                <input type="number" name="stok" class="form-control" placeholder="5" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">URL Gambar</label>
                                <input type="text" name="gambar" class="form-control" placeholder="https://..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success fw-bold">Simpan ke Katalog</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-7 mb-4">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold mb-3">Daftar Katalog</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr><th>No</th><th>Gambar</th><th>Nama & Deskripsi</th><th>Harga</th><th>Stok</th></tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_alat && mysqli_num_rows($result_alat) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result_alat)):
                                            $g = $row['gambar'];
                                            $src = filter_var($g, FILTER_VALIDATE_URL) ? htmlspecialchars($g) : '../img/' . htmlspecialchars($g);
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><img src="<?= $src; ?>" class="img-preview-katalog" onerror="this.src='https://placehold.co/110x80?text=No+Img'"></td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_alat']); ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars(substr($row['deskripsi'] ?? '', 0, 80)); ?>...</small>
                                            </td>
                                            <td class="text-success fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                            <td><span class="badge bg-info"><?= (int)$row['stok']; ?> unit</span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">Belum ada alat.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
