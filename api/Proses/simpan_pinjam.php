<?php
session_start();
require_once __DIR__ . '../koneksi.php';

$id_alat  = $_POST['id_alat'] ?? 0;
$durasi   = $_POST['durasi'] ?? 1;
$total    = $_POST['total'] ?? 0;
$metode   = $_POST['metode'] ?? '-';
$username = $_SESSION['username'] ?? 'Guest';

$query = mysqli_query($koneksi, "SELECT nama_alat FROM alat WHERE id = '$id_alat'");
$data  = mysqli_fetch_assoc($query);
$nama_alat = $data['nama_alat'] ?? 'Tidak Diketahui';

mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

$simpan = "INSERT INTO peminjaman (username, nama_alat, durasi, total_bayar, metode_bayar) 
           VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode')";
mysqli_query($koneksi, $simpan);

if ($metode == 'BCA') {
    header("Location: ../instruksi_bca.php?alat=$nama_alat&durasi=$durasi&total=$total&metode=$metode");
} elseif ($metode == 'GOPAY' || $metode == 'DANA') {
    header("Location: ../instruksi_gopay.php?alat=$nama_alat&durasi=$durasi&total=$total&metode=$metode");
} else {
    header("Location: daftar_alat.php");
}
exit();
?>