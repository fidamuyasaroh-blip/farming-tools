<?php
// Masukkan data koneksi langsung ke variabel
$host = 'gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com';
$user = '47DN5h88YR2vG6n.root';
$pass = 'nNViwj5m2tSckbTK';
$db   = 'si_tani';
$port = '4000';

// Koneksi ke database TiDB
$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    error_log("Koneksi Database Gagal: " . mysqli_connect_error());
    die("Maaf, layanan sedang gangguan. Silakan coba lagi nanti.");
}

// Jika berhasil, kamu bisa lanjut coding di sini
?>