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
    // Menangkap data asli dari form pembayaran.php
    $id_alat   = $_POST['id_alat'] ?? 0;
    $nama_alat = $_POST['nama_alat'] ?? 'Alat Pertanian'; 
    $durasi    = $_POST['durasi'] ?? 1;
    $total     = $_POST['total'] ?? 0; // Mengambil angka nominal dari form
    $metode    = $_POST['metode'] ?? 'BCA';
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // =========================================================================
    // QUERY PAS: Langsung menembak nama_alat dan total_harga yang terbukti valid
    // =========================================================================
    $query = "INSERT INTO peminjaman (username, nama_alat, durasi, total_harga, metode, status, tanggal) 
              VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";

    // Jalankan query-nya
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil disimpan, kurangi stok alat dan pindah ke halaman riwayat
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");
        echo "<script>
            alert('Pemesanan Berhasil!');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        // Tampilkan pesan jika ada error struktur lainnya
        die("Gagal menyimpan ke database. Error: " . mysqli_error($koneksi));
    }

} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>