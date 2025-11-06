<?php
// Sertakan file koneksi database
require 'db.php';

// Cek apakah data dikirim menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Validasi Sisi Server (Server-Side)
    
    // Cek apakah ada field yang kosong
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header("Location: register.html?error=empty");
        exit;
    }

    // Cek apakah password dan konfirmasi password sama
    if ($password !== $confirm_password) {
        header("Location: register.html?error=password_mismatch");
        exit;
    }

    // 3. Cek apakah username sudah ada (Gunakan Prepared Statements)
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Username sudah terdaftar
        header("Location: register.html?error=username_taken");
        exit;
    }
    
    $stmt->close();

    // 4. Hash Kata Sandi (PENTING!)
    // Gunakan PASSWORD_DEFAULT untuk algoritma hashing terkuat yang tersedia
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 5. Masukkan pengguna baru ke database (Gunakan Prepared Statements)
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        // Registrasi berhasil, alihkan ke halaman login
        // Kita bisa tambahkan pesan sukses di halaman login
        header("Location: index.html?success=registered");
        exit;
    } else {
        // Gagal mengeksekusi
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // Jika diakses langsung tanpa POST, alihkan ke halaman registrasi
    header("Location: register.html");
    exit;
}
?>