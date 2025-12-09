<?php
include 'db_connect.php';
$error = '';
$success = '';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user_id'])) {
    // Cek apakah admin atau user biasa
    if ($_SESSION['username'] === 'Admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

// Proses LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, nama_lengkap, username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            
            // Redirect berdasarkan role
            if ($user['username'] === 'Admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Username atau password salah.";
    }
    $stmt->close();
}

// Proses REGISTER
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['reg_username'];
    $password = $_POST['reg_password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];

    // Validasi: Tidak boleh register dengan username "Admin"
    if (strtolower($username) === 'admin') {
        $error = "Username 'Admin' adalah username khusus dan tidak dapat digunakan untuk registrasi.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, alamat, telepon) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $hashed_password, $nama_lengkap, $alamat, $telepon);
            
            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login dengan akun Anda.";
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = "Username '{$username}' sudah digunakan. Silakan pilih username lain.";
            } else {
                $error = "Registrasi gagal: " . $e->getMessage();
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - Radal&Beans</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-tabs">
            <button class="auth-tab active" onclick="switchTab('login')">Login</button>
            <button class="auth-tab" onclick="switchTab('register')">Daftar</button>
        </div>
        
        <div class="auth-content">
            <?php if ($error): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['pesan'])): ?>
                <div class="message success"><?php echo htmlspecialchars($_GET['pesan']); ?></div>
            <?php endif; ?>
            
            <!-- FORM LOGIN -->
            <div id="login-form" class="auth-form active">
                <h2 class="auth-title">Selamat Datang Kembali</h2>
                <form action="auth.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required autofocus placeholder="Masukkan username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Masukkan password">
                    </div>
                    
                    <button type="submit" name="login" class="btn-submit">Masuk</button>
                    <a href="index.php" class="back">Kembali ke Beranda</a>
                </form>
            </div>
            
            <!-- FORM REGISTER -->
            <div id="register-form" class="auth-form">
                <h2 class="auth-title">Buat Akun Baru</h2>
                <p class="auth-subtitle">Daftar untuk mulai berbelanja</p>
                <form action="auth.php" method="POST">
                    <div class="form-group">
                        <label for="reg_username">Username</label>
                        <input type="text" id="reg_username" name="reg_username" required placeholder="Pilih username unik">
                        <small class="form-hint">Username tidak boleh "Admin"</small>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Password</label>
                        <input type="password" id="reg_password" name="reg_password" required placeholder="Minimal 6 karakter" minlength="6">
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Nama lengkap Anda">
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap untuk pengiriman"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="telepon">No. Telepon</label>
                        <input type="tel" id="telepon" name="telepon" placeholder="Contoh: 08123456789">
                    </div>
                    <button type="submit" name="register" class="btn-submit">Daftar Sekarang</button>
                    <a href="index.php" class="back">Kembali ke Beranda</a>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            // Update tab buttons
            const tabs = document.querySelectorAll('.auth-tab');
            tabs.forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update forms
            const forms = document.querySelectorAll('.auth-form');
            forms.forEach(f => f.classList.remove('active'));
            document.getElementById(tab + '-form').classList.add('active');
        }
        
        // Jika ada error/success dari register, tampilkan tab register
        <?php if (isset($_POST['register']) || (isset($_POST['reg_username']))): ?>
            const registerTab = document.querySelectorAll('.auth-tab')[1];
            const loginTab = document.querySelectorAll('.auth-tab')[0];
            registerTab.classList.add('active');
            loginTab.classList.remove('active');
            document.getElementById('register-form').classList.add('active');
            document.getElementById('login-form').classList.remove('active');
        <?php endif; ?>
    </script>
</body>
</html>