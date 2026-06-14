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
    $total     = $_POST['total'] ?? 0; // Nominal uang dari form
    $metode    = $_POST['metode'] ?? 'BCA';
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // =========================================================================
    // STEP 1: DETEKSI OTOMATIS STRUKTUR KOLOM TABEL KAMU (ANTI-EROR)
    // =========================================================================
    $kolom_asli = [];
    $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
    while ($field = mysqli_fetch_assoc($result_fields)) {
        $kolom_asli[] = strtolower($field['Field']);
    }

    // A. Deteksi nama kolom untuk ALAT (alat ATAU nama_alat)
    $kolom_alat = 'nama_alat'; // default
    if (in_array('alat', $kolom_asli)) {
        $kolom_alat = 'alat';
    }

    // B. Deteksi nama kolom untuk HARGA (total_harga, total, ATAU harga)
    $kolom_harga = '';
    if (in_array('total_harga', $kolom_asli)) {
        $kolom_harga = 'total_harga';
    } elseif (in_array('total', $kolom_asli)) {
        $kolom_harga = 'total';
    } elseif (in_array('harga', $kolom_asli)) {
        $kolom_harga = 'harga';
    } else {
        // Jika tidak ketemu ketiganya, kita ambil kolom ke-5 atau buat fallback aman
        $kolom_harga = $kolom_asli[4] ?? 'harga'; 
    }

    // =========================================================================
    // STEP 2: JALANKAN QUERY BERDASARKAN KOLOM YANG TERDETEKSI
    // =========================================================================
    $query = "INSERT INTO peminjaman (username, $kolom_alat, durasi, $kolom_harga, metode, status, tanggal) 
              VALUES ('$username', '$nama_alat', '$durasi', '$total', '$metode', '$status', '$tanggal')";

    // Jalankan query ke database
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil disimpan, kurangi stok alat secara otomatis
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");
        
        echo "<script>
            alert('Pemesanan Berhasil!');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        // JIKA MASIH GAGAL, KITA CETAK STRUKTUR TABEL KAMU BIAR MAKIN JELAS
        $error_msg = mysqli_error($koneksi);
        echo "<div style='font-family: sans-serif; padding: 25px; background: #fff5f5; border: 2px solid #ffc9c9; border-radius: 10px; margin: 30px auto; max-width: 600px;'>";
        echo "<h3 style='color: #c53030; margin-top:0;'>❌ Oops! Gagal Menyimpan Ke Database</h3>";
        echo "<p><strong>Pesan Eror MySQL:</strong> <code style='background:#edd; padding:2px 6px; border-radius:4px;'>$error_msg</code></p>";
        echo "<p><strong>Kolom yang sebenarnya ada di tabel peminjaman kamu adalah:</strong></p>";
        echo "<ul style='background: #fff; padding: 15px 30px; border: 1px solid #e2e8f0; border-radius:6px;'>";
        foreach ($kolom_asli as $k) {
            echo "<li><strong><code>$k</code></strong></li>";
        }
        echo "</ul>";
        echo "<p style='font-size:14px; color:#555;'>Silakan infokan ke saya daftar kolom di atas jika halaman ini muncul!</p>";
        echo "</div>";
        exit();
    }

} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>