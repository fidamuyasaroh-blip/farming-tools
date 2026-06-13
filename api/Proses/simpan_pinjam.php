<?php
// Jalur koneksi aman untuk Vercel & Localhost
include dirname(__DIR__) . '/koneksi.php';

// Ambil data user dari Cookie
$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>
        alert('Silakan login terlebih dahulu!');
        window.location.href = '../login.php';
    </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_alat = $_POST['id_alat'];
    $durasi  = $_POST['durasi'];
    $total   = $_POST['total'];
    $metode  = $_POST['metode'];
    $tanggal = date('Y-m-d');
    $status  = 'belum lunas';

    // Ambil nama alat asli dari tabel alat untuk dimasukkan ke kolom 'alat' di tabel peminjaman
    $cari_alat = mysqli_query($koneksi, "SELECT nama_alat FROM alat WHERE id = '$id_alat'");
    $data_alat = mysqli_fetch_assoc($cari_alat);
    $nama_alat = $data_alat['nama_alat'] ?? 'Alat Pertanian';

    // =========================================================================
    // PERBAIKAN STRUKTUR: Disesuaikan 100% dengan isi tabel peminjaman di phpMyAdmin kamu!
    // Kolom database: nama, alat, durasi, total, metode, status, tanggal
    // =========================================================================
    $query = "INSERT INTO peminjaman (nama, alat, durasi, total, metode, status, tanggal) 
              VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";

    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok alat secara otomatis
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

        echo "<script>
            alert('Pemesanan Berhasil! Silakan cek riwayat pemesanan Anda.');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "Gagal menyimpan pesanan. Pesan Error: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>