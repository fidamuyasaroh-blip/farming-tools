<?php
include 'koneksi.php';

$username = $_COOKIE['username'] ?? null;
if (!$username) {
    echo "<script>alert('Sesi habis, silakan login kembali.'); window.location.href='login.php';</script>";
    exit();
}

$id_alat = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$durasi  = (int)($_POST['hari'] ?? $_POST['lama_sewa'] ?? 1);

if ($id_alat <= 0) {
    header("Location: daftar_alat.php");
    exit();
}

$stmt = mysqli_prepare($koneksi, "SELECT * FROM alat WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_alat);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data   = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: daftar_alat.php");
    exit();
}

$alat           = $data['nama_alat'];
$harga_per_hari = (float)$data['harga'];
$total_bayar    = $durasi * $harga_per_hari;

// ============================================================
// PROSES SIMPAN SAAT FORM KONFIRMASI DIKIRIM
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_konfirmasi'])) {
    $nama_alat_final = mysqli_real_escape_string($koneksi, $_POST['nama_alat'] ?? $alat);
    $durasi_final    = (int)($_POST['durasi'] ?? $durasi);
    $total_final     = (float)($_POST['total'] ?? $total_bayar);
    $metode_final    = mysqli_real_escape_string($koneksi, $_POST['metode'] ?? 'BCA');
    $username_esc    = mysqli_real_escape_string($koneksi, $username);
    $tanggal_final   = date('Y-m-d');
    $status_final    = 'belum lunas';

    $query_insert = "INSERT INTO peminjaman 
                        (username, nama_alat, durasi, total_bayar, metode_bayar, tanggal, status)
                     VALUES 
                        ('$username_esc', '$nama_alat_final', '$durasi_final', '$total_final', '$metode_final', '$tanggal_final', '$status_final')";

    if (mysqli_query($koneksi, $query_insert)) {
        // Kurangi stok
        $stmt_stok = mysqli_prepare($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = ? AND stok > 0");
        mysqli_stmt_bind_param($stmt_stok, 'i', $id_alat);
        mysqli_stmt_execute($stmt_stok);

        echo "<script>alert('Pemesanan Berhasil! Menunggu konfirmasi admin.'); window.location.href='riwayat_pemesanan.php';</script>";
        exit();
    } else {
        $err = mysqli_error($koneksi);
        echo "<script>alert('Gagal menyimpan: $err');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding-top: 50px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .payment-card { max-width: 550px; margin: auto; border-radius: 20px; border: none; }
        .method-box { border: 2px solid #eee; border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; }
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .method-box { border-color: #2e7d32; background: #f1f8e9; box-shadow: 0 4px 10px rgba(46,125,50,0.1); }
    </style>
</head>
<body>
<div class="container mb-5">
    <div class="card payment-card shadow p-4">
        <h3 class="fw-bold mb-4 text-center">Detail Pembayaran</h3>

        <div class="bg-light p-3 rounded-3 mb-4 border">
            <div class="d-flex justify-content-between mb-2">
                <span>Alat: <strong><?= htmlspecialchars($alat); ?></strong></span>
                <span><?= $durasi; ?> Hari</span>
            </div>
            <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                <span>Total Tagihan:</span>
                <span class="text-success">Rp <?= number_format($total_bayar, 0, ',', '.'); ?></span>
            </div>
        </div>

        <form action="" method="POST">
            <input type="hidden" name="proses_konfirmasi" value="1">
            <input type="hidden" name="id"        value="<?= $id_alat; ?>">
            <input type="hidden" name="nama_alat" value="<?= htmlspecialchars($alat); ?>">
            <input type="hidden" name="durasi"    value="<?= $durasi; ?>">
            <input type="hidden" name="total"     value="<?= $total_bayar; ?>">

            <h5 class="mb-3 fw-semibold">Pilih Metode Pembayaran</h5>

            <label class="w-100 mb-2">
                <input type="radio" name="metode" value="BCA" checked>
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" width="60" class="me-3">
                    <div><strong>Transfer BCA</strong><br><small class="text-muted">8830-1234-567 a/n TERRALEASE</small></div>
                </div>
            </label>

            <label class="w-100 mb-2">
                <input type="radio" name="metode" value="Mandiri">
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" width="60" class="me-3">
                    <div><strong>Transfer Mandiri</strong><br><small class="text-muted">131-0012-3456 a/n TERRALEASE</small></div>
                </div>
            </label>

            <label class="w-100 mb-3">
                <input type="radio" name="metode" value="Gopay">
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" width="60" class="me-3">
                    <div><strong>GOPAY / QRIS</strong><br><small class="text-muted">0812-3456-7890 a/n TERRALEASE</small></div>
                </div>
            </label>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success py-3 fw-bold">Konfirmasi & Pesan Sekarang</button>
                <a href="daftar_alat.php" class="btn btn-light py-2 border">Batal</a>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
