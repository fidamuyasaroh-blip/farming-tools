<?php
require_once __DIR__ . '/../koneksi.php';

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$role     = 'user';

if (empty($username) || empty($email) || empty($password)) {
    echo "<script>alert('Semua field wajib diisi!'); window.location.href='../register.php';</script>";
    exit();
}

if (strlen($password) < 8) {
    echo "<script>alert('Password harus minimal 8 karakter!'); window.location.href='../register.php';</script>";
    exit();
}

// Cek username sudah dipakai
$stmt_cek = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt_cek, 's', $username);
mysqli_stmt_execute($stmt_cek);
mysqli_stmt_store_result($stmt_cek);

if (mysqli_stmt_num_rows($stmt_cek) > 0) {
    echo "<script>alert('Username sudah digunakan, pilih username lain!'); window.location.href='../register.php';</script>";
    exit();
}

// Hash password sebelum disimpan
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt_ins = mysqli_prepare($koneksi, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt_ins, 'ssss', $username, $email, $hashed, $role);

if (mysqli_stmt_execute($stmt_ins)) {
    echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='../login.php';</script>";
} else {
    echo "<script>alert('Registrasi Gagal, coba lagi.'); window.location.href='../register.php';</script>";
}
?>
