<?php
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$user = '47DN5h88YR2vG6n.root';
$pass = 'nNViwj5m2tSckbTK';
$db   = 'si_tani';
$port = '4000';

$koneksi = mysqli_init();
mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

$status = mysqli_real_connect(
    $koneksi, $host, $user, $pass, $db, (int)$port, NULL, MYSQLI_CLIENT_SSL
);

if (!$status) {
    error_log("Koneksi Gagal: " . mysqli_connect_error());
    die(json_encode(['error' => 'Gagal tersambung ke database.']));
}

mysqli_set_charset($koneksi, 'utf8mb4');
?>
