<?php
require_once __DIR__ . '/../koneksi.php';

// Proteksi: hanya admin yang boleh hapus user
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

header("Location: ../kelola_user.php");
exit();
?>
