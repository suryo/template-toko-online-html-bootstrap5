<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online - Belanja Mudah & Terpercaya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Selamat Datang di Toko Online</h1>
            <p class="hero-subtitle">Belanja produk berkualitas dengan harga terbaik</p>
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag"></i> Mulai Belanja
            </a>
        </div>
        <div class="hero-image">
            <i class="fas fa-shopping-cart"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Mengapa Belanja di Sini?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Pengiriman Cepat</h3>
                    <p>Pengiriman ke seluruh Indonesia dengan berbagai pilihan kurir</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Pembayaran Aman</h3>
                    <p>Transaksi dijamin aman dengan berbagai metode pembayaran</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Customer Service 24/7</h3>
                    <p>Tim support siap membantu Anda kapan saja</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>Harga Terbaik</h3>
                    <p>Dapatkan produk berkualitas dengan harga yang kompetitif</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <h2 class="section-title">Produk Unggulan</h2>
            <div class="products-grid" id="featured-products">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Memuat produk...
                </div>
            </div>
            <div class="text-center" style="margin-top: 30px;">
                <a href="products.php" class="btn btn-outline">
                    Lihat Semua Produk <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Kategori Produk</h2>
            <div class="categories-grid" id="categories-list">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Memuat kategori...
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        // Load featured products
        async function loadFeaturedProducts() {
            try {
                const response = await fetch(API_BASE + '/product.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    const products = data.data.slice(0, 4); // Show only 4 products
                    const container = document.getElementById('featured-products');
                    
                    container.innerHTML = products.map(product => `
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
                console.error('Error loading products:', error);
                document.getElementById('featured-products').innerHTML = 
                    '<p class="error-message">Gagal memuat produk</p>';
            }
        }

        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch(API_BASE + '/category.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    const container = document.getElementById('categories-list');
                    
                    container.innerHTML = data.data.map(category => `
                        <a href="products.php?category=${category.id}" class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <h3>${category.nama_kategori}</h3>
                        </a>
                    `).join('');
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                document.getElementById('categories-list').innerHTML = 
                    '<p class="error-message">Gagal memuat kategori</p>';
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadFeaturedProducts();
            loadCategories();
        });
    </script>
</body>
</html>
