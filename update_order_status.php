<?php
include 'db_connect.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $new_status = isset($_POST['new_status']) ? $_POST['new_status'] : '';
    
    // Validasi status
    $allowed_statuses = ['Pending', 'Diproses', 'Selesai', 'Dibatalkan'];
    
    if ($order_id > 0 && in_array($new_status, $allowed_statuses)) {
        $sql = "UPDATE orders SET status_pesanan = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Status pesanan #$order_id berhasil diupdate menjadi '$new_status'";
        } else {
            $_SESSION['error_message'] = "Gagal mengupdate status pesanan";
        }
        
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Data tidak valid";
    }
}

header("Location: admin_dashboard.php");
exit;
?>