<?php
session_start();
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Toko Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Product Detail Section -->
    <section class="product-detail-section">
        <div class="container">
            <div id="product-detail" class="loading-container">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Memuat detail produk...
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews-section">
        <div class="container">
            <div class="reviews-container">
                <div class="reviews-header">
                    <h2><i class="fas fa-star"></i> Ulasan Produk</h2>
                    <div id="rating-summary"></div>
                </div>
                
                <div id="reviews-list" class="reviews-list">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i> Memuat ulasan...
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="related-products">
        <div class="container">
            <h2 class="section-title">Produk Terkait</h2>
            <div class="products-grid" id="related-products">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Memuat produk terkait...
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        const productId = <?php echo $product_id; ?>;
        let currentProduct = null;

        // Load product detail
        async function loadProductDetail() {
            try {
                const response = await fetch(`${API_BASE}/product.php?id=${productId}`);
                const data = await response.json();
                
                if (data.status && data.data) {
                    currentProduct = data.data;
                    displayProductDetail(data.data);
                } else {
                    showError('Produk tidak ditemukan');
                }
            } catch (error) {
                console.error('Error loading product:', error);
                showError('Gagal memuat detail produk');
            }
        }

        // Display product detail
        function displayProductDetail(product) {
            const container = document.getElementById('product-detail');
            
            container.innerHTML = `
                <div class="product-detail-grid">
                    <div class="product-gallery">
                        <div class="main-image">
                            <img src="assets/images/product-placeholder.jpg" alt="${product.nama_barang}">
                        </div>
                    </div>
                    
                    <div class="product-details">
                        <div class="breadcrumb">
                            <a href="index.php">Home</a>
                            <i class="fas fa-chevron-right"></i>
                            <a href="products.php">Produk</a>
                            <i class="fas fa-chevron-right"></i>
                            <span>${product.nama_barang}</span>
                        </div>
                        
                        <h1 class="product-name">${product.nama_barang}</h1>
                        
                        <div class="product-rating" id="product-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="rating-text">Memuat rating...</span>
                        </div>
                        
                        <div class="product-price-section">
                            <div class="price-main">Rp ${formatPrice(product.harga)}</div>
                        </div>
                        
                        <div class="product-stock">
                            <i class="fas fa-box"></i>
                            <span>Stok: <strong>${product.stok}</strong></span>
                        </div>
                        
                        <div class="product-description">
                            <h3>Deskripsi Produk</h3>
                            <p>${product.deskripsi || 'Produk berkualitas dengan harga terbaik'}</p>
                        </div>
                        
                        <div class="product-actions-section">
                            <div class="quantity-selector">
                                <label>Jumlah:</label>
                                <div class="quantity-controls">
                                    <button onclick="decreaseQty()" class="qty-btn">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="qty-input" value="1" min="1" max="${product.stok}" class="qty-input">
                                    <button onclick="increaseQty()" class="qty-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <button onclick="addToCartWithQty()" class="btn btn-primary btn-lg" ${product.stok <= 0 ? 'disabled' : ''}>
                                    <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                </button>
                                <button onclick="buyNow()" class="btn btn-success btn-lg" ${product.stok <= 0 ? 'disabled' : ''}>
                                    <i class="fas fa-shopping-bag"></i> Beli Sekarang
                                </button>
                            </div>
                        </div>
                        
                        <div class="product-meta">
                            <div class="meta-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Garansi Resmi</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-truck"></i>
                                <span>Gratis Ongkir</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-undo"></i>
                                <span>Bisa Retur</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Load reviews
        async function loadReviews() {
            try {
                const response = await fetch(`${API_BASE}/review.php?id_barang=${productId}`);
                const data = await response.json();
                
                if (data.status) {
                    displayRatingSummary(data.average_rating, data.total_reviews);
                    displayReviews(data.data);
                }
            } catch (error) {
                console.error('Error loading reviews:', error);
            }
        }

        // Display rating summary
        function displayRatingSummary(avgRating, totalReviews) {
            const summaryContainer = document.getElementById('rating-summary');
            const productRating = document.getElementById('product-rating');
            
            const stars = generateStars(avgRating);
            
            summaryContainer.innerHTML = `
                <div class="rating-stats">
                    <div class="avg-rating">${avgRating.toFixed(1)}</div>
                    <div class="rating-details">
                        <div class="stars">${stars}</div>
                        <div class="total-reviews">${totalReviews} ulasan</div>
                    </div>
                </div>
            `;
            
            productRating.innerHTML = `
                <div class="stars">${stars}</div>
                <span class="rating-text">${avgRating.toFixed(1)} (${totalReviews} ulasan)</span>
            `;
        }

        // Display reviews
        function displayReviews(reviews) {
            const container = document.getElementById('reviews-list');
            
            if (!reviews || reviews.length === 0) {
                container.innerHTML = `
                    <div class="empty-reviews">
                        <i class="fas fa-comment-slash"></i>
                        <p>Belum ada ulasan untuk produk ini</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = reviews.map(review => `
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="reviewer-name">${review.nama_member}</div>
                                <div class="review-date">${formatDate(review.tanggal_review)}</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            ${generateStars(review.rating)}
                        </div>
                    </div>
                    <div class="review-content">
                        <p>${review.komentar}</p>
                    </div>
                </div>
            `).join('');
        }

        // Load related products
        async function loadRelatedProducts() {
            if (!currentProduct) return;
            
            try {
                const response = await fetch(`${API_BASE}/product.php`);
                const data = await response.json();
                
                if (data.status && data.data) {
                    const related = data.data
                        .filter(p => p.id !== productId && p.id_kategori === currentProduct.id_kategori)
                        .slice(0, 4);
                    
                    const container = document.getElementById('related-products');
                    
                    if (related.length === 0) {
                        container.innerHTML = '<p class="text-center">Tidak ada produk terkait</p>';
                        return;
                    }
                    
                    container.innerHTML = related.map(product => `
                        <div class="product-card">
                            <div class="product-image">
                                <img src="assets/images/product-placeholder.jpg" alt="${product.nama_barang}">
                                <div class="product-badge">Stok: ${product.stok}</div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">${product.nama_barang}</h3>
                                <p class="product-description">${product.deskripsi || 'Produk berkualitas'}</p>
                                <div class="product-price">Rp ${formatPrice(product.harga)}</div>
                                <div class="product-actions">
                                    <a href="product-detail.php?id=${product.id}" class="btn btn-sm btn-outline">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <button onclick="addToCart(${product.id})" class="btn btn-sm btn-primary">
                                        <i class="fas fa-cart-plus"></i> Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error loading related products:', error);
            }
        }

        // Quantity controls
        function increaseQty() {
            const input = document.getElementById('qty-input');
            const max = parseInt(input.max);
            const current = parseInt(input.value);
            if (current < max) {
                input.value = current + 1;
            }
        }

        function decreaseQty() {
            const input = document.getElementById('qty-input');
            const current = parseInt(input.value);
            if (current > 1) {
                input.value = current - 1;
            }
        }

        // Add to cart with quantity
        function addToCartWithQty() {
            const qty = parseInt(document.getElementById('qty-input').value);
            addToCart(productId, qty);
        }

        // Buy now
        function buyNow() {
            const qty = parseInt(document.getElementById('qty-input').value);
            addToCart(productId, qty);
            setTimeout(() => {
                window.location.href = 'cart.php';
            }, 500);
        }

        // Generate stars HTML
        function generateStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    stars += '<i class="fas fa-star"></i>';
                } else if (i - 0.5 <= rating) {
                    stars += '<i class="fas fa-star-half-alt"></i>';
                } else {
                    stars += '<i class="far fa-star"></i>';
                }
            }
            return stars;
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', async () => {
            await loadProductDetail();
            loadReviews();
            loadRelatedProducts();
        });
    </script>
</body>
</html>
