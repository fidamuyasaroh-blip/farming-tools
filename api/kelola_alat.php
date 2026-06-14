<?php
require_once __DIR__ . '/koneksi.php';

if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- LOGIKA TAMBAH ---
if (isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = (float)$_POST['harga'];
    $stok      = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar    = mysqli_real_escape_string($koneksi, $_POST['gambar']); // URL gambar

    $query = "INSERT INTO alat (nama_alat, harga, stok, deskripsi, gambar)
              VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$gambar')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: kelola_alat.php");
        exit();
    } else {
        echo "<script>alert('Gagal tambah: " . mysqli_error($koneksi) . "');</script>";
    }
}

// --- LOGIKA EDIT ---
if (isset($_POST['edit'])) {
    $id        = (int)$_POST['id'];
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = (float)$_POST['harga'];
    $stok      = (int)$_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar    = mysqli_real_escape_string($koneksi, $_POST['gambar']);

    // Jika field gambar dikosongkan, pertahankan gambar lama
    if (!empty($gambar)) {
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi', gambar='$gambar' WHERE id='$id'";
    } else {
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi' WHERE id='$id'";
    }

    mysqli_query($koneksi, $query);
    header("Location: kelola_alat.php");
    exit();
}

// --- LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM alat WHERE id='$id'");
    header("Location: kelola_alat.php");
    exit();
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id = (int)$_GET['edit_id'];
    $q = mysqli_query($koneksi, "SELECT * FROM alat WHERE id='$id'");
    $edit_data = mysqli_fetch_assoc($q);
}

$query_tampil = mysqli_query($koneksi, "SELECT * FROM alat ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Alat - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f1f3f5; font-family: 'Plus Jakarta Sans', sans-serif; }
        .img-preview { width: 55px; height: 50px; object-fit: cover; border-radius: 6px; }
        #preview-box { width: 100%; height: 160px; object-fit: cover; border-radius: 10px; border: 2px dashed #ccc; display: none; }
    </style>
</head>
<body class="p-4">

<div class="container bg-white p-4 rounded shadow">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Kelola Alat Pertanian</h2>
        <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">← Kembali ke Dashboard</a>
    </div>

    <?php if ($edit_data): ?>
        <!-- FORM EDIT -->
        <div class="alert alert-warning">Mode Edit: <strong><?= htmlspecialchars($edit_data['nama_alat']); ?></strong></div>
        <form action="" method="POST" class="mb-5 row g-3">
            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
            <div class="col-md-4">
                <label class="form-label fw-bold">Nama Alat</label>
                <input type="text" name="nama_alat" class="form-control" value="<?= htmlspecialchars($edit_data['nama_alat']); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Harga Sewa / Hari</label>
                <input type="number" name="harga" class="form-control" value="<?= $edit_data['harga']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Stok</label>
                <input type="number" name="stok" class="form-control" value="<?= $edit_data['stok']; ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($edit_data['deskripsi']); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">URL Gambar <span class="text-muted fw-normal">(kosongkan jika tidak ingin mengganti)</span></label>
                <input type="text" name="gambar" id="url-edit" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($edit_data['gambar']); ?>" oninput="previewImg(this.value, 'preview-edit')">
                <img id="preview-edit" src="<?= htmlspecialchars($edit_data['gambar']); ?>" class="mt-2" style="width:100%;height:160px;object-fit:cover;border-radius:10px;border:2px dashed #ccc;" onerror="this.style.display='none'">
            </div>
            <div class="col-12">
                <button type="submit" name="edit" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                <a href="kelola_alat.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>

    <?php else: ?>
        <!-- FORM TAMBAH -->
        <form action="" method="POST" class="mb-5 row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Nama Alat</label>
                <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Traktor Modern" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Harga Sewa / Hari</label>
                <input type="number" name="harga" class="form-control" placeholder="500000" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Stok</label>
                <input type="number" name="stok" class="form-control" placeholder="5" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" placeholder="Deskripsi alat..." rows="2"></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">URL Gambar</label>
                <input type="text" name="gambar" id="url-tambah" class="form-control" placeholder="https://contoh.com/gambar.jpg" required oninput="previewImg(this.value, 'preview-tambah')">
                <img id="preview-tambah" class="mt-2" style="width:100%;height:160px;object-fit:cover;border-radius:10px;border:2px dashed #ccc;display:none;" onerror="this.style.display='none'">
            </div>
            <div class="col-12">
                <button type="submit" name="tambah" class="btn btn-success fw-bold px-4">Tambah Alat</button>
            </div>
        </form>
    <?php endif; ?>

    <!-- TABEL DAFTAR ALAT -->
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Foto</th>
                <th>Nama Alat</th>
                <th>Harga / Hari</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($query_tampil)):
                $g = $row['gambar'];
                // Gambar bisa URL penuh atau nama file lokal
                $src = filter_var($g, FILTER_VALIDATE_URL) ? htmlspecialchars($g) : '../img/' . htmlspecialchars($g);
            ?>
            <tr>
                <td>
                    <img src="<?= $src; ?>" class="img-preview"
                         onerror="this.src='https://placehold.co/55x50?text=No+Img'">
                </td>
                <td class="fw-bold"><?= htmlspecialchars($row['nama_alat']); ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td>
                    <span class="badge <?= $row['stok'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                        <?= (int)$row['stok']; ?> unit
                    </span>
                </td>
                <td>
                    <a href="?edit_id=<?= $row['id']; ?>" class="btn btn-warning btn-sm fw-bold">Edit</a>
                    <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm fw-bold"
                       onclick="return confirm('Hapus alat <?= htmlspecialchars($row['nama_alat']); ?>?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function previewImg(url, targetId) {
    const img = document.getElementById(targetId);
    if (url.trim() !== '') {
        img.src = url;
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>