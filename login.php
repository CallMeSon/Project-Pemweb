<?php
// Mulai session di paling atas file
session_start();

// Sertakan file koneksi database
require 'db.php';

// Cek apakah data dikirim menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Lindungi dari SQL Injection dengan Prepared Statements
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username); // "s" berarti tipenya adalah string
    $stmt->execute();
    $stmt->store_result();

    // Cek apakah pengguna ditemukan
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password); // Ambil password yang ter-hash dari database
        $stmt->fetch();

        // 2. Verifikasi Kata Sandi
        // Gunakan password_verify() untuk membandingkan password yang diinput
        // dengan hash yang ada di database.
        if (password_verify($password, $hashed_password)) {
            
            // Kata sandi benar!
            // Simpan data pengguna di session
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            // Alihkan pengguna ke halaman selamat datang (welcome.php)
            header("Location: welcome.php");
            exit; // Pastikan skrip berhenti setelah redirect

        } else {
            // Kata sandi salah
            echo "Username atau password salah.";
            // Anda bisa redirect kembali ke login dengan pesan error
            // header("Location: index.html?error=1");
        }
    } else {
        // Pengguna tidak ditemukan
        echo "Username atau password salah.";
    }

    $stmt->close();
}

$conn->close();
?>