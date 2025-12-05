<?php
include 'db_connect.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'Admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id > 0) {
    // Ambil data order
    $sql_order = "SELECT o.*, u.username, u.nama_lengkap, u.telepon, u.alamat
                  FROM orders o
                  JOIN users u ON o.user_id = u.id
                  WHERE o.id = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();
    $order = $stmt_order->get_result()->fetch_assoc();
    
    if ($order) {
        // Ambil detail items
        $sql_items = "SELECT od.*, p.nama_produk, p.gambar_url
                      FROM order_details od
                      JOIN products p ON od.product_id = p.id
                      WHERE od.order_id = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Format response
        $response = [
            'order_id' => $order['id'],
            'tanggal_order' => date('d M Y H:i', strtotime($order['tanggal_order'])),
            'status' => $order['status_pesanan'],
            'total_harga' => $order['total_harga'],
            'customer' => [
                'username' => $order['username'],
                'nama_lengkap' => $order['nama_lengkap'],
                'telepon' => $order['telepon'],
                'alamat' => $order['alamat']
            ],
            'items' => $items
        ];
        
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Order not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid order ID']);
}
?>