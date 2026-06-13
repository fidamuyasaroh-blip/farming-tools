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

    // =========================================================================
    // PERBAIKAN: Sesuaikan nama-nama kolom di dalam kurung pertama ( ... ) 
    // dengan struktur nama kolom yang ada di tabel 'peminjaman' phpMyAdmin kamu!
    // =========================================================================
    
    // CONTOH JIKA KOLOM KAMU BERNAMA: id_barang, tgl, total, dll.
    // Silakan ganti kata 'id_alat' di bawah ini dengan nama kolom aslimu (misal: id_barang)
    $query = "INSERT INTO peminjaman (username, id_alat, tgl_pinjam, durasi, total_harga, metode, status) 
              VALUES ('$username', '$id_alat', '$tanggal', '$durasi', '$total', '$metode', '$status')";

    if (mysqli_query($koneksi, $query)) {
        // Sesuaikan juga nama kolom 'id' di tabel alat jika bukan 'id'
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