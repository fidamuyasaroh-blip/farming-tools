<?php
// Hapus semua cookie login
$past = time() - 3600;
setcookie("user_id",  "", $past, "/");
setcookie("username", "", $past, "/");
setcookie("role",     "", $past, "/");

header("Location: /index.html");
exit();
?>
