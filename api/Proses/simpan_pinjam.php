<?php
include dirname(__DIR__) . '/koneksi.php';

$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href = '../login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_alat   = $_POST['id_alat'];
    $nama_alat = $_POST['nama_alat'] ?? 'Alat Pertanian'; // Menangkap nama alat dari form
    $durasi    = $_POST['durasi'];
    $total     = $_POST['total'];
    $metode    = $_POST['metode'];
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // Query simpan ke database menggunakan nama kolom tabel kamu (username, alat, durasi, total, metode, status, tanggal)
    $query = "INSERT INTO peminjaman (username, alat, durasi, total, metode, status, tanggal) 
              VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";

    if (mysqli_query($koneksi, $query)) {
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");
        echo "<script>
            alert('Pemesanan Berhasil!');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>