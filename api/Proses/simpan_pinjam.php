<?php
// Jalur koneksi aman untuk Vercel & Localhost
include dirname(__DIR__) . '/koneksi.php';

// Ambil data user dari Cookie
$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href = '../login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangkap data asli yang diisi oleh user dari halaman pembayaran.php
    $id_alat   = $_POST['id_alat'];
    $nama_alat = $_POST['nama_alat']; // Ini yang menangkap nama alat asli (contoh: Combine Harvester)
    $durasi    = $_POST['durasi'];
    $total     = $_POST['total'];
    $metode    = $_POST['metode'];    // Ini yang menangkap metode asli (contoh: Transfer Mandiri / Gopay)
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // =========================================================================
    // QUERY LANGSUNG: Memasukkan data asli user sesuai struktur tabelmu
    // =========================================================================
    $query = "INSERT INTO peminjaman (username, alat, durasi, total, metode, status, tanggal) 
              VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";

    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok alat secara otomatis
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

        echo "<script>
            alert('Pemesanan Berhasil!');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "Gagal menyimpan pesanan. Pesan Error MySQL: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>