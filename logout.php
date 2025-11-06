<?php
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Alihkan kembali ke halaman login
header("Location: index.html");
exit;
?>