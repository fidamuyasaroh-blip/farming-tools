<?php
require_once dirname(__DIR__) . '/koneksi.php';

$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='../login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../daftar_alat.php");
    exit();
}

$id_alat   = (int)($_POST['id_alat']   ?? $_POST['id']   ?? 0);
$nama_alat = trim($_POST['nama_alat']  ?? $_POST['alat'] ?? 'Alat Pertanian');
$durasi    = (int)($_POST['durasi']    ?? $_POST['hari']  ?? 1);
$total     = (float)($_POST['total']   ?? $_POST['total_harga'] ?? 0);
$metode    = trim($_POST['metode']     ?? 'BCA');
$tanggal   = date('Y-m-d');
$status    = 'belum lunas';

// Escape string untuk keamanan
$username_esc  = mysqli_real_escape_string($koneksi, $username);
$nama_alat_esc = mysqli_real_escape_string($koneksi, $nama_alat);
$metode_esc    = mysqli_real_escape_string($koneksi, $metode);

// Insert dengan kolom yang sudah pasti ada (sesuaikan nama kolom dengan skema DB kamu)
// Kolom: username, nama_alat, durasi, total_bayar, metode_bayar, tanggal, status
$query = "INSERT INTO peminjaman 
            (username, nama_alat, durasi, total_bayar, metode_bayar, tanggal, status)
          VALUES 
            ('$username_esc', '$nama_alat_esc', '$durasi', '$total', '$metode_esc', '$tanggal', '$status')";

if (mysqli_query($koneksi, $query)) {
    // Kurangi stok
    if ($id_alat > 0) {
        $stmt_stok = mysqli_prepare($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = ? AND stok > 0");
        mysqli_stmt_bind_param($stmt_stok, 'i', $id_alat);
        mysqli_stmt_execute($stmt_stok);
    }
    echo "<script>alert('Pemesanan Berhasil!'); window.location.href='../riwayat_pemesanan.php';</script>";
} else {
    $err = mysqli_error($koneksi);
    echo "<script>alert('Gagal menyimpan: $err'); window.location.href='../daftar_alat.php';</script>";
}
?>
