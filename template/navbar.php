    <!-- Navbar Sticky -->
<link rel="stylesheet" href="css/index.css">

    <header class="navbar" id="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="index.php">
                    <span class="logo-icon">☕</span>
                    <h1>Radal&Beans</h1>
                </a>
            </div>
            <nav class="navbar-menu">
                <?php if ($is_logged_in): ?>
                    <span class="user-greeting">Halo, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</span>
                    <a href="cart.php" class="btn btn-cart">
                        <span class="cart-icon">🛒</span> Keranjang
                    </a>
                    <a href="pesanan.php" class="btn btn-cart">
                        <span class="cart-icon">📦</span> Pesanan
                    </a>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                <?php else: ?>
                    <a href="auth.php" class="btn btn-login">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>