<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /api/login.php");
    exit();
}
include 'koneksi.php';

// Tambah
if (isset($_POST['tambah'])) {
    $nama      = $_POST['nama_alat'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    
    $nama_gambar   = $_FILES['gambar']['name'];
    $tmp_name      = $_FILES['gambar']['tmp_name'];
    $lokasi_simpan = 'img/' . $nama_gambar;

    if (!is_dir('img')) { mkdir('img'); }

    if (move_uploaded_file($tmp_name, $lokasi_simpan)) {
        $query = "INSERT INTO alat (nama_alat, harga, stok, deskripsi, gambar) 
                  VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$nama_gambar')";
        if(mysqli_query($koneksi, $query)) {
            echo "<script>window.location.href='kelola_alat.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
        exit();
    }
}

// Edit
if (isset($_POST['edit'])) {
    $id        = $_POST['id'];
    $nama      = $_POST['nama_alat'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];

    // Cek apakah ada gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $nama_gambar   = $_FILES['gambar']['name'];
        $tmp_name      = $_FILES['gambar']['tmp_name'];
        move_uploaded_file($tmp_name, 'img/' . $nama_gambar);
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi', gambar='$nama_gambar' WHERE id='$id'";
    } else {
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi' WHERE id='$id'";
    }

    mysqli_query($koneksi, $query);
    echo "<script>window.location.href='kelola_alat.php';</script>";
    exit();
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM alat WHERE id='$id'");
    echo "<script>window.location.href='kelola_alat.php';</script>";
    exit();
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
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
    <style>
        .table img { object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body class="p-5 bg-light">

<div class="container bg-white p-4 rounded shadow">
    <h2 class="fw-bold mb-4">Kelola Alat Pertanian</h2>

    <!-- TAMBAH / EDIT -->
    <?php if ($edit_data): ?>
        <!-- Form Edit -->
        <div class="alert alert-warning">Mode Edit: <strong><?= $edit_data['nama_alat']; ?></strong></div>
        <form action="" method="POST" enctype="multipart/form-data" class="mb-5">
            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
            <div class="row g-3">
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
                    <label class="form-label fw-bold">Deskripsi Alat</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required><?= $edit_data['deskripsi']; ?></textarea>
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-bold">Foto Alat (kosongkan jika tidak diganti)</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" name="edit" class="btn btn-warning w-100 fw-bold">Simpan Perubahan</button>
                </div>
            </div>
            <a href="api/kelola_alat.php" class="btn btn-secondary mt-3">Batal Edit</a>
        </form>
    <?php else: ?>
        <!-- Tambah -->
        <form action="" method="POST" enctype="multipart/form-data" class="mb-5">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Nama Alat</label>
                    <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Traktor" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Harga Sewa</label>
                    <input type="number" name="harga" class="form-control" placeholder="Contoh: 550000 (tanpa titik)" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Stok</label>
                    <input type="number" name="stok" class="form-control" placeholder="Jumlah" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi Alat</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan detail alat di sini..." required></textarea>
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-bold">Foto Alat</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="tambah" class="btn btn-primary w-100 fw-bold">Tambah Alat</button>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <!-- TABEL -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Gambar</th>
                    <th>Nama Alat</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query_tampil)): ?>
                <tr>
                    <td><img src="img/<?= $row['gambar']; ?>" width="60" height="60"></td>
                    <td class="fw-bold"><?= $row['nama_alat']; ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td><?= $row['stok']; ?></td>
                    <td><small class="text-muted"><?= substr($row['deskripsi'], 0, 50); ?>...</small></td>
                    <td>
                        <a href="kelola_alat.php?edit=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="kelola_alat.php?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <br>
    <a href="api/admin_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

</body>
</html>