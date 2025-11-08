<?php
include 'db_connect.php';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];

    // HASH password - JANGAN PERNAH SIMPAN PLAIN TEXT
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Gunakan prepared statement untuk keamanan
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, alamat, telepon) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $hashed_password, $nama_lengkap, $alamat, $telepon);
        
        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan <a href='login.php'>login di sini</a>.";
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        // Tangani error jika username sudah ada (karena ada UNIQUE constraint)
        if ($e->getCode() == 1062) { // 1062 = Duplicate entry
            $error = "Username '{$username}' sudah digunakan. Silakan pilih username lain.";
        } else {
            $error = "Registrasi gagal: " . $e->getMessage();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi - Kedai Kopi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Registrasi Akun Baru</h2>
        
        <?php if ($error): ?><p class="error-msg"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success-msg"><?php echo $success; ?></p><?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="telepon">No. Telepon:</label>
                <input type="tel" id="telepon" name="telepon">
            </div>
            <button type="submit" class="btn">Daftar</button>
            <p style="text-align: center; margin-top: 10px;">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </form>
    </div>
</body>
</html>