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
    $id_alat   = $_POST['id_alat'];
    $nama_alat = $_POST['nama_alat'] ?? 'Alat Pertanian';
    $durasi    = $_POST['durasi'];
    $total     = $_POST['total'];
    $metode    = $_POST['metode'];
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // 1. DETEKSI KOLOM: Ambil semua nama kolom asli dari tabel peminjaman kamu
    $columns = [];
    $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
    while ($field = mysqli_fetch_assoc($result_fields)) {
        $columns[] = strtolower($field['Field']);
    }

    // 2. PEMETAAN KOLOM SECARA OTOMATIS (ANTI ERROR UNKNOWN COLUMN)
    $insert_data = [];

    // Cek kolom Username / Nama Pengguna
    if (in_array('username', $columns)) $insert_data['username'] = "'$username'";
    elseif (in_array('nama', $columns)) $insert_data['nama'] = "'$username'";

    // Cek kolom Alat (Penyebab Error ke-24)
    if (in_array('nama_alat', $columns)) $insert_data['nama_alat'] = "'$nama_alat'";
    elseif (in_array('id_alat', $columns)) $insert_data['id_alat'] = "'$id_alat'";
    elseif (in_array('alat', $columns)) $insert_data['alat'] = "'$nama_alat'";
    elseif (in_array('alat_id', $columns)) $insert_data['alat_id'] = "'$id_alat'";

    // Cek kolom Durasi
    if (in_array('durasi', $columns)) $insert_data['durasi'] = "'$durasi'";

    // Cek kolom Total Harga
    if (in_array('total', $columns)) $insert_data['total'] = "'$total'";
    elseif (in_array('total_harga', $columns)) $insert_data['total_harga'] = "'$total'";

    // Cek kolom Metode
    if (in_array('metode', $columns)) $insert_data['metode'] = "'$metode'";

    // Cek kolom Status
    if (in_array('status', $columns)) $insert_data['status'] = "'$status'";

    // Cek kolom Tanggal
    if (in_array('tanggal', $columns)) $insert_data['tanggal'] = "'$tanggal'";
    elseif (in_array('tgl_pinjam', $columns)) $insert_data['tgl_pinjam'] = "'$tanggal'";

    // 3. SUSUN QUERY SECARA OTOMATIS BERDASARKAN HASIL DETEKSI
    $nama_kolom = implode(', ', array_keys($insert_data));
    $nilai_kolom = implode(', ', array_values($insert_data));

    $query = "INSERT INTO peminjaman ($nama_kolom) VALUES ($nilai_kolom)";

    // 4. EKSEKUSI KE DATABASE
    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok alat secara otomatis
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

        echo "<script>
            alert('Pemesanan Berhasil!');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "Gagal menyimpan pesanan. Pesan Error MySQL: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>