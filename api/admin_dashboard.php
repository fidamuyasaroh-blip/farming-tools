<?php
// JALUR MUTLAK: Menggunakan __DIR__ agar aman di Vercel & Localhost
require_once __DIR__ . '/koneksi.php';

// Ambil data user dari Cookie
$username = $_COOKIE['username'] ?? 'Admin';

// =========================================================================
// AKSI 1: PROSES MENGUBAH STATUS MENJADI LUNAS
// =========================================================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'set_lunas' && isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
    $query_update = "UPDATE peminjaman SET status = 'lunas' WHERE id = '$id_transaksi'";
    
    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>
            alert('Status transaksi berhasil diperbarui menjadi LUNAS!');
            window.location.href = 'admin_dashboard.php';
        </script>";
        exit();
    }
}

// =========================================================================
// AKSI 2: PROSES TAMBAH KATALOG ALAT BARU + DESKRIPSI
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_alat'])) {
    $nama_alat = $_POST['nama_alat'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $gambar    = $_POST['gambar'];
    $deskripsi = $_POST['deskripsi']; // Menangkap deskripsi alat baru

    $query_tambah = "INSERT INTO alat (nama_alat, harga, stok, gambar, deskripsi) VALUES ('$nama_alat', '$harga', '$stok', '$gambar', '$deskripsi')";
    
    if (mysqli_query($koneksi, $query_tambah)) {
        echo "<script>
            alert('Katalog Alat Pertanian Berhasil Ditambahkan!');
            // Mengarahkan kembali dan otomatis membuka tab 'tambah-alat' setelah reload
            window.location.href = 'admin_dashboard.php?tab=tambah-alat';
        </script>";
        exit();
    } else {
        echo "<script>alert('Gagal menambah alat: " . mysqli_error($koneksi) . "');</script>";
    }
}

// =========================================================================
// QUERY DATA: Ambil data transaksi sewa dan katalog alat
// =========================================================================
$query_pesanan = "SELECT * FROM peminjaman ORDER BY id DESC";
$result_pesanan = mysqli_query($koneksi, $query_pesanan);

$query_tampil = "SELECT * FROM alat ORDER BY id DESC";
$result_alat = mysqli_query($koneksi, $query_tampil);

// Cek parameter tab aktif dari URL saat reload
$tab_aktif = $_GET['tab'] ?? 'transaksi';
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
        .sidebar { height: 100vh; background: #212529; color: white; padding-top: 20px; position: fixed; width: 240px; }
        .main-content { margin-left: 240px; padding: 40px; }
        .card-custom { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .nav-tabs .nav-link { border: none; color: #6c757d; font-weight: 600; padding: 12px 20px; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom: 3px solid #198754; background: none; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center fw-bold mb-4 text-success">TERRALEASE</h4>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="admin_dashboard.php" class="nav-link active bg-success text-white fw-bold">Dashboard Admin</a>
        </li>
        <li class="nav-item mb-2">
            <a href="daftar_alat.php" class="nav-link text-white">Lihat Katalog User</a>
        </li>
    </ul>
    <hr>
    <div class="text-center text-muted small">Login Sebagai: <strong><?= htmlspecialchars($username); ?></strong></div>
</div>

<div class="main-content">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Dashboard Panel Admin</h2>
        <p class="text-muted">Kelola seluruh operasional TERRALEASE dalam satu tempat.</p>
    </div>

    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link <?= $tab_aktif == 'transaksi' ? 'active' : ''; ?>" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi-content" type="button" role="tab">
                📋 Kelola Transaksi Sewa
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?= $tab_aktif == 'tambah-alat' ? 'active' : ''; ?>" id="tambah-alat-tab" data-bs-toggle="tab" data-bs-target="#tambah-alat-content" type="button" role="tab">
                ➕ Tambah & Katalog Alat
            </button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">
        
        <div class="tab-pane fade <?= $tab_aktif == 'transaksi' ? 'show active' : ''; ?>" id="transaksi-content" role="tabpanel">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 text-primary">Daftar Transaksi Masuk</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Alat Pertanian</th>
                                <th>Durasi</th>
                                <th>Total Bayar</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th class="text-center">Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result_pesanan) > 0) : ?>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($result_pesanan)) : 
                                    $tampil_status = $row['status'] ?? 'belum lunas';
                                ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= isset($row['tanggal']) ? date('d M Y', strtotime($row['tanggal'])) : date('d M Y'); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['username'] ?? 'User'); ?></span></td>
                                        <td><strong><?= htmlspecialchars($row['alat'] ?? ($row['nama_alat'] ?? 'Alat')); ?></strong></td>
                                        <td><?= htmlspecialchars($row['durasi'] ?? 1); ?> Hari</td>
                                        <td class="text-success fw-bold">Rp <?= number_format($row['total_harga'] ?? ($row['total'] ?? 0), 0, ',', '.'); ?></td>
                                        <td><small class="fw-semibold text-secondary"><?= htmlspecialchars($row['metode'] ?? 'BCA'); ?></small></td>
                                        <td>
                                            <?php if (strtolower($tampil_status) == 'lunas') : ?>
                                                <span class="badge bg-success rounded-pill px-3 py-2">Lunas</span>
                                            <?php else : ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Belum Lunas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (strtolower($tampil_status) != 'lunas') : ?>
                                                <a href="admin_dashboard.php?aksi=set_lunas&id=<?= $row['id']; ?>" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menyetujui transaksi ini menjadi LUNAS?')" 
                                                   class="btn btn-sm btn-success fw-bold px-3">
                                                    ✓ Set Lunas
                                                </a>
                                            <?php else : ?>
                                                <span class="text-muted small">Selesai ✓</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Belum ada transaksi masuk dari user.</td>
                                endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade <?= $tab_aktif == 'tambah-alat' ? 'show active' : ''; ?>" id="tambah-alat-content" role="tabpanel">
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
                                <input type="number" name="harga" class="form-control" placeholder="Contoh: 150000" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jumlah Stok</label>
                                <input type="number" name="stok" class="form-control" placeholder="Contoh: 5" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">URL Link Gambar</label>
                                <input type="text" name="gambar" class="form-control" placeholder="https://..." required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Deskripsi / Spesifikasi Alat</label>
                                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Masukkan keunggulan atau spesifikasi teknis alat..." required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success fw-bold py-2">Simpan ke Katalog</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7 mb-4">
                    <div class="card card-custom p-4">
                        <h5 class="fw-bold mb-3">Daftar Katalog Alat Pertanian</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Gambar</th>
                                        <th>Nama & Deskripsi</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result_alat) > 0) : ?>
                                        <?php $no = 1; while($row = mysqli_fetch_assoc($result_alat)) : ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td>
                                                    <img src="<?= htmlspecialchars($row['gambar']); ?>" width="55" height="45" class="rounded object-fit-cover" onerror="this.src='https://placehold.co/55x45?text=Alat'">
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($row['nama_alat']); ?></strong><br>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 220px;">
                                                        <?= htmlspecialchars($row['deskripsi'] ?? 'Tidak ada deskripsi.'); ?>
                                                    </small>
                                                </td>
                                                <td class="text-success fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                                <td><span class="badge bg-info"><?= $row['stok']; ?> unit</span></td>
                                            </tr>
                                        <?php endwhile; ?>
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