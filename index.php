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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Radal&Beans</title>
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
     <?php 
      include 'template/navbar.php';
     ?> 

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">Selamat Datang di Kedai Kopi</h1>
            <p class="hero-subtitle">Nikmati secangkir kebahagiaan dengan kopi pilihan terbaik kami</p>
            <a href="#produk" class="btn btn-hero">Lihat Menu</a>
        </div>
        <div class="hero-decoration">
            <div class="coffee-steam steam-1"></div>
            <div class="coffee-steam steam-2"></div>
            <div class="coffee-steam steam-3"></div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container" id="produk">
        <div class="section-header">
            <h2>Produk Kami</h2>
            <p class="section-subtitle">Pilihan kopi dan minuman berkualitas untuk Anda</p>
        </div>
        
        <!-- Category Filters -->
        <div class="category-filters">
            <a href="index.php" class="btn-filter <?php echo ($kategori_terpilih_id == 0) ? 'active' : ''; ?>">
                <span>Semua</span>
            </a>
            <?php
            if ($result_categories->num_rows > 0) {
                while ($cat = $result_categories->fetch_assoc()) {
                    $active_class = ($kategori_terpilih_id == $cat['id']) ? 'active' : '';
                    echo "<a href='index.php?kategori_id=" . $cat['id'] . "' class='btn-filter " . $active_class . "'>";
                    echo "<span>" . htmlspecialchars($cat['nama_kategori']) . "</span>";
                    echo "</a>";
                }
            }
            ?>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php
            if ($result_products->num_rows > 0) {
                while($row = $result_products->fetch_assoc()) {
            ?>
                    <div class="product-card">
                        <div class="product-image">
                            <a href="product.php?id=<?php echo $row['id']; ?>" class="product-image-link">
                                <img src="<?php echo htmlspecialchars($row['gambar_url']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                            </a>
                            <span class="product-category"><?php echo htmlspecialchars($row['nama_kategori']); ?></span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                            <p class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                            
                            <form action="cart_action.php" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="quantity" value="1" class="qty-hidden">
                                <button type="button" class="btn btn-add-cart" onclick="<?php echo $is_logged_in ? "openQtyModal(this, '" . htmlspecialchars($row['nama_produk']) . "')" : "showLoginError()"; ?>">
                                    Tambah ke Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
            <?php
                }
            } else { 
                echo "<div class='no-products'>";
                echo "<p>Tidak ada produk dalam kategori ini.</p>";
                echo "</div>";
            }
            $stmt->close();
            ?>
        </div>
    </main>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content modal-error">
            <h3 id="errorTitle">⚠️ Perhatian</h3>
            <p id="errorMessage"></p>
            <div class="error-buttons">
                <button class="btn btn-confirm-cart" onclick="redirectToLogin()">Login Sekarang</button>
                <button class="btn btn-cancel" onclick="closeErrorModal()">Batal</button>
            </div>
        </div>
    </div>

    <!-- Quantity Modal Popup -->
    <div id="qtyModal" class="modal">
        <div class="modal-content">
            <h3 id="modalProductName"></h3>
            <div class="quantity-control-modal">
                <button type="button" class="qty-btn minus" onclick="decreaseQtyModal()">-</button>
                <input type="number" id="modalQtyInput" value="1" min="1" readonly>
                <button type="button" class="qty-btn plus" onclick="increaseQtyModal()">+</button>
            </div>
            <button class="btn btn-confirm-cart" onclick="confirmAddCart()">Tambahkan ke Keranjang</button>
        </div>
    </div>

    <!-- Footer -->
     <?php
         include 'template/footer.html';
     ?> 

    <script src="javascript/index.js"></script>
</body>
</html>