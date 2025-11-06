<?php
session_start();

// Cek apakah pengguna sudah login
// Jika tidak, alihkan kembali ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; }
    </style>
</head>
<body>
    <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Anda telah berhasil login.</p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>