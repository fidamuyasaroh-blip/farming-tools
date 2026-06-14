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
    $total     = $_POST['total'] ?? 0; 
    $metode    = $_POST['metode'] ?? 'BCA';
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // =========================================================================
    // STEP 1: DETEKSI OTOMATIS SEMUA KOLOM YANG ADA DI TABEL PEMINJAMAN KAMU
    // =========================================================================
    $kolom_db = [];
    $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
    while ($field = mysqli_fetch_assoc($result_fields)) {
        $kolom_db[] = strtolower($field['Field']);
    }

    // =========================================================================
    // STEP 2: PILIH HANYA DATA YANG KOLOMNYA BENERAN ADA DI DATABASE (ANTI-EROR)
    // =========================================================================
    $insert_data = [];

    // Username pasti ada
    if (in_array('username', $kolom_db)) {
        $insert_data['username'] = "'$username'";
    }

    // Cek kolom Alat (bisa 'nama_alat' atau 'alat')
    if (in_array('nama_alat', $kolom_db)) {
        $insert_data['nama_alat'] = "'$nama_alat'";
    } elseif (in_array('alat', $kolom_db)) {
        $insert_data['alat'] = "'$nama_alat'";
    }

    // Cek kolom Durasi
    if (in_array('durasi', $kolom_db)) {
        $insert_data['durasi'] = "'$durasi'";
    }

    // Cek kolom Harga (bisa 'total_harga', 'total', atau 'harga')
    if (in_array('total_harga', $kolom_db)) {
        $insert_data['total_harga'] = "'$total'";
    } elseif (in_array('total', $kolom_db)) {
        $insert_data['total'] = "'$total'";
    } elseif (in_array('harga', $kolom_db)) {
        $insert_data['harga'] = "'$total'";
    }

    // Cek kolom Metode (Jika tidak ada, diabaikan otomatis!)
    if (in_array('metode', $kolom_db)) {
        $insert_data['metode'] = "'$metode'";
    }

    // Cek kolom Status
    if (in_array('status', $kolom_db)) {
        $insert_data['status'] = "'$status'";
    }

    // Cek kolom Tanggal
    if (in_array('tanggal', $kolom_db)) {
        $insert_data['tanggal'] = "'$tanggal'";
    }

    // =========================================================================
    // STEP 3: SUSUN QUERY DINAMIS BERDASARKAN KOLOM YANG VALID
    // =========================================================================
    $nama_nama_kolom = implode(", ", array_keys($insert_data));
    $nilai_nilai_kolom = implode(", ", array_values($insert_data));

    $query = "INSERT INTO peminjaman ($nama_nama_kolom) VALUES ($nilai_nilai_kolom)";

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
        // Fallback aman jika ada kendala koneksi lain, sekaligus debug field
        $error_msg = mysqli_error($koneksi);
        echo "<div style='font-family: sans-serif; padding: 25px; background: #fff5f5; border: 2px solid #ffc9c9; border-radius: 10px; margin: 30px auto; max-width: 600px;'>";
        echo "<h3 style='color: #c53030; margin-top:0;'>❌ Sistem Database Menolak</h3>";
        echo "<p><strong>Pesan Eror:</strong> <code>$error_msg</code></p>";
        echo "<p><strong>Kolom asli di tabel kamu:</strong> " . implode(', ', $kolom_db) . "</p>";
        echo "</div>";
        exit();
    }

} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>