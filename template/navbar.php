    <!-- Navbar Sticky -->
<link rel="stylesheet" href="css/index.css">

    <header class="navbar" id="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="index.php">
                    <img src="svg/2.svg" alt="logo-icon" width="60" height="60">
                    <h1>Radal&Beans</h1>
                </a>
            </div>
            <nav class="navbar-menu">
                <?php if ($is_logged_in): ?>
                    <span class="user-greeting">Halo, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</span>
                    <a href="cart.php" class="btn btn-cart">
                        <img src="svg/cart.svg" alt="logo-cart" width="30" height="30"> keranjang
                    </a>
                    <a href="pesanan.php" class="btn btn-cart">
                        <img src="svg/orders.svg" alt="logo-orders" width="30" height="30"> pesanan
                    </a>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                <?php else: ?>
                    <a href="auth.php" class="btn btn-login">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>