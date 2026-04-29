<?php
session_start();
session_destroy(); // Tetap jalankan untuk membersihkan sisa session

// HAPUS COOKIE (Wajib agar benar-benar logout di Vercel)
// Setel waktu ke -3600 (satu jam yang lalu)
setcookie("user_id", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/");
setcookie("role", "", time() - 3600, "/");

// Arahkan ke halaman utama atau login
header("Location: /index.html");
exit();
?>