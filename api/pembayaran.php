<?php
// Jalur koneksi aman untuk Vercel & Localhost
include 'koneksi.php';

// Proteksi Cookie status login
$username = $_COOKIE['username'] ?? null;

if (!$username) {
    echo "<script>
        alert('Sesi habis, silakan login kembali.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

// 1. TANGKAP DATA AWAL DARI KATALOG / FORM SEBELUMNYA
$id_alat = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : 0);
$durasi  = isset($_POST['hari']) ? $_POST['hari'] : (isset($_POST['lama_sewa']) ? $_POST['lama_sewa'] : 1);

// Ambil data alat dari database berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM alat WHERE id = '$id_alat'");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: daftar_alat.php");
    exit();
}

$alat = $data['nama_alat'];
$harga_per_hari = $data['harga'];
$total_bayar = $durasi * $harga_per_hari;


// =========================================================================
// LOGIKA UTAMA: PROSES SIMPAN LANGSUNG DI TEMPAT SAAT TOMBOL DIKLIK
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_konfirmasi'])) {
    
    // Ambil data pasti dari input hidden di bawah
    $id_alat_final   = $_POST['id_alat'];
    $nama_alat_final = $_POST['nama_alat']; 
    $durasi_final    = $_POST['durasi'];
    $total_final     = $_POST['total']; 
    $metode_final    = $_POST['metode'] ?? 'Transfer BCA';
    $tanggal_final   = date('Y-m-d');
    $status_final    = 'belum lunas';

    // Cek kolom yang tersedia di tabel database kamu
    $kolom_db = [];
    $result_fields = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
    while ($field = mysqli_fetch_assoc($result_fields)) {
        $kolom_db[] = strtolower($field['Field']);
    }

    $insert_data = [];
    if (in_array('username', $kolom_db))   $insert_data['username'] = "'$username'";
    
    // Sinkronisasi kolom Nama Alat
    if (in_array('nama_alat', $kolom_db)) {
        $insert_data['nama_alat'] = "'$nama_alat_final'";
    } elseif (in_array('alat', $kolom_db)) {
        $insert_data['alat'] = "'$nama_alat_final'";
    }

    if (in_array('durasi', $kolom_db))     $insert_data['durasi'] = "'$durasi_final'";

    // Sinkronisasi kolom Harga
    if (in_array('total_harga', $kolom_db)) {
        $insert_data['total_harga'] = "'$total_final'";
    } elseif (in_array('total', $kolom_db)) {
        $insert_data['total'] = "'$total_final'";
    } elseif (in_array('harga', $kolom_db)) {
        $insert_data['harga'] = "'$total_final'";
    }

    if (in_array('metode', $kolom_db))     $insert_data['metode'] = "'$metode_final'";
    if (in_array('status', $kolom_db))     $insert_data['status'] = "'$status_final'";
    if (in_array('tanggal', $kolom_db))    $insert_data['tanggal'] = "'$tanggal_final'";

    $nama_nama_kolom = implode(", ", array_keys($insert_data));
    $nilai_nilai_kolom = implode(", ", array_values($insert_data));

    $query_insert = "INSERT INTO peminjaman ($nama_nama_kolom) VALUES ($nilai_nilai_kolom)";

    if (mysqli_query($koneksi, $query_insert)) {
        // Potong stok alat
        if ($id_alat_final > 0) {
            mysqli_query($koneksi, "UPDATE alat SET stok = stok - 1 WHERE id = '$id_alat_final'");
        }
        
        echo "<script>
            alert('Pemesanan Berhasil Disimpan!');
            window.location.href = 'riwayat_pemesanan.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding-top: 50px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .payment-card { max-width: 550px; margin: auto; border-radius: 20px; border: none; }
        .method-box { border: 2px solid #eee; border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; }
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .method-box { border-color: #2e7d32; background: #f1f8e9; box-shadow: 0 4px 10px rgba(46, 125, 50, 0.1); }
    </style>
</head>
<body>

<div class="container mb-5">
    <div class="card payment-card shadow p-4">
        <h3 class="fw-bold mb-4 text-center">Detail Pembayaran</h3>
        
        <div class="bg-light p-3 rounded-3 mb-4 border">
            <div class="d-flex justify-content-between mb-2">
                <span>Alat: <strong><?= htmlspecialchars($alat); ?></strong></span>
                <span><?= $durasi; ?> Hari</span>
            </div>
            <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                <span>Total Tagihan:</span>
                <span class="text-success">Rp <?= number_format($total_bayar, 0, ',', '.'); ?></span>
            </div>
        </div>

        <form action="" method="POST">
            
            <input type="hidden" name="proses_konfirmasi" value="1">

            <input type="hidden" name="id_alat" value="<?= $id_alat; ?>">
            <input type="hidden" name="nama_alat" value="<?= htmlspecialchars($alat); ?>"> 
            <input type="hidden" name="durasi" value="<?= $durasi; ?>">
            <input type="hidden" name="total" value="<?= $total_bayar; ?>">

            <h5 class="mb-3 fw-semibold">Pilih Metode Pembayaran</h5>

            <label class="w-100 mb-2">
                <input type="radio" name="metode" value="Transfer BCA" checked>
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" width="60" class="me-3">
                    <div>
                        <strong>Transfer BCA</strong><br>
                        <small class="text-muted">8830-1234-567 a/n TERRALEASE</small>
                    </div>
                </div>
            </label>

            <label class="w-100 mb-2">
                <input type="radio" name="metode" value="Transfer Mandiri">
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" width="60" class="me-3">
                    <div>
                        <strong>Transfer Mandiri</strong><br>
                        <small class="text-muted">131-0012-3456 a/n TERRALEASE</small>
                    </div>
                </div>
            </label>

            <label class="w-100 mb-3">
                <input type="radio" name="metode" value="Gopay">
                <div class="method-box">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" width="60" class="me-3">
                    <div>
                        <strong>GOPAY / QRIS</strong><br>
                        <small class="text-muted">0812-3456-7890 a/n TERRALEASE</small>
                    </div>
                </div>
            </label>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-success py-3 fw-bold">Konfirmasi & Bayar Sekarang</button>
                <a href="daftar_alat.php" class="btn btn-light py-2 border">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>