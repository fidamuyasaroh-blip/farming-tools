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
    // Menangkap data asli dari halaman pembayaran.php
    $id_alat   = $_POST['id_alat'] ?? 0;
    $nama_alat = $_POST['nama_alat'] ?? 'Alat Pertanian'; 
    $durasi    = $_POST['durasi'] ?? 1;
    $total     = $_POST['total'] ?? 0;
    $metode    = $_POST['metode'] ?? 'BCA';
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // -------------------------------------------------------------------------
    // COBA QUERY 1: Menggunakan 'nama_alat' (Kemungkinan besar struktur aslimu)
    // -------------------------------------------------------------------------
    $query = "INSERT INTO peminjaman (username, nama_alat, durasi, total, metode, status, tanggal) 
              VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";

    // Jalankan query, jika gagal kita tangkap erornya dan cari nama kolom yang benar
    if (!mysqli_query($koneksi, $query)) {
        
        // Cek apakah eror karena nama kolom salah
        $error_msg = mysqli_error($koneksi);
        
        // Ambil struktur kolom asli dari database untuk diinspeksi
        $kolom_asli = [];
        $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
        while ($field = mysqli_fetch_assoc($result_fields)) {
            $kolom_asli[] = $field['Field'];
        }

        // Tampilkan pesan panduan debug yang rapi di layar
        echo "<div style='font-family: sans-serif; padding: 20px; background: #fff5f5; border: 1px solid #ffc9c9; border-radius: 8px; margin: 20px;'>";
        echo "<h3 style='color: #c53030;'>❌ Gagal Menyimpan ke Database!</h3>";
        echo "<p><strong>Pesan Eror MySQL:</strong> <code>$error_msg</code></p>";
        echo "<p><strong>Nama kolom yang ADA di tabel <code>peminjaman</code> kamu saat ini adalah:</strong></p>";
        echo "<ul>";
        foreach ($kolom_asli as $k) {
            echo "<li><strong><code>$k</code></strong></li>";
        }
        echo "</ul>";
        echo "<p>💡 <em>Silakan sesuaikan nama kolom di dalam query INSERT (baris 24) dengan daftar di atas!</em></p>";
        echo "</div>";
        exit();
    }

    // Jika query 1 berhasil, kurangi stok dan geser ke halaman riwayat
    mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");
    echo "<script>
        alert('Pemesanan Berhasil!');
        window.location.href = '../riwayat_pemesanan.php';
    </script>";
    exit();

} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>