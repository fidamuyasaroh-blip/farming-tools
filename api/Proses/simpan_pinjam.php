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

    // =========================================================================
    // PERBAIKAN UTAMA: Mengubah 'total' menjadi 'total_harga' sesuai struktur databasemu
    // =========================================================================
    $query = "INSERT INTO peminjaman (username, nama_alat, durasi, total_harga, metode, status, tanggal) 
              VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";

    // Jalankan query, jika gagal kita buat fallback otomatis ke kolom 'total' biasa
    if (!mysqli_query($koneksi, $query)) {
        
        // Jika masih eror total_harga, kita coba masukkan ke kolom 'total' biasa
        $query_fallback = "INSERT INTO peminjaman (username, nama_alat, durasi, total, metode, status, tanggal) 
                           VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";
        
        if (!mysqli_query($koneksi, $query_fallback)) {
            // Jika kedua cara di atas gagal, cetak bocoran struktur kolom aslimu ke layar
            $error_msg = mysqli_error($koneksi);
            $kolom_asli = [];
            $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
            while ($field = mysqli_fetch_assoc($result_fields)) {
                $kolom_asli[] = $field['Field'];
            }

            echo "<div style='font-family: sans-serif; padding: 20px; background: #fff5f5; border: 1px solid #ffc9c9; border-radius: 8px; margin: 20px;'>";
            echo "<h3 style='color: #c53030;'>❌ Gagal Menyimpan ke Database!</h3>";
            echo "<p><strong>Pesan Eror MySQL:</strong> <code>$error_msg</code></p>";
            echo "<p><strong>Nama kolom asli di tabel <code>peminjaman</code> kamu:</strong></p><ul>";
            foreach ($kolom_asli as $k) { echo "<li><code>$k</code></li>"; }
            echo "</ul></div>";
            exit();
        }
    }

    // Jika berhasil disimpan, kurangi stok alat dan pindah ke halaman riwayat
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