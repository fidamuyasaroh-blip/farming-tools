<?php
session_start(); 

// PERBAIKAN 1: Gunakan __DIR__ agar path absolut dan tidak error di Vercel
require_once __DIR__ . '/../koneksi.php';

$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role     = 'user';

// Tambahkan pengaman agar input tidak merusak database (SQL Injection)
$username = mysqli_real_escape_string($koneksi, $username);
$email    = mysqli_real_escape_string($koneksi, $email);
$password = mysqli_real_escape_string($koneksi, $password);

// Validasi panjang password
if (strlen($password) < 8) {
    echo "<script>
            alert('Password harus minimal 8 karakter!');
            window.location.href='../register.php';
          </script>";
    exit();
}

// Cek username sudah dipakai belum
$cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>
            alert('Username sudah digunakan, pilih username lain!');
            window.location.href='../register.php';
          </script>";
    exit();
}

$query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
$result = mysqli_query($koneksi, $query);

if ($result) {
    echo "<script>
            alert('Registrasi Berhasil! Silakan Login.');
            window.location.href='../login.php';
          </script>";
} else {
    echo "Registrasi Gagal: " . mysqli_error($koneksi);
}
?>