<?php
include 'db_connect.php';
$is_logged_in = isset($_SESSION['user_id']);

// --- LOGIKA FILTER KATEGORI ---
$kategori_terpilih_id = isset($_GET['kategori_id']) ? (int)$_GET['kategori_id'] : 0;

$sql = "SELECT p.id, p.nama_produk, p.harga, p.deskripsi, p.gambar_url, c.nama_kategori 
        FROM products p
        JOIN categories c ON p.category_id = c.id";

if ($kategori_terpilih_id > 0) {
    $sql .= " WHERE p.category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kategori_terpilih_id);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result_products = $stmt->get_result();
$result_categories = $conn->query("SELECT id, nama_kategori FROM categories ORDER BY nama_kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Selamat Datang di Kedai Kopi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="navbar">
        <h1>Kedai Kopi</h1>
        <nav>
            <?php if ($is_logged_in): ?>
                <span>Halo, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</span>
                <a href="dashboard_pembeli.php" class="btn">Pesanan Saya</a>
                <a href="cart.php" class="btn">Keranjang</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <h2>Produk Kami</h2>
        
        <div class="category-filters">
            <a href="index.php" class="btn-filter <?php echo ($kategori_terpilih_id == 0) ? 'active' : ''; ?>">Semua</a>
            <?php
            if ($result_categories->num_rows > 0) {
                while ($cat = $result_categories->fetch_assoc()) {
                    $active_class = ($kategori_terpilih_id == $cat['id']) ? 'active' : '';
                    echo "<a href='index.php?kategori_id=" . $cat['id'] . "' class='btn-filter " . $active_class . "'>";
                    echo htmlspecialchars($cat['nama_kategori']);
                    echo "</a>";
                }
            }
            ?>
        </div>

        <div class="product-grid">
            <?php
            if ($result_products->num_rows > 0) {
                while($row = $result_products->fetch_assoc()) {
            ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($row['gambar_url']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                        <span class="product-category"><?php echo htmlspecialchars($row['nama_kategori']); ?></span>
                        <h3><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                        <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                        <p class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        
                        <form action="cart_action.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" style="width: 50px;">
                            <button type="submit" class="btn">Tambah ke Keranjang</button>
                        </form>
                    </div>
            <?php
                }
            } else { echo "<p>Tidak ada produk dalam kategori ini.</p>"; }
            $stmt->close();
            ?>
        </div>
    </main>
</body>
</html>