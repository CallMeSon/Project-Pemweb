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

// Ambil ringkasan keranjang (hanya total)
$cart = $_SESSION['cart'];
$total_harga = 0;
if (!empty($cart)) {
    $product_ids = array_keys($cart);
    $ids_string = implode(',', $product_ids);
    $sql = "SELECT id, harga FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    $harga_db = [];
    while($row = $result->fetch_assoc()) {
        $harga_db[$row['id']] = $row['harga'];
    }
    foreach ($cart as $id => $qty) {
        $total_harga += ($harga_db[$id] ?? 0) * $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Checkout - Kedai Kopi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="navbar">
        <h1>Konfirmasi Checkout</h1>
        <nav><a href="cart.php" class="btn">Kembali ke Keranjang</a></nav>
    </header>

    <main class="container">
        <div class="checkout-container">
            <div class="checkout-details">
                <h3>Alamat Pengiriman</h3>
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($user_data['nama_lengkap']); ?></p>
                <p><strong>Telepon:</strong> <?php echo htmlspecialchars($user_data['telepon']); ?></p>
                <p><strong>Alamat:</strong><br><?php echo nl2br(htmlspecialchars($user_data['alamat'])); ?></p>
                <small>(Untuk mengubah alamat, silakan ubah di halaman profil Anda)</small>
            </div>
            
            <div class="checkout-summary">
                <h3>Ringkasan Pesanan</h3>
                <p>Jumlah Item: <?php echo count($cart); ?> jenis produk</p>
                <h4 style="margin: 0;">Total Pembayaran:</h4>
                <h2 style="margin: 0; color: #009879;">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></h2>
                
                <p>
                    Ini adalah transaksi sederhana. Dengan mengklik tombol di bawah, pesanan Anda akan dicatat
                    sebagai "Pending" dan Anda akan diarahkan ke halaman "Pesanan Saya".
                </p>
                
                <form action="checkout_process.php" method="POST">
                    <button type="submit" class="btn" style="width: 100%; font-size: 1.3em; padding: 20px;">
                        Konfirmasi & Buat Pesanan
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>