<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php?redirect=cart');
    exit;
}

$member_id = $_SESSION['member_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Toko Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h1>
            <p>Kelola produk yang akan Anda beli</p>
        </div>
    </section>

    <!-- Cart Section -->
    <section class="cart-section">
        <div class="container">
            <div class="cart-layout">
                <!-- Cart Items -->
                <div class="cart-items-container">
                    <div class="cart-header">
                        <h2>Produk di Keranjang</h2>
                        <button onclick="clearCart()" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Kosongkan Keranjang
                        </button>
                    </div>
                    
                    <div id="cart-items" class="cart-items">
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Memuat keranjang...
                        </div>
                    </div>
                    
                    <div id="empty-cart" class="empty-cart" style="display: none;">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Keranjang Anda Kosong</h3>
                        <p>Belum ada produk di keranjang belanja</p>
                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Mulai Belanja
                        </a>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary-container">
                    <div class="cart-summary">
                        <h3>Ringkasan Belanja</h3>
                        
                        <div class="summary-row">
                            <span>Total Produk</span>
                            <span id="total-items">0 item</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotal">Rp 0</span>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-row summary-total">
                            <span>Total Pembayaran</span>
                            <span id="total">Rp 0</span>
                        </div>
                        
                        <button onclick="proceedToCheckout()" class="btn btn-success btn-lg btn-block" id="checkout-btn" disabled>
                            <i class="fas fa-credit-card"></i> Lanjut ke Pembayaran
                        </button>
                        
                        <a href="products.php" class="btn btn-outline btn-block">
                            <i class="fas fa-arrow-left"></i> Lanjut Belanja
                        </a>
                    </div>
                    
                    <!-- Promo Code -->
                    <div class="promo-code-section">
                        <h4><i class="fas fa-tag"></i> Kode Promo</h4>
                        <div class="promo-input-group">
                            <input type="text" id="promo-code" placeholder="Masukkan kode promo" class="form-control">
                            <button onclick="applyPromo()" class="btn btn-primary">
                                Gunakan
                            </button>
                        </div>
                        <div id="promo-message" class="promo-message"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        const memberId = <?php echo $member_id; ?>;
        let cartData = [];
        let cartTotal = 0;

        // Load cart
        async function loadCart() {
            try {
                const response = await fetch(`${API_BASE}/cart.php?id_member=${memberId}`);
                const data = await response.json();
                
                if (data.status) {
                    cartData = data.data || [];
                    cartTotal = data.total || 0;
                    displayCart();
                }
            } catch (error) {
                console.error('Error loading cart:', error);
                showError('Gagal memuat keranjang');
            }
        }

        // Display cart
        function displayCart() {
            const container = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            if (cartData.length === 0) {
                container.style.display = 'none';
                emptyCart.style.display = 'flex';
                checkoutBtn.disabled = true;
                updateSummary();
                return;
            }
            
            container.style.display = 'block';
            emptyCart.style.display = 'none';
            checkoutBtn.disabled = false;
            
            container.innerHTML = cartData.map(item => `
                <div class="cart-item" data-cart-id="${item.id}">
                    <div class="cart-item-image">
                        <img src="assets/images/product-placeholder.jpg" alt="${item.nama_barang}">
                    </div>
                    
                    <div class="cart-item-details">
                        <h3 class="cart-item-name">${item.nama_barang}</h3>
                        <div class="cart-item-price">Rp ${formatPrice(item.harga)}</div>
                        <div class="cart-item-stock">Stok tersedia: ${item.stok}</div>
                    </div>
                    
                    <div class="cart-item-quantity">
                        <button onclick="updateCartQty(${item.id}, ${item.qty - 1})" class="qty-btn" ${item.qty <= 1 ? 'disabled' : ''}>
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" value="${item.qty}" min="1" max="${item.stok}" 
                               onchange="updateCartQty(${item.id}, this.value)" 
                               class="qty-input">
                        <button onclick="updateCartQty(${item.id}, ${item.qty + 1})" class="qty-btn" ${item.qty >= item.stok ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    
                    <div class="cart-item-subtotal">
                        <div class="subtotal-label">Subtotal</div>
                        <div class="subtotal-value">Rp ${formatPrice(item.subtotal)}</div>
                    </div>
                    
                    <div class="cart-item-actions">
                        <button onclick="removeFromCart(${item.id})" class="btn-icon btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
            
            updateSummary();
        }

        // Update summary
        function updateSummary() {
            const totalItems = cartData.reduce((sum, item) => sum + item.qty, 0);
            
            document.getElementById('total-items').textContent = `${totalItems} item`;
            document.getElementById('subtotal').textContent = `Rp ${formatPrice(cartTotal)}`;
            document.getElementById('total').textContent = `Rp ${formatPrice(cartTotal)}`;
        }

        // Update cart quantity
        async function updateCartQty(cartId, newQty) {
            newQty = parseInt(newQty);
            
            if (newQty < 1) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('qty', newQty);
                
                const response = await fetch(`${API_BASE}/cart.php?id=${cartId}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Jumlah produk diperbarui');
                    loadCart();
                } else {
                    showError(data.message || 'Gagal memperbarui jumlah');
                }
            } catch (error) {
                console.error('Error updating cart:', error);
                showError('Gagal memperbarui keranjang');
            }
        }

        // Remove from cart
        async function removeFromCart(cartId) {
            if (!confirm('Hapus produk dari keranjang?')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`${API_BASE}/cart.php?id=${cartId}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Produk dihapus dari keranjang');
                    loadCart();
                    updateCartCount();
                } else {
                    showError(data.message || 'Gagal menghapus produk');
                }
            } catch (error) {
                console.error('Error removing from cart:', error);
                showError('Gagal menghapus produk');
            }
        }

        // Clear cart
        async function clearCart() {
            if (!confirm('Kosongkan semua produk dari keranjang?')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`${API_BASE}/cart.php?id_member=${memberId}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Keranjang dikosongkan');
                    loadCart();
                    updateCartCount();
                } else {
                    showError(data.message || 'Gagal mengosongkan keranjang');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                showError('Gagal mengosongkan keranjang');
            }
        }

        // Apply promo code
        async function applyPromo() {
            const promoCode = document.getElementById('promo-code').value.trim();
            const messageEl = document.getElementById('promo-message');
            
            if (!promoCode) {
                messageEl.innerHTML = '<span class="error">Masukkan kode promo</span>';
                return;
            }
            
            try {
                const response = await fetch(`${API_BASE}/voucher.php?kode=${promoCode}`);
                const data = await response.json();
                
                if (data.status && data.valid) {
                    const voucher = data.data;
                    const discount = Math.min(
                        (cartTotal * voucher.persentase / 100),
                        voucher.max_potongan
                    );
                    const newTotal = cartTotal - discount;
                    
                    messageEl.innerHTML = `<span class="success">
                        <i class="fas fa-check-circle"></i> 
                        Kode promo berhasil! Hemat Rp ${formatPrice(discount)}
                    </span>`;
                    
                    document.getElementById('total').textContent = `Rp ${formatPrice(newTotal)}`;
                } else {
                    messageEl.innerHTML = '<span class="error">Kode promo tidak valid atau sudah kadaluarsa</span>';
                }
            } catch (error) {
                console.error('Error applying promo:', error);
                messageEl.innerHTML = '<span class="error">Gagal memvalidasi kode promo</span>';
            }
        }

        // Proceed to checkout
        function proceedToCheckout() {
            if (cartData.length === 0) {
                showError('Keranjang masih kosong');
                return;
            }
            
            window.location.href = 'checkout.php';
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadCart();
        });
    </script>
</body>
</html>
