<?php
include 'db_connect.php';
$is_logged_in = isset($_SESSION['user_id']);

if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$cart_products = [];
$total_harga = 0;

if (!empty($cart)) {
    $product_ids = array_keys($cart);
    $ids_string = implode(',', $product_ids);
    
    // Ambil data produk yang ada di keranjang
    $sql = "SELECT id, nama_produk, harga, gambar_url FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $quantity = $cart[$row['id']];
            $subtotal = $row['harga'] * $quantity;
            $row['quantity'] = $quantity;
            $row['subtotal'] = $subtotal;
            $cart_products[] = $row;
            
            $total_harga += $subtotal;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Keranjang Belanja - Kedai Kopi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="navbar">
        <h1>Keranjang Belanja</h1>
        <nav>
            <a href="index.php" class="btn">Lanjut Belanja</a>
            <?php if ($is_logged_in): ?>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <?php if (empty($cart_products)): ?>
            <h2>Keranjang Anda Kosong</h2>
            <p>Silakan kembali ke <a href="index.php">halaman produk</a> untuk berbelanja.</p>
        <?php else: ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_products as $product): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($product['gambar_url']); ?>" alt="" width="50" style="vertical-align: middle; margin-right: 10px;">
                            <?php echo htmlspecialchars($product['nama_produk']); ?>
                        </td>
                        <td>Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <form action="cart_action.php" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" min="0" style="width: 60px;">
                                <button type="submit" class="btn">Update</button>
                            </form>
                        </td>
                        <td>Rp <?php echo number_format($product['subtotal'], 0, ',', '.'); ?></td>
                        <td>
                            <form action="cart_action.php" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-logout">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Total Belanja:</td>
                        <td colspan="2" style="font-weight: bold; font-size: 1.2em;">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <div style="text-align: right; margin-top: 20px;">
                <a href="checkout.php" class="btn" style="font-size: 1.2em; padding: 15px 30px;">Lanjut ke Checkout</a>
            </div>
            
        <?php endif; ?>
    </main>
</body>
</html>