<?php
session_start();
// PERBAIKAN 1: Perbaikan path absolut (ditambahkan slash '/')
require_once __DIR__ . '/../koneksi.php';

$id_alat  = $_POST['id_alat'] ?? 0;
$durasi   = $_POST['durasi'] ?? 1;
$total    = $_POST['total'] ?? 0;
$metode   = $_POST['metode'] ?? '-';
$username = $_SESSION['username'] ?? 'Guest';

// Cek apakah alat ada dan stoknya mencukupi
$query_stok = mysqli_query($koneksi, "SELECT nama_alat, stok FROM alat WHERE id = '$id_alat'");
$data_alat  = mysqli_fetch_assoc($query_stok);

if (!$data_alat || $data_alat['stok'] <= 0) {
    echo "<script>
            alert('Maaf, stok alat ini sudah habis atau tidak ditemukan!');
            window.location.href = '../daftar_alat.php';
          </script>";
    exit();
}

$nama_alat = $data_alat['nama_alat'];

// PERBAIKAN 2: Gunakan Database Transaction agar aman dari error sinkronisasi
mysqli_begin_transaction($koneksi);

try {
    // 1. Kurangi stok alat
    mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

    // 2. Simpan data ke tabel peminjaman (Default status 'pending' jika belum konfirmasi admin)
    $simpan = "INSERT INTO peminjaman (username, nama_alat, durasi, total_bayar, metode_bayar, status) 
               VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', 'pending')";
    mysqli_query($koneksi, $simpan);

    // Jika kedua query di atas sukses, simpan permanen ke DB
    mysqli_commit($koneksi);

    // Redirect sesuai metode pembayaran
    if ($metode == 'BCA') {
        header("Location: ../instruksi_bca.php?alat=" . urlencode($nama_alat) . "&durasi=$durasi&total=$total&metode=$metode");
    } elseif ($metode == 'GOPAY' || $metode == 'DANA') {
        header("Location: ../instruksi_gopay.php?alat=" . urlencode($nama_alat) . "&durasi=$durasi&total=$total&metode=$metode");
    } else {
        header("Location: ../daftar_alat.php");
    }
    exit();

} catch (Exception $e) {
    // Jika ada yang error, batalkan semua perubahan stok/data
    mysqli_rollback($koneksi);
    echo "<script>
            alert('Gagal memproses pemesanan: " . $e->getMessage() . "');
            window.location.href = '../daftar_alat.php';
          </script>";
    exit();
}
?>