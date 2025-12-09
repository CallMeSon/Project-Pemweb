<?php
include 'db_connect.php';

// Wajib login dan keranjang tidak boleh kosong
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user (untuk alamat)
$stmt_user = $conn->prepare("SELECT nama_lengkap, alamat, telepon FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

// Ambil detail produk di keranjang
$cart = $_SESSION['cart'];
$cart_products = [];
$total_harga = 0;
$total_items = 0;

if (!empty($cart)) {
    $product_ids = array_keys($cart);
    $ids_string = implode(',', $product_ids);
    $sql = "SELECT id, nama_produk, harga, gambar_url FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    
    while($row = $result->fetch_assoc()) {
        $quantity = $cart[$row['id']];
        $subtotal = $row['harga'] * $quantity;
        
        $cart_products[] = [
            'id' => $row['id'],
            'nama_produk' => $row['nama_produk'],
            'harga' => $row['harga'],
            'gambar_url' => $row['gambar_url'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
        
        $total_harga += $subtotal;
        $total_items += $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Radal&Beans</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <h1>Konfirmasi Checkout</h1>
            <nav>
                <a href="cart.php" class="btn">← Kembali ke Keranjang</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="checkout-container">
            <!-- LEFT SIDE: Alamat Pengiriman -->
            <div class="checkout-details">
                <h3>📍 Alamat Pengiriman</h3>
                
                <div class="info-group">
                    <span class="info-label">Nama Lengkap</span>
                    <div class="info-value">
                        <?php echo htmlspecialchars($user_data['nama_lengkap'] ?? 'Belum diisi'); ?>
                    </div>
                </div>

                <div class="info-group">
                    <span class="info-label">Nomor Telepon</span>
                    <div class="info-value">
                        <?php echo htmlspecialchars($user_data['telepon'] ?? 'Belum diisi'); ?>
                    </div>
                </div>

                <div class="info-group">
                    <span class="info-label">Alamat Lengkap</span>
                    <div class="info-value">
                        <?php echo nl2br(htmlspecialchars($user_data['alamat'] ?? 'Belum diisi')); ?>
                    </div>
                </div>
            </div>
            
            <!-- RIGHT SIDE: Ringkasan Pesanan -->
            <div class="checkout-summary">
                <h3>🛒 Ringkasan Pesanan</h3>
                
                <!-- Daftar Produk -->
                <div class="product-list">
                    <?php foreach ($cart_products as $product): ?>
                        <div class="product-item">
                            <img src="<?php echo htmlspecialchars($product['gambar_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['nama_produk']); ?>"
                                 class="product-thumb">
                            <div class="product-details">
                                <div class="product-name"><?php echo htmlspecialchars($product['nama_produk']); ?></div>
                                <div class="product-price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?> × <?php echo $product['quantity']; ?></div>
                            </div>
                            <div class="product-subtotal">
                                Rp <?php echo number_format($product['subtotal'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="item-count">
                    <strong>Total:</strong> <?php echo $total_items; ?> item dari <?php echo count($cart); ?> jenis produk
                </div>

                <div class="total-section">
                    <div class="total-label">Total Pembayaran</div>
                    <div class="total-price">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></div>
                </div>
                
                <form action="checkout_process.php" method="POST" onsubmit="return confirmCheckout()">
                    <button type="submit" class="btn">
                        Konfirmasi & Buat Pesanan
                    </button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <?php include 'template/footer.html' ?>
    </footer>

    <script>
        function confirmCheckout() {
            return confirm('Apakah Anda yakin ingin membuat pesanan ini?\n\nTotal: Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>');
        }
    </script>
</body>
</html>