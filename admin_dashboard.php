<?php
include 'db_connect.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['username'] !== 'Admin') {
    header("Location: index.php");
    exit;
}

// Ambil filter status dari URL
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Query untuk mengambil semua pesanan dengan detail
$sql = "SELECT 
            o.id as order_id,
            o.tanggal_order,
            o.total_harga,
            o.status_pesanan,
            u.username,
            u.nama_lengkap,
            u.telepon,
            u.alamat,
            GROUP_CONCAT(
                CONCAT(p.nama_produk, ' (x', od.quantity, ')')
                SEPARATOR ', '
            ) as items
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_details od ON o.id = od.order_id
        LEFT JOIN products p ON od.product_id = p.id";

if ($filter_status !== 'all') {
    $sql .= " WHERE o.status_pesanan = ?";
}

$sql .= " GROUP BY o.id ORDER BY o.tanggal_order DESC";

if ($filter_status !== 'all') {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filter_status);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Hitung statistik
$stats_query = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status_pesanan = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status_pesanan = 'Diproses' THEN 1 ELSE 0 END) as diproses,
                    SUM(CASE WHEN status_pesanan = 'Selesai' THEN 1 ELSE 0 END) as selesai,
                    SUM(CASE WHEN status_pesanan = 'Dibatalkan' THEN 1 ELSE 0 END) as dibatalkan,
                    SUM(total_harga) as total_revenue
                FROM orders";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Radal&Beans</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Main Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>📊 Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="nav-item active">
                    <span>📦</span> Kelola Pesanan
                </a>
                <a href="logout.php" class="nav-item logout">
                    <span>🚪</span> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="content-header">
                <h1>Dashboard Pesanan</h1>
                <p class="subtitle">Kelola semua pesanan pelanggan di sini</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #3498db;">📦</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f39c12;">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['pending']; ?></h3>
                        <p>Pending</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #9b59b6;">🔄</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['diproses']; ?></h3>
                        <p>Diproses</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #27ae60;">✅</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['selesai']; ?></h3>
                        <p>Selesai</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e74c3c;">❌</div>
                    <div class="stat-info">
                        <h3><?php echo $stats['dibatalkan']; ?></h3>
                        <p>Dibatalkan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #16a085;">💰</div>
                    <div class="stat-info">
                        <h3>Rp <?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?></h3>
                        <p>Total Pendapatan</p>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="admin_dashboard.php?status=all" class="tab <?php echo $filter_status === 'all' ? 'active' : ''; ?>">
                    Semua
                </a>
                <a href="admin_dashboard.php?status=Pending" class="tab <?php echo $filter_status === 'Pending' ? 'active' : ''; ?>">
                    Pending
                </a>
                <a href="admin_dashboard.php?status=Diproses" class="tab <?php echo $filter_status === 'Diproses' ? 'active' : ''; ?>">
                    Diproses
                </a>
                <a href="admin_dashboard.php?status=Selesai" class="tab <?php echo $filter_status === 'Selesai' ? 'active' : ''; ?>">
                    Selesai
                </a>
                <a href="admin_dashboard.php?status=Dibatalkan" class="tab <?php echo $filter_status === 'Dibatalkan' ? 'active' : ''; ?>">
                    Dibatalkan
                </a>
            </div>

            <!-- Orders Table -->
            <div class="orders-section">
                <h2>Daftar Pesanan</h2>
                
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Kontak</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($order = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                                        <td><?php echo date('d M Y H:i', strtotime($order['tanggal_order'])); ?></td>
                                        <td>
                                            <div class="customer-info">
                                                <strong><?php echo htmlspecialchars($order['nama_lengkap']); ?></strong>
                                                <small>@<?php echo htmlspecialchars($order['username']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <div>📞 <?php echo htmlspecialchars($order['telepon']); ?></div>
                                                <div>📍 <?php echo htmlspecialchars($order['alamat']); ?></div>
                                            </div>
                                        </td>
                                        <td class="items-cell">
                                            <?php echo htmlspecialchars($order['items']); ?>
                                        </td>
                                        <td class="price-cell">
                                            <strong>Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></strong>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['status_pesanan']); ?>">
                                                <?php echo $order['status_pesanan']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-detail" onclick="viewOrderDetail(<?php echo $order['order_id']; ?>)">
                                                    👁️ Detail
                                                </button>
                                                <?php if ($order['status_pesanan'] !== 'Selesai' && $order['status_pesanan'] !== 'Dibatalkan'): ?>
                                                    <button class="btn-action btn-update" onclick="openUpdateModal(<?php echo $order['order_id']; ?>, '<?php echo $order['status_pesanan']; ?>')">
                                                        ✏️ Update
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-orders">
                        <div class="no-orders-icon">📭</div>
                        <h3>Belum Ada Pesanan</h3>
                        <p>Tidak ada pesanan <?php echo $filter_status !== 'all' ? 'dengan status "' . $filter_status . '"' : ''; ?> saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal Update Status -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h2>Update Status Pesanan</h2>
            <form id="updateForm" method="POST" action="update_order_status.php">
                <input type="hidden" name="order_id" id="update_order_id">
                
                <div class="form-group">
                    <label for="new_status">Status Baru:</label>
                    <select name="new_status" id="new_status" class="form-control" required>
                        <option value="Pending">Pending</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                
                <div class="modal-buttons">
                    <button type="submit" class="btn btn-primary">💾 Simpan</button>
                    <button type="button" class="btn btn-secondary" onclick="closeUpdateModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Pesanan -->
    <div id="detailModal" class="modal">
        <div class="modal-content modal-large">
            <span class="close" onclick="closeDetailModal()">&times;</span>
            <h2>Detail Pesanan</h2>
            <div id="orderDetailContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>

    <script src="javascript/admin_dashboard.js"></script>
</body>
</html>