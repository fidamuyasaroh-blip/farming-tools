<?php
require_once __DIR__ . '/../koneksi.php';

// Proteksi: hanya admin
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "UPDATE peminjaman SET status = 'lunas' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header("Location: ../admin_dashboard.php");
exit();
?>
