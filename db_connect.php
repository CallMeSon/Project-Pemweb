<?php

$host = 'localhost';
$db_user = 'root'; 
$db_pass = '';     
$db_name = 'project_prakweb'; 

// Buat koneksi
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

// Atur zona waktu (opsional tapi bagus)
date_default_timezone_set('Asia/Jakarta');

// Mulai session
if (!session_id()) {
    session_start();
}
?>