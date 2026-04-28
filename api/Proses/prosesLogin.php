<?php
session_start();

// PERBAIKAN 1: Naik satu folder (../) karena koneksi.php ada di luar folder Proses
require_once __DIR__ . '/../koneksi.php'; 

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Hindari SQL Injection (Tambahkan mysqli_real_escape_string agar lebih aman saat UTS)
$username = mysqli_real_escape_string($koneksi, $username);
$password = mysqli_real_escape_string($koneksi, $password);

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
$cek = mysqli_num_rows($query);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($query);

    $_SESSION['username'] = $data['username'];
    $_SESSION['role']     = $data['role'];

    if ($data['role'] == "admin") {
        // PERBAIKAN 2: Pastikan file ini ada di folder api/
        header("Location: ../admin_dashboard.php");
    } else {
        if (isset($_SESSION['redirect_after_login'])) {
            $tujuan = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            // Jika kembali ke index.html di root
            header("Location: ../../" . $tujuan);
        } else {
            // Kembali ke dashboard user di folder api
            header("Location: ../dashboard_user.php"); 
        }
    }
    exit();

} else {
    // PERBAIKAN 3: Kembali ke login.php di folder api
    echo "<script>
            alert('Username atau Password Salah!');
            window.location.href='../login.php'; 
          </script>";
}
?>