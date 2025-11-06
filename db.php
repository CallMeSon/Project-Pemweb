<?php
// Ganti dengan detail database Anda
$DB_HOST = 'localhost';
$DB_USER = 'root'; // Ganti dengan username database Anda
$DB_PASS = '';     // Ganti dengan password database Anda
$DB_NAME = 'project_prakweb'; // Ganti dengan nama database Anda

// Membuat koneksi menggunakan MySQLi
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}
?>