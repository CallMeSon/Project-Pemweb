// js/cart.js

document.addEventListener('DOMContentLoaded', function() {
    // Ambil semua input quantity
    const quantityInputs = document.querySelectorAll('.quantity-input');

    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.id;
            const newQuantity = this.value;
            const row = this.closest('tr'); // Baris tabel terkait
            
            // Validasi input tidak boleh kurang dari 1
            if(newQuantity < 1) {
                alert("Jumlah minimal adalah 1");
                this.value = 1;
                return;
            }

            // Kirim data ke cart_action.php menggunakan Fetch API
            updateCart(productId, newQuantity, row);
        });
    });
});

function updateCart(id, qty, rowElement) {
    const formData = new FormData();
    formData.append('action', 'update_ajax'); // Action khusus untuk AJAX
    formData.append('product_id', id);
    formData.append('quantity', qty);

    fetch('cart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update Subtotal di baris tersebut
            const subtotalCell = rowElement.querySelector('.subtotal');
            subtotalCell.textContent = data.new_subtotal_formatted;

            // Update Total Belanja di footer
            const totalCell = document.querySelector('#cart-total');
            totalCell.textContent = data.new_total_formatted;
        } else {
            alert('Gagal mengupdate keranjang');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi');
    });
}