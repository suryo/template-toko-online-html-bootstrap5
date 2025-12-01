<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php?redirect=checkout');
    exit;
}

$member_id = $_SESSION['member_id'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toko Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1><i class="fas fa-credit-card"></i> Checkout</h1>
            <p>Lengkapi data untuk menyelesaikan pembelian</p>
        </div>
    </section>

    <!-- Checkout Section -->
    <section class="checkout-section">
        <div class="container">
            <div class="checkout-layout">
                <!-- Checkout Form -->
                <div class="checkout-form-container">
                    <!-- Step Indicator -->
                    <div class="checkout-steps">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <div class="step-label">Alamat Pengiriman</div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-label">Metode Pembayaran</div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-label">Konfirmasi</div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="checkout-section-card">
                        <h3><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>
                        
                        <div id="saved-addresses" class="saved-addresses">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i> Memuat alamat...
                            </div>
                        </div>
                        
                        <button onclick="toggleAddressForm()" class="btn btn-outline" id="add-address-btn">
                            <i class="fas fa-plus"></i> Tambah Alamat Baru
                        </button>
                        
                        <div id="address-form" class="address-form" style="display: none;">
                            <div class="form-group">
                                <label>Nama Penerima *</label>
                                <input type="text" id="nama-penerima" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Nomor HP *</label>
                                <input type="tel" id="no-hp" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Alamat Lengkap *</label>
                                <textarea id="alamat" class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Kota *</label>
                                    <input type="text" id="kota" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Kode Pos</label>
                                    <input type="text" id="kode-pos" class="form-control">
                                </div>
                            </div>
                            
                            <button onclick="saveAddress()" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Alamat
                            </button>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="checkout-section-card">
                        <h3><i class="fas fa-truck"></i> Metode Pengiriman</h3>
                        
                        <div class="shipping-methods">
                            <label class="radio-card">
                                <input type="radio" name="shipping" value="jne" checked>
                                <div class="radio-content">
                                    <div class="radio-header">
                                        <strong>JNE Regular</strong>
                                        <span class="price">Rp 15.000</span>
                                    </div>
                                    <div class="radio-description">Estimasi 3-5 hari</div>
                                </div>
                            </label>
                            
                            <label class="radio-card">
                                <input type="radio" name="shipping" value="jnt">
                                <div class="radio-content">
                                    <div class="radio-header">
                                        <strong>J&T Express</strong>
                                        <span class="price">Rp 12.000</span>
                                    </div>
                                    <div class="radio-description">Estimasi 2-4 hari</div>
                                </div>
                            </label>
                            
                            <label class="radio-card">
                                <input type="radio" name="shipping" value="sicepat">
                                <div class="radio-content">
                                    <div class="radio-header">
                                        <strong>SiCepat</strong>
                                        <span class="price">Rp 10.000</span>
                                    </div>
                                    <div class="radio-description">Estimasi 2-3 hari</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section-card">
                        <h3><i class="fas fa-credit-card"></i> Metode Pembayaran</h3>
                        
                        <div class="payment-methods">
                            <label class="radio-card">
                                <input type="radio" name="payment" value="transfer-bca" checked>
                                <div class="radio-content">
                                    <div class="radio-header">
                                        <strong>Transfer Bank BCA</strong>
                                    </div>
                                    <div class="radio-description">No. Rek: 1234567890 a.n. Toko Online</div>
                                </div>
                            </label>
                            
                            <label class="radio-card">
                                <input type="radio" name="payment" value="transfer-mandiri">
                                <div class="radio-content">
                                    <div class="radio-header">
                                        <strong>Transfer Bank Mandiri</strong>
                                    </div>
                                    <div class="radio-description">No. Rek: 0987654321 a.n. Toko Online</div>
                                </div>
                            </label>
                            
                            <label class="radio-card">
                                <input type="radio" name="payment" value="cod">
                                <div class="radio-content">
                                    <div class="radio-header">
                                        <strong>COD (Bayar di Tempat)</strong>
                                    </div>
                                    <div class="radio-description">Bayar saat barang diterima</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary-container">
                    <div class="order-summary">
                        <h3>Ringkasan Pesanan</h3>
                        
                        <div id="order-items" class="order-items">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i> Memuat...
                            </div>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-row">
                            <span>Subtotal Produk</span>
                            <span id="subtotal">Rp 0</span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span id="shipping-cost">Rp 15.000</span>
                        </div>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-row summary-total">
                            <span>Total Pembayaran</span>
                            <span id="grand-total">Rp 0</span>
                        </div>
                        
                        <button onclick="placeOrder()" class="btn btn-success btn-lg btn-block" id="place-order-btn">
                            <i class="fas fa-check-circle"></i> Buat Pesanan
                        </button>
                        
                        <a href="cart.php" class="btn btn-outline btn-block">
                            <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                        </a>
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
        let selectedAddressId = null;
        let shippingCost = 15000;

        // Load cart items
        async function loadCartItems() {
            try {
                const response = await fetch(`${API_BASE}/cart.php?id_member=${memberId}`);
                const data = await response.json();
                
                if (data.status) {
                    cartData = data.data || [];
                    displayOrderItems();
                    updateOrderSummary();
                }
            } catch (error) {
                console.error('Error loading cart:', error);
            }
        }

        // Display order items
        function displayOrderItems() {
            const container = document.getElementById('order-items');
            
            if (cartData.length === 0) {
                container.innerHTML = '<p class="text-center">Keranjang kosong</p>';
                document.getElementById('place-order-btn').disabled = true;
                return;
            }
            
            container.innerHTML = cartData.map(item => `
                <div class="order-item">
                    <img src="assets/images/product-placeholder.jpg" alt="${item.nama_barang}">
                    <div class="order-item-details">
                        <div class="order-item-name">${item.nama_barang}</div>
                        <div class="order-item-qty">${item.qty}x Rp ${formatPrice(item.harga)}</div>
                    </div>
                    <div class="order-item-price">Rp ${formatPrice(item.subtotal)}</div>
                </div>
            `).join('');
        }

        // Update order summary
        function updateOrderSummary() {
            const subtotal = cartData.reduce((sum, item) => sum + item.subtotal, 0);
            const grandTotal = subtotal + shippingCost;
            
            document.getElementById('subtotal').textContent = `Rp ${formatPrice(subtotal)}`;
            document.getElementById('grand-total').textContent = `Rp ${formatPrice(grandTotal)}`;
        }

        // Load saved addresses
        async function loadAddresses() {
            try {
                const response = await fetch(`${API_BASE}/alamat.php?id_member=${memberId}`);
                const data = await response.json();
                
                if (data.status && data.data && data.data.length > 0) {
                    displayAddresses(data.data);
                    selectedAddressId = data.data[0].id;
                } else {
                    document.getElementById('saved-addresses').innerHTML = 
                        '<p class="text-muted">Belum ada alamat tersimpan</p>';
                }
            } catch (error) {
                console.error('Error loading addresses:', error);
            }
        }

        // Display addresses
        function displayAddresses(addresses) {
            const container = document.getElementById('saved-addresses');
            
            container.innerHTML = addresses.map((addr, index) => `
                <label class="address-card">
                    <input type="radio" name="address" value="${addr.id}" 
                           ${index === 0 ? 'checked' : ''}
                           onchange="selectedAddressId = ${addr.id}">
                    <div class="address-content">
                        <div class="address-header">
                            <strong>${addr.nama_penerima}</strong>
                            <span class="address-phone">${addr.no_hp}</span>
                        </div>
                        <div class="address-details">
                            ${addr.alamat}, ${addr.kota} ${addr.kode_pos || ''}
                        </div>
                    </div>
                </label>
            `).join('');
        }

        // Toggle address form
        function toggleAddressForm() {
            const form = document.getElementById('address-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        // Save new address
        async function saveAddress() {
            const formData = new FormData();
            formData.append('id_member', memberId);
            formData.append('nama_penerima', document.getElementById('nama-penerima').value);
            formData.append('no_hp', document.getElementById('no-hp').value);
            formData.append('alamat', document.getElementById('alamat').value);
            formData.append('kota', document.getElementById('kota').value);
            formData.append('kode_pos', document.getElementById('kode-pos').value);
            
            try {
                const response = await fetch(`${API_BASE}/alamat.php`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Alamat berhasil disimpan');
                    toggleAddressForm();
                    loadAddresses();
                    
                    // Clear form
                    document.getElementById('nama-penerima').value = '';
                    document.getElementById('no-hp').value = '';
                    document.getElementById('alamat').value = '';
                    document.getElementById('kota').value = '';
                    document.getElementById('kode-pos').value = '';
                } else {
                    showError(data.message || 'Gagal menyimpan alamat');
                }
            } catch (error) {
                console.error('Error saving address:', error);
                showError('Gagal menyimpan alamat');
            }
        }

        // Update shipping cost
        document.querySelectorAll('input[name="shipping"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const costs = { jne: 15000, jnt: 12000, sicepat: 10000 };
                shippingCost = costs[e.target.value] || 15000;
                document.getElementById('shipping-cost').textContent = `Rp ${formatPrice(shippingCost)}`;
                updateOrderSummary();
            });
        });

        // Place order
        async function placeOrder() {
            if (!selectedAddressId) {
                showError('Pilih alamat pengiriman terlebih dahulu');
                return;
            }
            
            if (cartData.length === 0) {
                showError('Keranjang masih kosong');
                return;
            }
            
            const items = cartData.map(item => ({
                id_barang: item.id_barang,
                qty: item.qty
            }));
            
            const formData = new FormData();
            formData.append('id_member', memberId);
            formData.append('items', JSON.stringify(items));
            
            try {
                document.getElementById('place-order-btn').disabled = true;
                document.getElementById('place-order-btn').innerHTML = 
                    '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                
                const response = await fetch(`${API_BASE}/penjualan.php`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Pesanan berhasil dibuat!');
                    setTimeout(() => {
                        window.location.href = `order-success.php?id=${data.id}`;
                    }, 1500);
                } else {
                    showError(data.message || 'Gagal membuat pesanan');
                    document.getElementById('place-order-btn').disabled = false;
                    document.getElementById('place-order-btn').innerHTML = 
                        '<i class="fas fa-check-circle"></i> Buat Pesanan';
                }
            } catch (error) {
                console.error('Error placing order:', error);
                showError('Gagal membuat pesanan');
                document.getElementById('place-order-btn').disabled = false;
                document.getElementById('place-order-btn').innerHTML = 
                    '<i class="fas fa-check-circle"></i> Buat Pesanan';
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadCartItems();
            loadAddresses();
        });
    </script>
</body>
</html>
