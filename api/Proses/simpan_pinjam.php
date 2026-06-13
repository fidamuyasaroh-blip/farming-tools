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
    // Menangkap data dari form pembayaran.php
    $id_alat   = $_POST['id_alat'] ?? 0;
    $nama_alat = $_POST['nama_alat'] ?? 'Alat Pertanian'; 
    $durasi    = $_POST['durasi'] ?? 1;
    $total     = $_POST['total'] ?? 0;
    $metode    = $_POST['metode'] ?? 'BCA';
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // 1. Ambil semua nama kolom asli yang ada di tabel 'peminjaman' kamu
    $kolom_asli = [];
    $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
    while ($field = mysqli_fetch_assoc($result_fields)) {
        $kolom_asli[] = strtolower($field['Field']);
    }

    // 2. Deteksi otomatis nama kolom untuk ALAT (alat ATAU nama_alat)
    $kolom_alat = 'nama_alat'; // default
    if (in_array('alat', $kolom_asli)) {
        $kolom_alat = 'alat';
    }

    // 3. Deteksi otomatis nama kolom untuk HARGA (total, total_harga, ATAU harga)
    $kolom_harga = 'total'; // default awal
    if (in_array('total_harga', $kolom_asli)) {
        $kolom_harga = 'total_harga';
    } elseif (in_array('harga', $kolom_asli)) {
        $kolom_harga = 'harga';
    }

    // 4. Susun query dinamis berdasarkan kolom yang beneran ada di phpMyAdmin kamu
    $query = "INSERT INTO peminjaman (username, $kolom_alat, durasi, $kolom_harga, metode, status, tanggal) 
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
        // Jika masih gagal (kemungkinan tipe data atau alasan lain), cetak debug rapi
        $error_msg = mysqli_error($koneksi);
        echo "<div style='font-family: sans-serif; padding: 20px; background: #fff5f5; border: 1px solid #ffc9c9; border-radius: 8px; margin: 20px;'>";
        echo "<h3 style='color: #c53030;'>❌ Masih Terjadi Kendala Database</h3>";
        echo "<p><strong>Pesan Eror:</strong> <code>$error_msg</code></p>";
        echo "<p><strong>Kolom yang terdeteksi di tabelmu:</strong> " . implode(', ', $kolom_asli) . "</p>";
        echo "</div>";
        exit();
    }

} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>