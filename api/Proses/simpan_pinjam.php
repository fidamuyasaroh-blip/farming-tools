<?php
require_once __DIR__ . '/../koneksi.php'; 

// PERBAIKAN: Gunakan Cookie bukan Session (konsisten dengan sistem lain)
if (!isset($_COOKIE['username'])) {
    header("Location: /api/login.php");
    exit();
}

$id_alat  = $_POST['id_alat'] ?? 0;
$durasi   = $_POST['durasi'] ?? 1;
$total    = $_POST['total'] ?? 0;
$metode   = $_POST['metode'] ?? '-';
$username = $_COOKIE['username']; // PERBAIKAN: dari Cookie bukan Session

$query = mysqli_query($koneksi, "SELECT nama_alat FROM alat WHERE id = '$id_alat'");
$data  = mysqli_fetch_assoc($query);
$nama_alat = $data['nama_alat'] ?? 'Tidak Diketahui';

mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

$simpan = "INSERT INTO peminjaman (username, nama_alat, durasi, total_bayar, metode_bayar) 
           VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode')";
mysqli_query($koneksi, $simpan);

$nama_alat_enc = urlencode($nama_alat);
if ($metode == 'BCA') {
    header("Location: /api/instruksi_bca.php?alat={$nama_alat_enc}&durasi={$durasi}&total={$total}&metode={$metode}");
} elseif ($metode == 'GOPAY' || $metode == 'DANA') {
    header("Location: /api/instruksi_gopay.php?alat={$nama_alat_enc}&durasi={$durasi}&total={$total}&metode={$metode}");
} else {
    header("Location: /api/daftar_alat.php");
}
exit();
?>
