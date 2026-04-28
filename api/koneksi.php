<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$user = '47DN5h88YR2vG6n.root';
$pass = 'nNViwj5m2tSckbTK';
$db   = 'si_tani';
$port = '4000';

// 1. Inisialisasi mysqli
$koneksi = mysqli_init();

// 2. Atur agar menggunakan SSL (kunci perbaikan)
// Kita tidak perlu path sertifikat khusus karena Vercel sudah punya root CA standar
mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

// 3. Lakukan koneksi dengan flag MYSQLI_CLIENT_SSL
$status = mysqli_real_connect($koneksi, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$status) {
    error_log("Koneksi Gagal: " . mysqli_connect_error());
    die("Gagal tersambung ke database.");
}
?>