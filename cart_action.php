<?php
// cart_action.php
include 'db_connect.php';

// Wajib login untuk aksi keranjang
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?pesan=Silakan login untuk berbelanja.");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? 'add';
    $product_id = (int)$_POST['product_id'];

    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'add':
            $quantity = (int)$_POST['quantity'];
            if ($quantity < 1) $quantity = 1;

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            // Arahkan kembali ke index setelah menambah
            header("Location: index.php?status=cart_added");
            exit;

        case 'remove':
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
            // Arahkan kembali ke keranjang
            header("Location: cart.php");
            exit;
            
        case 'update':
            $quantity = (int)$_POST['quantity'];
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                // Hapus jika kuantitas 0 atau kurang
                unset($_SESSION['cart'][$product_id]);
            }
            header("Location: cart.php");
            exit;
    }
}
?>