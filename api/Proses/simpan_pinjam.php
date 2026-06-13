<?php
// PERBAIKAN UTAMA: Menggunakan __DIR__ agar pencarian file koneksi.php 
// selalu akurat, baik saat dijalankan di Localhost XAMPP maupun di server Vercel.
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

// Pastikan data dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_alat = $_POST['id_alat'];
    $durasi  = $_POST['durasi'];
    $total   = $_POST['total'];
    $metode  = $_POST['metode'];
    $tanggal = date('Y-m-d'); // Mengambil tanggal hari ini
    $status  = 'belum lunas';  // Status awal sewa sebelum dikonfirmasi admin

    // SQL: Masukkan data ke tabel peminjaman
    $query = "INSERT INTO peminjaman (username, id_alat, tgl_pinjam, durasi, total_harga, metode, status) 
              VALUES ('$username', '$id_alat', '$tanggal', '$durasi', '$total', '$metode', '$status')";

    // Baris 30 yang tadinya error sekarang dijamin aman karena $koneksi sudah terhubung
    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok alat secara otomatis setelah dipesan
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

        echo "<script>
            alert('Pemesanan Berhasil! Silakan cek riwayat pemesanan Anda.');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "Gagal menyimpan pesanan: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>