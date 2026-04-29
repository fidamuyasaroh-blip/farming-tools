<?php
// PERBAIKAN: Sederhanakan include koneksi
require_once __DIR__ . '/koneksi.php';

// Cek Login menggunakan Cookie
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: /api/login.php"); 
    exit();
}

// --- LOGIKA TAMBAH ---
if (isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Di Vercel file upload tidak persisten, simpan nama file saja
    $nama_gambar = mysqli_real_escape_string($koneksi, $_FILES['gambar']['name'] ?? '');
    
    $query = "INSERT INTO alat (nama_alat, harga, stok, deskripsi, gambar) 
              VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$nama_gambar')";
    mysqli_query($koneksi, $query);
    header("Location: /api/kelola_alat.php");
    exit();
}

// --- LOGIKA EDIT ---
if (isset($_POST['edit'])) {
    $id        = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    if (!empty($_FILES['gambar']['name'])) {
        $nama_gambar = mysqli_real_escape_string($koneksi, $_FILES['gambar']['name']);
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi', gambar='$nama_gambar' WHERE id='$id'";
    } else {
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi' WHERE id='$id'";
    }

    mysqli_query($koneksi, $query);
    header("Location: /api/kelola_alat.php");
    exit();
}

// --- LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM alat WHERE id='$id'");
    header("Location: /api/kelola_alat.php");
    exit();
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit_id']);
    $q = mysqli_query($koneksi, "SELECT * FROM alat WHERE id='$id'");
    $edit_data = mysqli_fetch_assoc($q);
}

$query_tampil = mysqli_query($koneksi, "SELECT * FROM alat");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Alat - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.table img { object-fit: cover; border-radius: 5px; }</style>
</head>
<body class="p-5 bg-light">

<div class="container bg-white p-4 rounded shadow">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Kelola Alat Pertanian</h2>
        <a href="/api/admin_dashboard.php" class="btn btn-outline-secondary btn-sm">← Kembali ke Dashboard</a>
    </div>

    <?php if ($edit_data): ?>
        <!-- Form Edit -->
        <div class="alert alert-warning">Mode Edit: <strong><?= htmlspecialchars($edit_data['nama_alat']); ?></strong></div>
        <form action="" method="POST" enctype="multipart/form-data" class="mb-5 row g-3">
            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
            <div class="col-md-4">
                <label class="form-label fw-bold">Nama Alat</label>
                <input type="text" name="nama_alat" class="form-control" value="<?= htmlspecialchars($edit_data['nama_alat']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Harga Sewa</label>
                <input type="number" name="harga" class="form-control" value="<?= $edit_data['harga']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Stok</label>
                <input type="number" name="stok" class="form-control" value="<?= $edit_data['stok']; ?>" required>
            </div>
            <div class="col-12">
                <textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($edit_data['deskripsi']); ?></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold">Ganti Foto (Opsional)</label>
                <input type="file" name="gambar" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" name="edit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                <a href="/api/kelola_alat.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    <?php else: ?>
        <!-- Form Tambah -->
        <form action="" method="POST" enctype="multipart/form-data" class="mb-5 row g-3">
            <div class="col-md-4"><input type="text" name="nama_alat" class="form-control" placeholder="Nama Alat" required></div>
            <div class="col-md-4"><input type="number" name="harga" class="form-control" placeholder="Harga" required></div>
            <div class="col-md-4"><input type="number" name="stok" class="form-control" placeholder="Stok" required></div>
            <div class="col-12"><textarea name="deskripsi" class="form-control" placeholder="Deskripsi"></textarea></div>
            <div class="col-md-12"><input type="file" name="gambar" class="form-control" required></div>
            <div class="col-12">
                <button type="submit" name="tambah" class="btn btn-primary fw-bold">Tambah Alat</button>
            </div>
        </form>
    <?php endif; ?>

    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Foto</th>
                <th>Nama Alat</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query_tampil)): ?>
            <tr>
                <td><img src="/img/<?= htmlspecialchars($row['gambar']); ?>" width="50" height="50" onerror="this.src='https://placehold.co/50x50?text=?'"></td>
                <td class="fw-bold"><?= htmlspecialchars($row['nama_alat']); ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?= $row['stok']; ?></td>
                <td>
                    <a href="?edit_id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
