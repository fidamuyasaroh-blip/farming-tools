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

    // Cari nama alat dari tabel alat sebagai cadangan jika kolomnya bernama 'nama_alat'
    $cari_alat = mysqli_query($koneksi, "SELECT nama_alat FROM alat WHERE id = '$id_alat'");
    $data_alat = mysqli_fetch_assoc($cari_alat);
    $nama_alat = $data_alat['nama_alat'] ?? '';

    // =========================================================================
    // SILAKAN PILIH SALAH SATU QUERY DI BAWAH INI YANG COCOK DENGAN TABELMU:
    // (Buka salah satu tanda komentar '//' di bawah ini yang sesuai)
    // =========================================================================
    
    // Pilihan 1: Jika di phpMyAdmin kolomnya bernama 'id_barang' dan 'total_harga'
    $query = "INSERT INTO peminjaman (username, id_barang, tgl_pinjam, durasi, total_harga, metode, status) 
              VALUES ('$username', '$id_alat', '$tanggal', '$durasi', '$total', '$metode', '$status')";
              
    // Pilihan 2: Jika di phpMyAdmin kolomnya bernama 'nama_alat' (menyimpan teks nama alat)
    // $query = "INSERT INTO peminjaman (username, nama_alat, tgl_pinjam, durasi, total_harga, metode, status) 
    //           VALUES ('$username', '$nama_alat', '$tanggal', '$durasi', '$total', '$metode', '$status')";

    // Pilihan 3: Jika di phpMyAdmin kolomnya bernama 'id_alat' tapi totalnya hanya bernama 'total'
    // $query = "INSERT INTO peminjaman (username, id_alat, tgl_pinjam, durasi, total, metode, status) 
    //           VALUES ('$username', '$id_alat', '$tanggal', '$durasi', '$total', '$metode', '$status')";


    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok alat
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