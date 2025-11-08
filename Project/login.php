<?php
include 'db_connect.php';
$error = '';

// Jika sudah login, tendang ke index.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Siapkan statement
    $stmt = $conn->prepare("SELECT id, password, nama_lengkap FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password yang di-hash
        if (password_verify($password, $user['password'])) {
            // Password benar! Simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            
            // Alihkan ke halaman utama
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Username atau password salah.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Kedai Kopi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login Pembeli</h2>
        
        <?php if ($error): ?><p class="error-msg"><?php echo $error; ?></p><?php endif; ?>
        <?php if (isset($_GET['pesan'])): ?><p class="success-msg"><?php echo htmlspecialchars($_GET['pesan']); ?></p><?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <p style="text-align: center; margin-top: 10px;">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </form>
    </div>
</body>
</html>