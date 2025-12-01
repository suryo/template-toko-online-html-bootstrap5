<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Toko Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1><i class="fas fa-shopping-bag"></i> Semua Produk</h1>
            <p>Temukan produk yang Anda butuhkan</p>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="products-layout">
                <!-- Sidebar Filters -->
                <aside class="filters-sidebar">
                    <div class="filter-card">
                        <h3><i class="fas fa-filter"></i> Filter Produk</h3>
                        
                        <!-- Category Filter -->
                        <div class="filter-group">
                            <h4>Kategori</h4>
                            <div id="category-filters">
                                <div class="loading-small">
                                    <i class="fas fa-spinner fa-spin"></i> Memuat...
                                </div>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-group">
                            <h4>Harga</h4>
                            <div class="price-inputs">
                                <input type="number" id="min-price" placeholder="Min" class="form-control">
                                <span>-</span>
                                <input type="number" id="max-price" placeholder="Max" class="form-control">
                            </div>
                            <button onclick="applyPriceFilter()" class="btn btn-sm btn-primary" style="width: 100%; margin-top: 10px;">
                                Terapkan
                            </button>
                        </div>

                        <!-- Search -->
                        <div class="filter-group">
                            <h4>Cari Produk</h4>
                            <div class="search-box">
                                <input type="text" id="search-input" placeholder="Nama produk..." class="form-control">
                                <button onclick="searchProducts()" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <button onclick="resetFilters()" class="btn btn-outline" style="width: 100%;">
                            <i class="fas fa-redo"></i> Reset Filter
                        </button>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="products-content">
                    <!-- Sort & View Options -->
                    <div class="products-toolbar">
                        <div class="products-count">
                            <span id="products-count">0 Produk</span>
                        </div>
                        <div class="sort-options">
                            <select id="sort-select" onchange="sortProducts()" class="form-control">
                                <option value="default">Urutkan</option>
                                <option value="name-asc">Nama A-Z</option>
                                <option value="name-desc">Nama Z-A</option>
                                <option value="price-asc">Harga Terendah</option>
                                <option value="price-desc">Harga Tertinggi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="products-grid" id="products-container">
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Memuat produk...
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="empty-state" style="display: none;">
                        <i class="fas fa-box-open"></i>
                        <h3>Tidak ada produk ditemukan</h3>
                        <p>Coba ubah filter atau kata kunci pencarian</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        let allProducts = [];
        let filteredProducts = [];
        let selectedCategory = null;

        // Load all products
        async function loadProducts() {
            try {
                const response = await fetch(API_BASE + '/product.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    allProducts = data.data;
                    filteredProducts = [...allProducts];
                    
                    // Check for category filter from URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const categoryId = urlParams.get('category');
                    if (categoryId) {
                        selectedCategory = parseInt(categoryId);
                        filteredProducts = allProducts.filter(p => p.id_kategori == selectedCategory);
                    }
                    
                    displayProducts();
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showError('Gagal memuat produk');
            }
        }

        // Load categories for filter
        async function loadCategories() {
            try {
                const response = await fetch(API_BASE + '/category.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    const container = document.getElementById('category-filters');
                    
                    container.innerHTML = data.data.map(category => `
                        <label class="checkbox-label">
                            <input type="checkbox" 
                                   value="${category.id}" 
                                   onchange="filterByCategory()"
                                   ${selectedCategory == category.id ? 'checked' : ''}>
                            <span>${category.nama_kategori}</span>
                        </label>
                    `).join('');
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Display products
        function displayProducts() {
            const container = document.getElementById('products-container');
            const emptyState = document.getElementById('empty-state');
            const countElement = document.getElementById('products-count');
            
            countElement.textContent = `${filteredProducts.length} Produk`;
            
            if (filteredProducts.length === 0) {
                container.style.display = 'none';
                emptyState.style.display = 'flex';
                return;
            }
            
            container.style.display = 'grid';
            emptyState.style.display = 'none';
            
            container.innerHTML = filteredProducts.map(product => `
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/product-placeholder.jpg" alt="${product.nama_barang}">
                        <div class="product-badge ${product.stok > 0 ? 'badge-success' : 'badge-danger'}">
                            ${product.stok > 0 ? 'Stok: ' + product.stok : 'Habis'}
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.nama_barang}</h3>
                        <p class="product-description">${product.deskripsi || 'Produk berkualitas'}</p>
                        <div class="product-price">Rp ${formatPrice(product.harga)}</div>
                        <div class="product-actions">
                            <a href="product-detail.php?id=${product.id}" class="btn btn-sm btn-outline">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <button onclick="addToCart(${product.id})" 
                                    class="btn btn-sm btn-primary"
                                    ${product.stok <= 0 ? 'disabled' : ''}>
                                <i class="fas fa-cart-plus"></i> Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Filter by category
        function filterByCategory() {
            const checkboxes = document.querySelectorAll('#category-filters input[type="checkbox"]');
            const selectedCategories = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => parseInt(cb.value));
            
            if (selectedCategories.length === 0) {
                filteredProducts = [...allProducts];
            } else {
                filteredProducts = allProducts.filter(p => 
                    selectedCategories.includes(p.id_kategori)
                );
            }
            
            displayProducts();
        }

        // Apply price filter
        function applyPriceFilter() {
            const minPrice = parseFloat(document.getElementById('min-price').value) || 0;
            const maxPrice = parseFloat(document.getElementById('max-price').value) || Infinity;
            
            filteredProducts = allProducts.filter(p => 
                p.harga >= minPrice && p.harga <= maxPrice
            );
            
            displayProducts();
        }

        // Search products
        function searchProducts() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            
            if (!searchTerm) {
                filteredProducts = [...allProducts];
            } else {
                filteredProducts = allProducts.filter(p => 
                    p.nama_barang.toLowerCase().includes(searchTerm) ||
                    (p.deskripsi && p.deskripsi.toLowerCase().includes(searchTerm))
                );
            }
            
            displayProducts();
        }

        // Sort products
        function sortProducts() {
            const sortValue = document.getElementById('sort-select').value;
            
            switch(sortValue) {
                case 'name-asc':
                    filteredProducts.sort((a, b) => a.nama_barang.localeCompare(b.nama_barang));
                    break;
                case 'name-desc':
                    filteredProducts.sort((a, b) => b.nama_barang.localeCompare(a.nama_barang));
                    break;
                case 'price-asc':
                    filteredProducts.sort((a, b) => a.harga - b.harga);
                    break;
                case 'price-desc':
                    filteredProducts.sort((a, b) => b.harga - a.harga);
                    break;
            }
            
            displayProducts();
        }

        // Reset filters
        function resetFilters() {
            document.querySelectorAll('#category-filters input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.getElementById('min-price').value = '';
            document.getElementById('max-price').value = '';
            document.getElementById('search-input').value = '';
            document.getElementById('sort-select').value = 'default';
            
            filteredProducts = [...allProducts];
            displayProducts();
        }

        // Search on Enter key
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('search-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    searchProducts();
                }
            });
            
            loadProducts();
            loadCategories();
        });
    </script>
</body>
</html>
