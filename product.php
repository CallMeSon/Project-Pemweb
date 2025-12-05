<?php
include 'db_connect.php';
$is_logged_in = isset($_SESSION['user_id']);

// Ambil ID produk dari URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: index.php");
    exit();
}

// Ambil detail produk
$sql = "SELECT p.*, c.nama_kategori 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

// Ambil rata-rata rating dan jumlah review
$sql_rating = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
               FROM product_reviews 
               WHERE product_id = ?";
$stmt_rating = $conn->prepare($sql_rating);
$stmt_rating->bind_param("i", $product_id);
$stmt_rating->execute();
$rating_data = $stmt_rating->get_result()->fetch_assoc();

$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$total_reviews = $rating_data['total_reviews'];

// Ambil semua review untuk produk ini
$sql_reviews = "SELECT pr.*, u.nama_lengkap, u.username 
                FROM product_reviews pr 
                JOIN users u ON pr.user_id = u.id 
                WHERE pr.product_id = ? 
                ORDER BY pr.tanggal_review DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $product_id);
$stmt_reviews->execute();
$reviews = $stmt_reviews->get_result();

// Cek apakah user sudah memberikan review
$user_has_reviewed = false;
$user_review = null;
if ($is_logged_in) {
    $sql_user_review = "SELECT * FROM product_reviews 
                        WHERE product_id = ? AND user_id = ?";
    $stmt_user_review = $conn->prepare($sql_user_review);
    $stmt_user_review->bind_param("ii", $product_id, $_SESSION['user_id']);
    $stmt_user_review->execute();
    $user_review = $stmt_user_review->get_result()->fetch_assoc();
    $user_has_reviewed = ($user_review != null);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nama_produk']); ?> - Radal&Beans</title>
    <link rel="stylesheet" href="css/product.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="javascript/index.js"></script>
</head>
<body>
    <!-- Navbar -->
    <?php 
        include 'template/navbar.php';
    ?>
    <!-- Main Content -->
    <main class="container">
        <!-- Product Detail Section -->
        <div class="product-detail">
            <div class="product-image-section">
                <img src="<?php echo htmlspecialchars($product['gambar_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['nama_produk']); ?>"
                     class="product-main-image">
                <span class="product-category-badge"><?php echo htmlspecialchars($product['nama_kategori']); ?></span>
            </div>
            
            <div class="product-info-section">
                <h1 class="product-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
                
                <!-- Rating Display -->
                <div class="rating-summary">
                    <div class="stars-display">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?php echo $i <= $avg_rating ? 'filled' : ''; ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-text"><?php echo $avg_rating; ?>/5</span>
                    <span class="review-count">(<?php echo $total_reviews; ?> review)</span>
                </div>
                
                <p class="product-price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                
                <div class="product-description">
                    <h3>Deskripsi Produk</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>
                </div>
                
                <!-- Add to Cart Form -->
                <form action="cart_action.php" method="POST" class="add-to-cart-section">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="button" class="btn btn-add-cart"
                        onclick='<?php echo $is_logged_in ? "openQtyModal(this.closest(\"form\"), " . json_encode($product['nama_produk']) . ")" : "showLoginError()"; ?>'>
                        <span>🛒</span> Tambah ke Keranjang
                    </button>
                </form>
            </div>
        </div>

        <!-- Error Modal -->
           <div id="errorModal" class="modal">
                <div class="modal-content modal-error">
                    <span class="close" onclick="closeErrorModal()">&times;</span>
                    <h3 id="errorTitle">⚠️ Perhatian</h3>
                    <p id="errorMessage"></p>
                    <div class="error-buttons">
                        <button class="btn btn-confirm-cart" onclick="redirectToLogin()">Login Sekarang</button>
                        <button class="btn btn-cancel" onclick="closeErrorModal()">Batal</button>
                    </div>
                </div>
            </div>

        <!-- Reviews Section -->
        <div class="reviews-section">
            <h2>Review Produk</h2>
            <!-- Reviews List -->
            <div class="reviews-list">
                <h3>Semua Review (<?php echo $total_reviews; ?>)</h3>
                
                <?php if ($reviews->num_rows > 0): ?>
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <span class="reviewer-name"><?php echo htmlspecialchars($review['nama_lengkap']); ?></span>
                                    <span class="review-date"><?php echo date('d M Y', strtotime($review['tanggal_review'])); ?></span>
                                </div>
                                <div class="review-stars">
                                    <?php for ($i = 1; $i <= $review['rating']; $i++): ?>
                                        <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['komentar'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-reviews">Belum ada review untuk produk ini. Jadilah yang pertama!</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php
        include 'template/footer.html';
    ?>
    <script src="javascript/product.js"></script>

    <!-- Quantity Modal-->
    <div id="qtyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeQtyModal()">&times;</span>
            <h3 id="modalProductName"></h3>
            <div class="quantity-control-modal">
                <button type="button" class="qty-btn minus" onclick="decreaseQtyModal()">-</button>
                <input type="number" id="modalQtyInput" value="1" min="1" readonly>
                <button type="button" class="qty-btn plus" onclick="increaseQtyModal()">+</button>
            </div>
            <button class="btn btn-confirm-cart" onclick="confirmAddCart()">Tambahkan ke Keranjang</button>
        </div>
    </div>
</body>
</html>