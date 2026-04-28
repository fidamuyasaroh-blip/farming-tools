<?php
// 1. Tampilkan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Perbaikan Path Koneksi (Mencoba beberapa kemungkinan path Vercel)
if (file_exists(__DIR__ . '/../koneksi.php')) {
    require_once __DIR__ . '/../koneksi.php';
} elseif (file_exists(__DIR__ . '/koneksi.php')) {
    require_once __DIR__ . '/koneksi.php';
} else {
    die("Error: File koneksi.php tidak ditemukan. Pastikan file tersebut ada di root folder.");
}

// 3. Cek Login menggunakan Cookie
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit();
}

// --- LOGIKA TAMBAH ---
if (isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $nama_gambar   = $_FILES['gambar']['name'];
    $tmp_name      = $_FILES['gambar']['tmp_name'];
    $lokasi_simpan = '../img/' . $nama_gambar;

    if (!is_dir('../img')) { mkdir('../img', 0777, true); }

    if (move_uploaded_file($tmp_name, $lokasi_simpan)) {
        $query = "INSERT INTO alat (nama_alat, harga, stok, deskripsi, gambar) 
                  VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$nama_gambar')";
        mysqli_query($koneksi, $query);
        header("Location: kelola_alat.php");
        exit();
    }
}

// --- LOGIKA EDIT ---
if (isset($_POST['edit'])) {
    $id        = $_POST['id'];
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    if (!empty($_FILES['gambar']['name'])) {
        $nama_gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../img/' . $nama_gambar);
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi', gambar='$nama_gambar' WHERE id='$id'";
    } else {
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi' WHERE id='$id'";
    }

    mysqli_query($koneksi, $query);
    header("Location: kelola_alat.php");
    exit();
}

// --- LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM alat WHERE id='$id'");
    header("Location: kelola_alat.php");
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
    <h2 class="fw-bold mb-4">Kelola Alat Pertanian</h2>

    <?php if ($edit_data): ?>
        <!-- Form Edit -->
        <div class="alert alert-warning">Mode Edit: <strong><?= $edit_data['nama_alat']; ?></strong></div>
        <form action="" method="POST" enctype="multipart/form-data" class="mb-5 row g-3">
            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
            <div class="col-md-4">
                <label class="form-label fw-bold">Nama Alat</label>
                <input type="text" name="nama_alat" class="form-control" value="<?= $edit_data['nama_alat']; ?>" required>
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
                <textarea name="deskripsi" class="form-control" rows="2"><?= $edit_data['deskripsi']; ?></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold">Ganti Foto (Opsional)</label>
                <input type="file" name="gambar" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" name="edit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                <a href="kelola_alat.php" class="btn btn-secondary">Batal</a>
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
                <a href="../admin_dashboard.php" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
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
                <td><img src="../img/<?= $row['gambar']; ?>" width="50" height="50"></td>
                <td class="fw-bold"><?= $row['nama_alat']; ?></td>
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