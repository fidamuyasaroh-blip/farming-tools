<?php
session_start();
include '../koneksi.php';

// Proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

if ($id) {
    mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");
}

header("Location: kelola_user.php");
exit();
?>
