<?php
// checkout_process.php
include 'db_connect.php';



// Pastikan user login dan keranjang tidak kosong
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$total_harga_pesanan = 0;

// Mulai Database Transaction
$conn->begin_transaction();

try {
    // 1. Ambil harga asli dari DB (Validasi Harga)
    $product_ids = array_keys($cart);
    $ids_string = implode(',', $product_ids);
    $sql = "SELECT id, harga FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    
    $harga_produk_db = [];
    while ($row = $result->fetch_assoc()) {
        $harga_produk_db[$row['id']] = $row['harga'];
    }

    // Hitung total harga berdasarkan harga DB
    foreach ($cart as $product_id => $quantity) {
        if (!isset($harga_produk_db[$product_id])) {
            throw new Exception("Produk dengan ID {$product_id} tidak ditemukan.");
        }
        $total_harga_pesanan += $harga_produk_db[$product_id] * $quantity;
    }

    // 2. Masukkan 1 baris ke tabel 'orders'
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_harga, status_pesanan) VALUES (?, ?, 'Pending')");
    $stmt_order->bind_param("id", $user_id, $total_harga_pesanan);
    $stmt_order->execute();
    
    // Ambil ID dari order yang baru saja dibuat
    $new_order_id = $conn->insert_id;
    $stmt_order->close();

    // 3. Masukkan N baris ke tabel 'order_details'
    $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, harga_saat_beli) VALUES (?, ?, ?, ?)");
    
    foreach ($cart as $product_id => $quantity) {
        $harga_saat_beli = $harga_produk_db[$product_id];
        $stmt_detail->bind_param("iiid", $new_order_id, $product_id, $quantity, $harga_saat_beli);
        $stmt_detail->execute();
    }
    $stmt_detail->close();

    // 4. Jika semua berhasil, commit transaksi
    $conn->commit();

    // 5. Kosongkan keranjang belanja
    unset($_SESSION['cart']);

    // Tutup koneksi sebelum melakukan redirect/exit
    $conn->close();

    // 6. Alihkan ke halaman "Pesanan Saya" (dashboard pembeli)
    header("Location: pesanan.php?status=sukses");
    exit;

} catch (Exception $e) {
    // 6. Jika ada 1 saja yang gagal, batalkan semua (rollback)
    $conn->rollback();
    
    // Tutup koneksi sebelum melakukan redirect/exit
    $conn->close();

    // Alihkan kembali ke keranjang dengan pesan error
    header("Location: cart.php?error=Pesanan gagal diproses: " . urlencode($e->getMessage()));
    exit;
}
?>