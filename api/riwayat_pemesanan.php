<?php
session_start();
// Hubungkan ke file koneksi database Anda
require_once __DIR__ . '/../koneksi.php';

// Proteksi halaman: Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan & Laporan - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Gaya dasar untuk tampilan Admin di Browser */
        body {
            background-color: #f4f6f9;
        }
        .wrapper {
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #22c55e; /* Hijau Agrikultur */
            color: #fff;
            position: fixed;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: #16a34a;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }
        .card-laporan {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        /* ==========================================================================
           STYLE KHUSUS CETAK (Akan aktif secara otomatis saat tombol cetak diklik)
           ========================================================================== */
        @media print {
            /* Sembunyikan elemen navigasi, tombol, sidebar yang tidak perlu dicetak */
            .no-print, .sidebar, .navbar, .btn, header, footer, .main-content-header {
                display: none !important;
            }
            
            /* Atur ulang margin halaman utama agar pas di kertas A4 / Letter */
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            
            body {
                background: #fff !important;
                color: #000 !important;
                padding: 10px;
            }
            
            .card-laporan {
                box-shadow: none !important;
                padding: 0 !important;
                border: none !important;
            }

            /* Desain header surat/laporan formal saat dicetak */
            .kop-surat {
                display: block !important;
                text-align: center;
                margin-bottom: 25px;
                border-bottom: 3px double #000;
                padding-bottom: 10px;
            }

            /* Memaksa border tabel tetap terlihat hitam pekat saat dicetak */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 15px;
            }
            table th, table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                color: #000 !important;
            }
            table th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Sembunyikan KOP Surat di tampilan browser biasa */
        .kop-surat {
            display: none;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h4>TERRALEASE ADMIN</h4>
        <hr>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
        <a href="kelola_user.php"><i class="fa fa-users"></i> Kelola User</a>
        <a href="riwayat_pemesanan.php" class="active"><i class="fa fa-history"></i> Riwayat Pemesanan</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Keluar</a>
    </div>

    <div class="main-content">
        
        <div class="main-content-header d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Transaksi</h2>
            <span>Halo, <strong><?php echo $_SESSION['username']; ?></strong></span>
        </div>

        <div class="card-laporan">
            
            <div class="kop-surat">
                <h2>TERRALEASE - SMART FARMING & RENTAL ALAT TANI</h2>
                <p style="margin: 0; font-size: 14px;">Laporan Data Transaksi Penyewaan Alat Pertanian</p>
                <p style="margin: 0; font-size: 12px; color: #555;">Tanggal Cetak: <?php echo date('d F Y'); ?></p>
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center no-print">
                <h5 class="m-0 text-muted">Daftar Transaksi Masuk</h5>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fa fa-print"></i> Cetak Laporan Transaksi (PDF)
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Penyewa</th>
                            <th>Nama Alat Tani</th>
                            <th width="10%">Durasi</th>
                            <th>Total Bayar</th>
                            <th>Metode</th>
                            <th>Status Pembayaran</th>
                            <th class="no-print" width="15%">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil seluruh data dari tabel peminjaman di MySQL
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM peminjaman ORDER BY id DESC");
                        
                        // Cek apakah ada data di database
                        if (mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($data['username']); ?></td>
                                    <td><?php echo htmlspecialchars($data['nama_alat']); ?></td>
                                    <td><?php echo $data['durasi']; ?> Hari</td>
                                    <td>Rp <?php echo number_format($data['total_bayar'], 0, ',', '.'); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $data['metode_bayar']; ?></span></td>
                                    <td>
                                        <?php if ($data['status'] == 'lunas') : ?>
                                            <span class="badge bg-success p-2">Lunas</span>
                                        <?php else : ?>
                                            <span class="badge bg-warning text-dark p-2">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="no-print">
                                        <?php if ($data['status'] != 'lunas') : ?>
                                            <a href="konfirmasi_bayar.php?id=<?php echo $data['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-check"></i> Konfirmasi
                                            </a>
                                        <?php else : ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled>Selesai</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            // Jika data di database kosong
                            echo "<tr><td colspan='8' class='text-center py-4 text-muted'>Belum ada riwayat transaksi peminjaman.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>