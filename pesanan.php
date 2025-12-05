<?php
include 'db_connect.php';

$is_logged_in = isset($_SESSION['user_id']);

// Wajib login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// PROSES SUBMIT REVIEW
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $order_id = intval($_POST['order_id']);
    $reviews = $_POST['reviews'] ?? [];
    
    $conn->begin_transaction();
    try {
        $stmt_insert = $conn->prepare("
            INSERT INTO product_reviews (product_id, user_id, rating, komentar) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), komentar = VALUES(komentar)
        ");
        
        foreach ($reviews as $product_id => $review_data) {
            $rating = intval($review_data['rating']);
            $comment = trim($review_data['comment']) ?: null;
            
            if ($rating >= 1 && $rating <= 5) {
                $stmt_insert->bind_param("iiis", $product_id, $user_id, $rating, $comment);
                $stmt_insert->execute();
            }
        }
        
        $stmt_insert->close();
        $conn->commit();
        $message = "Review berhasil dikirim! Terima kasih atas feedback Anda.";
        $message_type = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Gagal mengirim review: " . $e->getMessage();
        $message_type = "error";
    }
}

// AMBIL DAFTAR PESANAN
$stmt = $conn->prepare("
    SELECT id, total_harga, status_pesanan, tanggal_order
    FROM orders 
    WHERE user_id = ?
    ORDER BY tanggal_order DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pesanan_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Radal&Beans</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/pesanan.css">
</head>
<body>
    <?php
        include 'template/navbar.php'; 
    ?>
        

    <div class="container">
        <div class="header">
            <h1>📦 Pesanan Saya</h1>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message_type === 'success' ? '✅' : '❌'; ?> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($pesanan_list)): ?>
            <div class="empty-state">
                <h2>Belum Ada Pesanan</h2>
                <p style="color: #999; margin: 10px 0 20px;">Anda belum memiliki pesanan apapun.</p>
                <a href="index.php" class="btn">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($pesanan_list as $pesanan): ?>
                <?php
                // Ambil detail items dan hitung total quantity untuk order_id yang sama
                $stmt_detail = $conn->prepare("
                    SELECT 
                        od.quantity,
                        od.harga_saat_beli,
                        p.id as product_id,
                        p.nama_produk,
                        p.gambar_url
                    FROM order_details od
                    JOIN products p ON od.product_id = p.id
                    WHERE od.order_id = ?
                ");
                $stmt_detail->bind_param("i", $pesanan['id']);
                $stmt_detail->execute();
                $items = $stmt_detail->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt_detail->close();
                
                // Hitung total quantity dari semua produk dengan order_id yang sama
                $total_quantity = 0;
                foreach ($items as $item) {
                    $total_quantity += intval($item['quantity']);
                }
                
                // Cek review
                $stmt_review = $conn->prepare("
                    SELECT COUNT(*) as reviewed
                    FROM product_reviews
                    WHERE user_id = ? AND product_id IN (
                        SELECT product_id FROM order_details WHERE order_id = ?
                    )
                ");
                $stmt_review->bind_param("ii", $user_id, $pesanan['id']);
                $stmt_review->execute();
                $review_count = $stmt_review->get_result()->fetch_assoc()['reviewed'];
                $all_reviewed = ($review_count == count($items));
                $stmt_review->close();
                
                $status_class = 'status-pending';
                if ($pesanan['status_pesanan'] === 'Diproses') $status_class = 'status-diproses';
                if ($pesanan['status_pesanan'] === 'Dikirim') $status_class = 'status-dikirim';
                if ($pesanan['status_pesanan'] === 'Selesai') $status_class = 'status-selesai';
                ?>
                
                <div class="pesanan-card">
                    <div class="pesanan-header">
                        <div>
                            <h3 style="color: #333;">Pesanan #<?php echo $pesanan['id']; ?></h3>
                            <p style="color: #666; font-size: 14px; margin-top: 5px;">
                                <?php echo date('d M Y, H:i', strtotime($pesanan['tanggal_order'])); ?>
                            </p>
                        </div>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo htmlspecialchars($pesanan['status_pesanan']); ?>
                        </span>
                    </div>
                    
                    <div class="pesanan-info">
                        <div class="info-item">
                            <span class="info-label">Total Pembayaran</span>
                            <span class="info-value" style="color: #009879;">
                                Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Jumlah Item</span>
                            <span class="info-value"><?php echo $total_quantity; ?> item</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="info-value"><?php echo $pesanan['status_pesanan']; ?></span>
                        </div>
                    </div>
                    
                    <div class="pesanan-actions">
                        <?php if ($pesanan['status_pesanan'] !== 'Selesai'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $pesanan['id']; ?>">
                            </form>
                        <?php endif; ?>
                        
                        <?php if ($pesanan['status_pesanan'] === 'Selesai' && !$all_reviewed): ?>
                            <button class="btn btn-warning btn-small" onclick="openReviewPopup(<?php echo $pesanan['id']; ?>)">
                                ⭐ Tulis Review
                            </button>
                        <?php elseif ($pesanan['status_pesanan'] === 'Selesai' && $all_reviewed): ?>
                            <span style="color: #28a745; font-weight: bold; font-size: 14px;">✓ Sudah direview</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- POPUP REVIEW -->
                <div id="popup-<?php echo $pesanan['id']; ?>" class="popup-overlay">
                    <div class="popup-content">
                        <div class="popup-header">
                            <h2>⭐ Tulis Review</h2>
                            <button class="popup-close" onclick="closeReviewPopup(<?php echo $pesanan['id']; ?>)">×</button>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $pesanan['id']; ?>">
                            
                            <?php foreach ($items as $item): ?>
                                <?php
                                // Cek apakah sudah direview
                                $stmt_check = $conn->prepare("SELECT rating, komentar FROM product_reviews WHERE user_id = ? AND product_id = ?");
                                $stmt_check->bind_param("ii", $user_id, $item['product_id']);
                                $stmt_check->execute();
                                $existing_review = $stmt_check->get_result()->fetch_assoc();
                                $stmt_check->close();
                                
                                if ($existing_review) continue; // Skip jika sudah direview
                                ?>
                                
                                <div class="product-review-item">
                                    <div class="product-header">
                                        <img src="<?php echo htmlspecialchars($item['gambar_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['nama_produk']); ?>"
                                             class="product-image">
                                        <div>
                                            <div class="product-name"><?php echo htmlspecialchars($item['nama_produk']); ?></div>
                                            <p style="color: #666; font-size: 14px;">Qty: <?php echo $item['quantity']; ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Rating <span style="color: #dc3545;">*</span></label>
                                        <div class="star-rating" data-product="<?php echo $item['product_id']; ?>">
                                            <span class="star" data-value="1">★</span>
                                            <span class="star" data-value="2">★</span>
                                            <span class="star" data-value="3">★</span>
                                            <span class="star" data-value="4">★</span>
                                            <span class="star" data-value="5">★</span>
                                        </div>
                                        <input type="hidden" name="reviews[<?php echo $item['product_id']; ?>][rating]" 
                                               id="rating-<?php echo $item['product_id']; ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Komentar (Opsional)</label>
                                        <textarea name="reviews[<?php echo $item['product_id']; ?>][comment]" 
                                                  placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div style="display: flex; gap: 10px; margin-top: 20px;">
                                <button type="submit" name="submit_review" class="btn" style="flex: 1;">
                                    ✓ Kirim Review
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="closeReviewPopup(<?php echo $pesanan['id']; ?>)">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Pesanan Anda Sudah Masuk!</h2>
            <p>Pesanan Anda telah dicatat sebagai "Pending".</p>
            <button onclick="redirectToOrders()">Lihat Pesanan Saya</button>
        </div>
    </div>

    <script>
        // Star Rating
        document.querySelectorAll('.star-rating').forEach(container => {
            const productId = container.dataset.product;
            const stars = container.querySelectorAll('.star');
            const input = document.getElementById('rating-' + productId);
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.dataset.value;
                    input.value = value;
                    
                    stars.forEach((s, index) => {
                        if (index < value) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
                
                star.addEventListener('mouseenter', function() {
                    const value = this.dataset.value;
                    stars.forEach((s, index) => {
                        s.style.color = index < value ? '#ffc107' : '#ddd';
                    });
                });
            });
            
            container.addEventListener('mouseleave', function() {
                const currentValue = input.value;
                stars.forEach((s, index) => {
                    s.style.color = currentValue && index < currentValue ? '#ffc107' : '#ddd';
                });
            });
        });
        
        // Popup Functions
        function openReviewPopup(orderId) {
            document.getElementById('popup-' + orderId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeReviewPopup(orderId) {
            document.getElementById('popup-' + orderId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function redirectToOrders() {
            window.location.href = 'pesanan.php'; // Ganti dengan URL halaman pesanan Anda
        }
        
        // Close popup when clicking overlay
        document.querySelectorAll('.popup-overlay').forEach(popup => {
            popup.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // Modal Script
        function showModal() {
            document.getElementById("orderModal").style.display = "block";
        }
    </script>

</body>

 

</html>

<?php $conn->close(); ?>