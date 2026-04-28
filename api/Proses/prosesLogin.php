<?php
ob_start();
// Tetap sertakan koneksi
require_once __DIR__ . '/../koneksi.php'; 

$username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
$password = mysqli_real_escape_string($koneksi, $_POST['password'] ?? '');

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");

if ($query && mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);

    // MENGGUNAKAN COOKIE (Berlaku selama 1 hari)
    // Pastikan path "/" agar bisa dibaca oleh file di luar folder 'Proses'
    setcookie("user_id", $data['id'], time() + 86400, "/");
    setcookie("username", $data['username'], time() + 86400, "/");
    setcookie("role", $data['role'], time() + 86400, "/");

    // PERBAIKAN: Gunakan path relatif (../) agar lebih stabil di Vercel
    if (strtolower($data['role']) == "admin") {
        header("Location: ../admin_dashboard.php");
    } else {
        header("Location: ../dashboard_user.php"); 
    }
    exit();
} else {
    // PERBAIKAN: Gunakan path relatif ke login.php
    echo "<script>
        alert('Username atau Password Salah!'); 
        window.location.href='../login.php';
    </script>";
}
ob_end_flush();
?>