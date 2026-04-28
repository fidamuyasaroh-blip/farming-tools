<?php
// 1. Paksa tampilkan error agar tidak halaman putih polos
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Gunakan path absolut untuk koneksi agar tidak salah panggil
// __DIR__ membantu PHP menemukan folder yang tepat
require_once __DIR__ . '/../koneksi.php'; 

// 3. Cek Login menggunakan Cookie
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: ../login.php"); // Sesuaikan path ke login.php
    exit();
}

// --- LOGIKA CRUD (TAMBAH) ---
if (isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $nama_gambar   = $_FILES['gambar']['name'];
    $tmp_name      = $_FILES['gambar']['tmp_name'];
    $lokasi_simpan = '../img/' . $nama_gambar; // Pastikan folder img ada di root

    if (!is_dir('../img')) { mkdir('../img', 0777, true); }

    if (move_uploaded_file($tmp_name, $lokasi_simpan)) {
        $query = "INSERT INTO alat (nama_alat, harga, stok, deskripsi, gambar) 
                  VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$nama_gambar')";
        mysqli_query($koneksi, $query);
        header("Location: kelola_alat.php");
        exit();
    }
}

// --- LOGIKA CRUD (HAPUS) ---
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM alat WHERE id='$id'");
    header("Location: kelola_alat.php");
    exit();
}

// Ambil data untuk tabel
$query_tampil = mysqli_query($koneksi, "SELECT * FROM alat");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Alat - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
    <div class="container bg-white p-4 rounded shadow">
        <h2 class="fw-bold mb-4">Kelola Alat Pertanian</h2>
        
        <!-- Form Tambah (Singkat) -->
        <form action="" method="POST" enctype="multipart/form-data" class="mb-5 row g-3">
            <div class="col-md-4">
                <input type="text" name="nama_alat" class="form-control" placeholder="Nama Alat" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="harga" class="form-control" placeholder="Harga" required>
            </div>
            <div class="col-md-4">
                <input type="file" name="gambar" class="form-control" required>
            </div>
            <div class="col-12">
                <textarea name="deskripsi" class="form-control" placeholder="Deskripsi"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" name="tambah" class="btn btn-primary">Tambah Alat</button>
                <a href="../admin_dashboard.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>

        <!-- Tabel Tampil -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query_tampil)): ?>
                <tr>
                    <td><?= $row['nama_alat']; ?></td>
                    <td><?= number_format($row['harga']); ?></td>
                    <td>
                        <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>