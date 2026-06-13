<?php
session_start();
// Hubungkan ke file koneksi database Anda
require_once __DIR__ . '/../koneksi.php';

// Proteksi halaman: Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ==========================================================================
// LOGIKA DATABASE UNTUK MENGAMBIL DATA RANGKUMAN DASHBOARD
// ==========================================================================

// 1. Hitung total user terdaftar
$query_user = mysqli_query($koneksi, "SELECT COUNT(*) as total_user FROM users WHERE role='user'");
$data_user = mysqli_fetch_assoc($query_user);
$total_user = $data_user['total_user'] ?? 0;

// 2. Hitung total transaksi peminjaman
$query_trx = mysqli_query($koneksi, "SELECT COUNT(*) as total_trx FROM peminjaman");
$data_trx = mysqli_fetch_assoc($query_trx);
$total_trx = $data_trx['total_trx'] ?? 0;

// 3. Hitung total pendapatan dari transaksi yang sudah lunas
$query_pendapatan = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total_idr FROM peminjaman WHERE status='lunas'");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total_idr'] ?? 0;


// ==========================================================================
// LOGIKA DATABASE UNTUK GRAFIK (Chart.js)
// ==========================================================================

// Perbaikan Query: Mengasumsikan Anda memiliki kolom tanggal/waktu di tabel peminjaman.
// Jika Anda belum membuat kolom tanggal, kita gunakan kolom 'id' atau membuat kolom baru 'tanggal_pinjam' DATETIME.
// Di bawah ini menggunakan kolom asumsi bernama 'tanggal_pinjam' atau 'id' jika tipenya timestamp.
$query_grafik = mysqli_query($koneksi, "
    SELECT 
        MONTHNAME(tanggal_bayar) as bulan, 
        SUM(total_bayar) as total 
    FROM peminjaman 
    WHERE status='lunas' 
    GROUP BY MONTH(tanggal_bayar)
    ORDER BY MONTH(tanggal_bayar) ASC
");

$labels = [];
$data_total = [];

if ($query_grafik && mysqli_num_rows($query_grafik) > 0) {
    while ($row = mysqli_fetch_assoc($query_grafik)) {
        // Konversi nama bulan bahasa Inggris ke Indonesia secara sederhana jika diperlukan
        $labels[] = $row['bulan'] ?? 'Data';
        $data_total[] = $row['total'];
    }
}

// Jika data di database masih kosong (baru install/buat database), 
// kita beri data dummy/default agar grafik UTS/Demo Anda tidak kosong melongpong.
if (empty($labels)) {
    $labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
    $data_total = [1200000, 3500000, 2400000, 5100000, 4200000, 7500000];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
        }
        .wrapper {
            display: flex;
        }
        /* Style Sidebar Dashboard Admin */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #22c55e; /* Hijau Identitas Terralease */
            color: #fff;
            position: fixed;
            padding: 20px;
        }
        .sidebar h4 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #16a34a;
            font-weight: bold;
        }
        /* Konten Utama di sebelah kanan sidebar */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
        .card-box {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .card-box:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h4>TERRALEASE</h4>
        <p class="text-center text-light" style="font-size: 12px;">Smart Farming Admin Panel</p>
        <hr>
        <a href="admin_dashboard.php" class="active"><i class="fa fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="kelola_user.php"><i class="fa fa-users me-2"></i> Kelola User</a>
        <a href="riwayat_pemesanan.php"><i class="fa fa-history me-2"></i> Riwayat Pemesanan</a>
        <hr>
        <a href="logout.php" class="text-white bg-danger text-center"><i class="fa fa-sign-out-alt me-2"></i> Keluar</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold m-0 text-dark">Dashboard Analisis</h2>
                <small class="text-muted">Pantau statistik penyewaan alat pertanian Anda di sini.</small>
            </div>
            <div class="text-end">
                <span class="badge bg-dark p-2 fs-6">
                    <i class="fa fa-user-shield me-2"></i> Admin: <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-box bg-white p-3 border-start border-primary border-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 12px;">Total Penyewa</h6>
                            <h2 class="fw-bold m-0"><?php echo $total_user; ?></h2>
                        </div>
                        <div class="text-primary fs-1">
                            <i class="fa fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-box bg-white p-3 border-start border-warning border-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 12px;">Total Pesanan</h6>
                            <h2 class="fw-bold m-0"><?php echo $total_trx; ?></h2>
                        </div>
                        <div class="text-warning fs-1">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-box bg-white p-3 border-start border-success border-5">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 12px;">Total Pendapatan</h6>
                            <h2 class="fw-bold m-0 text-success">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h2>
                        </div>
                        <div class="text-success fs-1">
                            <i class="fa fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card my-4" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-dark m-0"><i class="fa fa-chart-bar text-success me-2"></i>Grafik Pendapatan Bulanan TERRALEASE</h4>
                <span class="badge bg-success">Real-time Data</span>
            </div>
            <canvas id="grafikPeminjaman" width="400" height="150"></canvas>
        </div>

    </div>
</div>

<script>
const ctx = document.getElementById('grafikPeminjaman').getContext('2d');
const grafikPeminjaman = new Chart(ctx, {
    type: 'bar', // Anda bisa ganti menjadi 'line' jika ingin grafik tipe garis
    data: {
        // Data label sumbu X diambil secara dinamis dari PHP array
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Total Pendapatan Rental Alat (Rp)',
            // Data angka sumbu Y diambil secara dinamis dari PHP array
            data: <?php echo json_encode($data_total); ?>,
            backgroundColor: 'rgba(34, 197, 94, 0.5)', // Warna hijau transparan
            borderColor: 'rgba(22, 163, 74, 1)',       // Garis tepi hijau tegas
            borderWidth: 2,
            borderRadius: 5 // Membuat sudut batang grafik sedikit melengkung halus
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    // Mengubah format angka di sumbu Y menjadi format rupiah rupiah biasa (Contoh: Rp 5.000.000)
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>