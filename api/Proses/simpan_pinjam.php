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
    
    // =========================================================================
    // DETEKSI INPUT FORMsecara Agresif (Mencegah nama alat jadi default/Rp 0)
    // =========================================================================
    $id_alat   = $_POST['id_alat'] ?? ($_POST['id'] ?? 0);
    
    // Cek kemungkinan nama input untuk ALAT
    $nama_alat = $_POST['nama_alat'] ?? ($_POST['alat'] ?? ($_POST['nama'] ?? 'Alat Pertanian')); 
    
    // Cek kemungkinan nama input untuk DURASI
    $durasi    = $_POST['durasi'] ?? ($_POST['lama_sewa'] ?? 1);
    
    // Cek kemungkinan nama input untuk TOTAL BAYAR
    $total     = $_POST['total'] ?? ($_POST['total_harga'] ?? ($_POST['harga'] ?? ($_POST['biaya'] ?? 0))); 
    
    // Cek kemungkinan nama input untuk METODE PEMBAYARAN
    $metode    = $_POST['metode'] ?? ($_POST['metode_pembayaran'] ?? ($_POST['pembayaran'] ?? 'BCA'));
    
    $tanggal   = date('Y-m-d');
    $status    = 'belum lunas';

    // =========================================================================
    // STEP 1: DETEKSI OTOMATIS STRUKTUR KOLOM TABEL PEMINJAMAN DI DATABASE
    // =========================================================================
    $kolom_db = [];
    $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
    while ($field = mysqli_fetch_assoc($result_fields)) {
        $kolom_db[] = strtolower($field['Field']);
    }

    // =========================================================================
    // STEP 2: PETAKAN DATA HANYA KE KOLOM YANG TERSEDIA DI DATABASE
    // =========================================================================
    $insert_data = [];

    if (in_array('username', $kolom_db)) {
        $insert_data['username'] = "'$username'";
    }

    // Isikan ke kolom alat yang tepat
    if (in_array('nama_alat', $kolom_db)) {
        $insert_data['nama_alat'] = "'$nama_alat'";
    } elseif (in_array('alat', $kolom_db)) {
        $insert_data['alat'] = "'$nama_alat'";
    }

    if (in_array('durasi', $kolom_db)) {
        $insert_data['durasi'] = "'$durasi'";
    }

    // Isikan ke kolom keuangan yang tepat
    if (in_array('total_harga', $kolom_db)) {
        $insert_data['total_harga'] = "'$total'";
    } elseif (in_array('total', $kolom_db)) {
        $insert_data['total'] = "'$total'";
    } elseif (in_array('harga', $kolom_db)) {
        $insert_data['harga'] = "'$total'";
    }

    if (in_array('metode', $kolom_db)) {
        $insert_data['metode'] = "'$metode'";
    }

    if (in_array('status', $kolom_db)) {
        $insert_data['status'] = "'$status'";
    }

    if (in_array('tanggal', $kolom_db)) {
        $insert_data['tanggal'] = "'$tanggal'";
    }

    // =========================================================================
    // STEP 3: EKSEKUSI INSERT
    // =========================================================================
    $nama_nama_kolom = implode(", ", array_keys($insert_data));
    $nilai_nilai_kolom = implode(", ", array_values($insert_data));

    $query = "INSERT INTO peminjaman ($nama_nama_kolom) VALUES ($nilai_nilai_kolom)";

    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok alat jika kolom id_alat valid
        if ($id_alat > 0) {
            mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");
        }
        
        echo "<script>
            alert('Pemesanan Berhasil Disimpan!');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "Gagal menyimpan: " . mysqli_error($koneksi);
    }

} else {
    header("Location: ../daftar_alat.php");
    exit();
}
?>