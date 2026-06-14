<?php
ob_start();
require_once __DIR__ . '/../koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    echo "<script>alert('Username dan password wajib diisi!'); window.location.href='../login.php';</script>";
    exit();
}

// Prepared statement — anti SQL injection
$stmt = mysqli_prepare($koneksi, "SELECT id, username, password, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);

    // Cek password: dukung password_hash (baru) DAN plain text lama
    $password_ok = false;
    if (password_verify($password, $data['password'])) {
        $password_ok = true;
    } elseif ($data['password'] === $password) {
        // Password masih plain text — migrate otomatis ke hash
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt_up = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt_up, 'si', $new_hash, $data['id']);
        mysqli_stmt_execute($stmt_up);
        $password_ok = true;
    }

    if ($password_ok) {
        setcookie("user_id",  $data['id'],       time() + 86400, "/", "", false, true);
        setcookie("username", $data['username'],  time() + 86400, "/", "", false, true);
        setcookie("role",     $data['role'],      time() + 86400, "/", "", false, true);

        if (strtolower($data['role']) === 'admin') {
            header("Location: ../admin_dashboard.php");
        } else {
            header("Location: ../dashboard_user.php");
        }
        exit();
    }
}

echo "<script>alert('Username atau Password Salah!'); window.location.href='../login.php';</script>";
ob_end_flush();
?>
