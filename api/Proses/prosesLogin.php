<?php
ob_start();
// session_start(); // Kamu bisa hapus atau biarkan ini jika masih butuh session di tempat lain

require_once __DIR__ . '/../koneksi.php'; 

$username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
$password = mysqli_real_escape_string($koneksi, $_POST['password'] ?? '');

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");

if ($query && mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);

    // MENGGUNAKAN COOKIE (Berlaku selama 1 hari)
    // setcookie(nama, nilai, waktu_habis, path)
    setcookie("user_id", $data['id'], time() + 86400, "/");
    setcookie("username", $data['username'], time() + 86400, "/");
    setcookie("role", $data['role'], time() + 86400, "/");

    if (strtolower($data['role']) == "admin") {
        header("Location: /api/admin_dashboard.php");
    } else {
        header("Location: /api/dashboard_user.php"); 
    }
    exit();
} else {
    echo "<script>alert('Username atau Password Salah!'); window.location.href='/api/login.php';</script>";
}
ob_end_flush();
?>