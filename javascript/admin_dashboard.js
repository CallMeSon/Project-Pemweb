// Admin Dashboard JavaScript

// Open Update Modal
function openUpdateModal(orderId, currentStatus) {
    document.getElementById('update_order_id').value = orderId;
    document.getElementById('new_status').value = currentStatus;
    document.getElementById('updateModal').classList.add('show');
}

// Close Update Modal
function closeUpdateModal() {
    document.getElementById('updateModal').classList.remove('show');
}

// View Order Detail
function viewOrderDetail(orderId) {
    const modal = document.getElementById('detailModal');
    const content = document.getElementById('orderDetailContent');
    
    // Show loading
    content.innerHTML = '<div class="loading">⏳ Memuat detail pesanan...</div>';
    modal.classList.add('show');
    
    // Fetch order details
    fetch(`get_order_detail.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = `<div class="error">❌ ${data.error}</div>`;
                return;
            }
            
            // Build detail HTML
            let itemsHtml = '';
            data.items.forEach(item => {
                itemsHtml += `
                    <li>
                        <span class="item-name">${item.nama_produk} x${item.quantity}</span>
                        <span class="item-price">Rp ${formatNumber(item.harga_saat_beli * item.quantity)}</span>
                    </li>
                `;
            });
            
            content.innerHTML = `
                <div class="detail-section">
                    <h3>📦 Informasi Pesanan</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">ID Pesanan</div>
                            <div class="detail-value">#${data.order_id}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tanggal</div>
                            <div class="detail-value">${data.tanggal_order}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                <span class="status-badge status-${data.status.toLowerCase()}">${data.status}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Total</div>
                            <div class="detail-value" style="color: #27ae60; font-weight: 700; font-size: 1.2rem;">
                                Rp ${formatNumber(data.total_harga)}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>👤 Informasi Pelanggan</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Username</div>
                            <div class="detail-value">@${data.customer.username}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Nama Lengkap</div>
                            <div class="detail-value">${data.customer.nama_lengkap}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Telepon</div>
                            <div class="detail-value">📞 ${data.customer.telepon}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Alamat</div>
                            <div class="detail-value">📍 ${data.customer.alamat}</div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>🛒 Item Pesanan</h3>
                    <ul class="items-list">
                        ${itemsHtml}
                    </ul>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = '<div class="error">❌ Gagal memuat detail pesanan</div>';
            console.error('Error:', error);
        });
}

// Close Detail Modal
function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('show');
}

// Format number to Indonesian format
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const updateModal = document.getElementById('updateModal');
    const detailModal = document.getElementById('detailModal');
    
    if (event.target === updateModal) {
        closeUpdateModal();
    }
    if (event.target === detailModal) {
        closeDetailModal();
    }
}

// Show success/error messages if present
window.addEventListener('DOMContentLoaded', function() {
    // Check for PHP session messages and display them
    // This would typically be handled by PHP outputting a toast/notification
});

// Auto-hide messages after 5 seconds
setTimeout(function() {
    const messages = document.querySelectorAll('.alert-message');
    messages.forEach(msg => {
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 300);
    });
}, 5000);