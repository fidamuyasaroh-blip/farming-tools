<?php
session_start();

// PERBAIKAN 1: Jalur koneksi yang benar untuk naik satu tingkat ke folder utama
include '../koneksi.php'; 

// PERBAIKAN 2: Jalur tendangan login dinaikkan satu tingkat (../login.php)
// agar tidak mencari file login di dalam folder proses/admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID dari tombol konfirmasi yang diklik admin
$id = $_GET['id'] ?? 0;

if ($id) {
    // Jalankan query update status menjadi lunas
    mysqli_query($koneksi, "UPDATE peminjaman SET status='lunas' WHERE id='$id'");
}

// Setelah sukses mengubah data, kembalikan admin ke halaman riwayat
header("Location: riwayat_pemesanan.php");
exit();
?>