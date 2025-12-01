<?php
// cart_action.php
include 'db_connect.php';

// Wajib login
if (!isset($_SESSION['user_id'])) {
    // Jika request AJAX tapi belum login, kirim JSON error
    if (isset($_POST['action']) && $_POST['action'] == 'update_ajax') {
        echo json_encode(['status' => 'error', 'message' => 'Silakan login']);
        exit;
    }
    header("Location: login.php?pesan=Silakan login untuk berbelanja.");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? 'add';
    $product_id = (int)$_POST['product_id'];

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
            header("Location: index.php?status=cart_added");
            exit;

        case 'remove':
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
            header("Location: cart.php");
            exit;
            
        case 'update':
            // Ini fallback untuk browser tanpa JS
            $quantity = (int)$_POST['quantity'];
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            header("Location: cart.php");
            exit;

        // --- FITUR BARU: UPDATE VIA AJAX (JS) ---
        case 'update_ajax':
            $quantity = (int)$_POST['quantity'];
            
            // Update session
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }

            // Hitung Ulang Total Harga Keranjang untuk dikirim balik ke JS
            $new_cart_total = 0;
            $new_item_subtotal = 0;
            
            if (!empty($_SESSION['cart'])) {
                $ids = array_keys($_SESSION['cart']);
                $ids_str = implode(',', $ids);
                
                // Query untuk mengambil harga terbaru
                $sql = "SELECT id, harga FROM products WHERE id IN ($ids_str)";
                $res = $conn->query($sql);
                
                while($row = $res->fetch_assoc()) {
                    $qty = $_SESSION['cart'][$row['id']];
                    $sub = $row['harga'] * $qty;
                    $new_cart_total += $sub;
                    
                    // Simpan subtotal khusus untuk item yang sedang diupdate
                    if($row['id'] == $product_id) {
                        $new_item_subtotal = $sub;
                    }
                }
            }

            // Kirim respons JSON
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'new_subtotal_formatted' => 'Rp ' . number_format($new_item_subtotal, 0, ',', '.'),
                'new_total_formatted' => 'Rp ' . number_format($new_cart_total, 0, ',', '.')
            ]);
            exit;
    }
}
?>