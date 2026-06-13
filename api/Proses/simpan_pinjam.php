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

    // 1. DETEKSI OTOMATIS: Ambil semua nama kolom yang ada di tabel peminjaman kamu
    $columns = [];
    $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
    while ($field = mysqli_fetch_assoc($result_fields)) {
        $columns[] = strtolower($field['Field']);
    }

    // 2. PEMETAAN CERDAS: Tentukan kolom mana saja yang cocok dengan data kita
    $insert_data = [];

    // Cek kolom untuk Username
    if (in_array('username', $columns)) $insert_data['username'] = "'$username'";
    elseif (in_array('user_id', $columns)) $insert_data['user_id'] = "'$username'";

    // Cek kolom untuk ID Alat / Barang (Penyebab error kamu sebelumnya)
    if (in_array('id_alat', $columns)) $insert_data['id_alat'] = "'$id_alat'";
    elseif (in_array('id_barang', $columns)) $insert_data['id_barang'] = "'$id_alat'";
    elseif (in_array('alat_id', $columns)) $insert_data['alat_id'] = "'$id_alat'";
    elseif (in_array('barang_id', $columns)) $insert_data['barang_id'] = "'$id_alat'";
    elseif (in_array('id', $columns) && !isset($insert_data['username'])) $insert_data['id'] = "'$id_alat'"; 

    // Cek kolom untuk Tanggal
    if (in_array('tgl_pinjam', $columns)) $insert_data['tgl_pinjam'] = "'$tanggal'";
    elseif (in_array('tgl_sewa', $columns)) $insert_data['tgl_sewa'] = "'$tanggal'";
    elseif (in_array('tanggal', $columns)) $insert_data['tanggal'] = "'$tanggal'";

    // Cek kolom untuk Durasi / Hari
    if (in_array('durasi', $columns)) $insert_data['durasi'] = "'$durasi'";
    elseif (in_array('lama_sewa', $columns)) $insert_data['lama_sewa'] = "'$durasi'";
    elseif (in_array('hari', $columns)) $insert_data['hari'] = "'$durasi'";

    // Cek kolom untuk Total Harga
    if (in_array('total_harga', $columns)) $insert_data['total_harga'] = "'$total'";
    elseif (in_array('total', $columns)) $insert_data['total'] = "'$total'";
    elseif (in_array('harga', $columns)) $insert_data['harga'] = "'$total'";

    // Cek kolom untuk Metode Pembayaran
    if (in_array('metode', $columns)) $insert_data['metode'] = "'$metode'";
    elseif (in_array('metode_pembayaran', $columns)) $insert_data['metode_pembayaran'] = "'$metode'";

    // Cek kolom untuk Status
    if (in_array('status', $columns)) $insert_data['status'] = "'$status'";

    // 3. SELESAIKAN QUERY SECARA OTOMATIS
    $nama_kolom = implode(', ', array_keys($insert_data));
    $nilai_kolom = implode(', ', array_values($insert_data));

    $query = "INSERT INTO peminjaman ($nama_kolom) VALUES ($nilai_kolom)";

    // 4. EKSEKUSI KE DATABASE
    if (mysqli_query($koneksi, $query)) {
        // Kurangi stok alat secara otomatis
        mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat'");

        echo "<script>
            alert('Pemesanan Berhasil! Silakan cek riwayat pemesanan Anda.');
            window.location.href = '../riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "<h4>Gagal Menyimpan. Nama-nama kolom di tabel database Anda saat ini adalah:</h4>";
        echo "<pre>"; print_rows_fields($columns); echo "</pre>";
        echo "Pesan Error MySQL: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../daftar_alat.php");
    exit();
}

function print_rows_fields($fields) {
    foreach($fields as $f) {
        echo "- " . $f . "\n";
    }
}
?>