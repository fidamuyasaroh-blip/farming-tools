<?php
require_once __DIR__ . '/koneksi.php';

if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$username = htmlspecialchars($_COOKIE['username'] ?? 'Admin');

// === FILTER PERIODE ===
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

$stmt = mysqli_prepare($koneksi, "SELECT * FROM peminjaman WHERE MONTH(tanggal)=? AND YEAR(tanggal)=? ORDER BY tanggal DESC");
mysqli_stmt_bind_param($stmt, 'ii', $bulan, $tahun);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Normalisasi baris (skema kolom tidak konsisten: nama_alat/alat, total_bayar/total, metode_bayar/metode)
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = [
        'id'      => (int)$r['id'],
        'tanggal' => $r['tanggal'] ?? null,
        'username'=> $r['username'] ?? '-',
        'alat'    => $r['nama_alat'] ?? $r['alat'] ?? '-',
        'durasi'  => (int)($r['durasi'] ?? 0),
        'total'   => (float)($r['total_bayar'] ?? $r['total'] ?? 0),
        'metode'  => $r['metode_bayar'] ?? $r['metode'] ?? '-',
        'status'  => strtolower($r['status'] ?? ''),
    ];
}

$total_transaksi = count($rows);
$total_lunas      = 0;
$total_pending    = 0;
$total_pendapatan = 0;

$harian   = []; // hari => [jumlah, pendapatan]
$metode   = []; // metode => jumlah
$alatpop  = []; // alat => jumlah

foreach ($rows as $r) {
    if ($r['status'] === 'lunas') {
        $total_lunas++;
        $total_pendapatan += $r['total'];
    } else {
        $total_pending++;
    }

    if ($r['tanggal']) {
        $h = (int)date('j', strtotime($r['tanggal']));
        if (!isset($harian[$h])) $harian[$h] = ['jumlah' => 0, 'pendapatan' => 0];
        $harian[$h]['jumlah']++;
        if ($r['status'] === 'lunas') $harian[$h]['pendapatan'] += $r['total'];
    }

    $m = $r['metode'] ?: '-';
    $metode[$m] = ($metode[$m] ?? 0) + 1;

    $a = $r['alat'] ?: '-';
    $alatpop[$a] = ($alatpop[$a] ?? 0) + 1;
}

ksort($harian);
arsort($alatpop);
$alatpop = array_slice($alatpop, 0, 5, true);

$chart_labels     = array_map(fn($d) => "Tgl $d", array_keys($harian));
$chart_transaksi  = array_map(fn($v) => $v['jumlah'], array_values($harian));
$chart_pendapatan = array_map(fn($v) => $v['pendapatan'], array_values($harian));

$metode_labels = array_keys($metode);
$metode_data   = array_values($metode);

$alat_labels = array_keys($alatpop);
$alat_data   = array_values($alatpop);

$bulan_nama = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
    7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body { background-color: #f1f3f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-bottom: 60px; }
        .sidebar { height: 100vh; background: #212529; color: white; padding-top: 20px; position: fixed; width: 220px; }
        .main-content { margin-left: 220px; padding: 40px; }
        .card-custom { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .stat-card { border: none; border-radius: 15px; padding: 22px 26px; }
        .section-title { font-size: .95rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; }

        @media print {
            .no-print { display: none !important; }
            .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background: white !important; font-size: 12px; }
            .card-custom, .stat-card { box-shadow: none !important; border: 1px solid #ccc !important; }
            canvas { max-height: 220px !important; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3 no-print">
    <h5 class="text-center fw-bold mb-4 text-success">TERRALEASE</h5>
    <hr class="border-secondary">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2"><a href="admin_dashboard.php" class="nav-link text-white">Dashboard</a></li>
        <li class="nav-item mb-2"><a href="kelola_alat.php" class="nav-link text-white">Kelola Alat</a></li>
        <li class="nav-item mb-2"><a href="kelola_user.php" class="nav-link text-white">Kelola User</a></li>
        <li class="nav-item mb-2"><a href="laporan.php" class="nav-link bg-success text-white fw-bold">Laporan</a></li>
        <li class="nav-item mb-2"><a href="daftar_alat.php" class="nav-link text-white">Lihat Katalog</a></li>
        <li class="nav-item mb-2"><a href="Proses/logout.php" class="nav-link text-danger">Logout</a></li>
    </ul>
    <hr class="border-secondary">
    <div class="text-center text-muted small">Admin: <strong><?= $username ?></strong></div>
</div>

<div class="main-content">

    <div class="text-center mb-4 d-none d-print-block">
        <h3 class="fw-bold">LAPORAN TRANSAKSI TERRALEASE</h3>
        <p class="text-muted">Periode: <?= $bulan_nama[$bulan] ?? $bulan ?> <?= $tahun ?></p>
        <hr>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <h2 class="fw-bold text-dark mb-0">📊 Laporan & Grafik Transaksi</h2>
            <p class="text-muted mb-0">Periode: <?= $bulan_nama[$bulan] ?? $bulan ?> <?= $tahun ?></p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="bulan" class="form-select form-select-sm" style="width:130px;">
                    <?php foreach ($bulan_nama as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $bulan==$k?'selected':'' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tahun" class="form-select form-select-sm" style="width:90px;">
                    <?php for ($y = (int)date('Y'); $y >= 2023; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun==$y?'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button class="btn btn-success btn-sm fw-bold px-3">Filter</button>
            </form>
            <button onclick="window.print()" class="btn btn-dark btn-sm fw-bold px-3">🖨️ Cetak / PDF</button>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm" style="background:linear-gradient(135deg,#2e7d32,#66bb6a);">
                <div class="text-white">
                    <div style="font-size:1.8rem;">📋</div>
                    <div class="fw-bold fs-3 mt-1"><?= $total_transaksi ?></div>
                    <div style="font-size:.82rem;opacity:.85;">Total Transaksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm" style="background:linear-gradient(135deg,#1565c0,#64b5f6);">
                <div class="text-white">
                    <div style="font-size:1.8rem;">💰</div>
                    <div class="fw-bold mt-1" style="font-size:1.2rem;">Rp <?= number_format($total_pendapatan,0,',','.') ?></div>
                    <div style="font-size:.82rem;opacity:.85;">Pendapatan (Lunas)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm" style="background:linear-gradient(135deg,#2e7d32,#aed581);">
                <div class="text-white">
                    <div style="font-size:1.8rem;">✅</div>
                    <div class="fw-bold fs-3 mt-1"><?= $total_lunas ?></div>
                    <div style="font-size:.82rem;opacity:.85;">Lunas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card shadow-sm" style="background:linear-gradient(135deg,#e65100,#ffb74d);">
                <div class="text-white">
                    <div style="font-size:1.8rem;">⏳</div>
                    <div class="fw-bold fs-3 mt-1"><?= $total_pending ?></div>
                    <div style="font-size:.82rem;opacity:.85;">Belum Lunas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFIK ROW 1 -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card-custom p-4 h-100">
                <div class="section-title mb-3">Grafik Pendapatan Harian</div>
                <canvas id="chartHarian" height="100"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom p-4 h-100">
                <div class="section-title mb-3">Metode Pembayaran</div>
                <canvas id="chartMetode" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- GRAFIK ROW 2 -->
    <div class="row g-3 mb-4">
        <div class="col-md-7">
            <div class="card-custom p-4">
                <div class="section-title mb-3">Alat Paling Sering Disewa</div>
                <canvas id="chartAlat" height="100"></canvas>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card-custom p-4">
                <div class="section-title mb-3">Ringkasan Periode</div>
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td class="text-muted">Periode</td><td class="fw-bold"><?= $bulan_nama[$bulan] ?? $bulan ?> <?= $tahun ?></td></tr>
                        <tr><td class="text-muted">Total Transaksi</td><td class="fw-bold"><?= $total_transaksi ?></td></tr>
                        <tr><td class="text-muted">Lunas</td><td class="fw-bold text-success"><?= $total_lunas ?></td></tr>
                        <tr><td class="text-muted">Belum Lunas</td><td class="fw-bold text-warning"><?= $total_pending ?></td></tr>
                        <tr><td class="text-muted">Total Pendapatan</td><td class="fw-bold text-success">Rp <?= number_format($total_pendapatan,0,',','.') ?></td></tr>
                        <?php if ($total_lunas > 0): ?>
                        <tr><td class="text-muted">Rata-rata/Transaksi</td><td class="fw-bold">Rp <?= number_format($total_pendapatan/$total_lunas,0,',','.') ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TABEL DETAIL -->
    <div class="page-break">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title">Detail Transaksi</div>
                <small class="text-muted no-print">Total: <?= $total_transaksi ?> transaksi</small>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th><th>Tanggal</th><th>User</th><th>Alat</th>
                            <th>Durasi</th><th>Total</th><th>Metode</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_transaksi > 0): $no=1; foreach ($rows as $r): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="small text-muted"><?= $r['tanggal'] ? date('d M Y, H:i', strtotime($r['tanggal'])) : '-' ?></td>
                            <td><?= htmlspecialchars($r['username']) ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($r['alat']) ?></td>
                            <td><?= $r['durasi'] ?> hari</td>
                            <td class="text-success fw-bold">Rp <?= number_format($r['total'],0,',','.') ?></td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($r['metode']) ?></span></td>
                            <td>
                                <?php if ($r['status'] === 'lunas'): ?>
                                    <span class="badge bg-success">Lunas</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if ($total_transaksi > 0): ?>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Total Pendapatan (Lunas):</td>
                            <td class="text-success">Rp <?= number_format($total_pendapatan,0,',','.') ?></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="text-muted small text-end mt-3 no-print">
        Dicetak oleh: <?= $username ?> | <?= date('d M Y H:i') ?>
    </div>
</div>

<script>
const hariLabels     = <?= json_encode($chart_labels) ?>;
const hariPendapatan = <?= json_encode(array_map('floatval', $chart_pendapatan)) ?>;
const hariTransaksi  = <?= json_encode(array_map('intval', $chart_transaksi)) ?>;
const metodeLabels   = <?= json_encode($metode_labels) ?>;
const metodeData     = <?= json_encode(array_map('intval', $metode_data)) ?>;
const alatLabels     = <?= json_encode($alat_labels) ?>;
const alatData       = <?= json_encode(array_map('intval', $alat_data)) ?>;

const pallete = ['#2e7d32','#1565c0','#e65100','#6a1b9a','#00838f'];

new Chart(document.getElementById('chartHarian'), {
    data: {
        labels: hariLabels.length ? hariLabels : ['Tidak ada data'],
        datasets: [
            { type:'bar', label:'Pendapatan (Rp)', data: hariPendapatan.length?hariPendapatan:[0],
              backgroundColor:'rgba(46,125,50,0.7)', borderColor:'#2e7d32', borderWidth:1, yAxisID:'y' },
            { type:'line', label:'Jumlah Transaksi', data: hariTransaksi.length?hariTransaksi:[0],
              borderColor:'#1565c0', backgroundColor:'rgba(21,101,192,0.1)', tension:0.4, pointRadius:4, yAxisID:'y2' }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode:'index', intersect:false },
        plugins: {
            legend: { position:'top' },
            tooltip: { callbacks: { label: ctx => ctx.datasetIndex===0
                ? ' Rp '+ctx.raw.toLocaleString('id-ID') : ' '+ctx.raw+' transaksi' } }
        },
        scales: {
            y:  { type:'linear', position:'left',  ticks:{ callback: v => 'Rp '+v.toLocaleString('id-ID') } },
            y2: { type:'linear', position:'right', grid:{ drawOnChartArea:false }, ticks:{ stepSize:1 } }
        }
    }
});

new Chart(document.getElementById('chartMetode'), {
    type: 'doughnut',
    data: {
        labels: metodeLabels.length ? metodeLabels : ['Belum ada data'],
        datasets: [{ data: metodeData.length?metodeData:[1], backgroundColor: pallete, borderWidth: 2 }]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom' } } }
});

new Chart(document.getElementById('chartAlat'), {
    type: 'bar',
    data: {
        labels: alatLabels.length ? alatLabels : ['Belum ada data'],
        datasets: [{ label:'Jumlah Sewa', data: alatData.length?alatData:[0], backgroundColor: pallete, borderRadius: 6 }]
    },
    options: { indexAxis:'y', responsive:true, plugins:{ legend:{ display:false } }, scales:{ x:{ ticks:{ stepSize:1 } } } }
});
</script>

</body>
</html>