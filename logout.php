<?php
// logout.php
include 'db_connect.php'; // Untuk session_start()

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Alihkan kembali ke halaman login
header("Location: login.php");
exit;
?>