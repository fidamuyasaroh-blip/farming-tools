<?php
require_once __DIR__ . '/../koneksi.php';

// PERBAIKAN: Gunakan Cookie bukan Session
if (!isset($_COOKIE['role']) || $_COOKIE['role'] != 'admin') {
    header("Location: /api/login.php");
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? 0);

if ($id) {
    mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");
}

header("Location: /api/kelola_user.php");
exit();
?>
