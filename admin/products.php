<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <h1><i class="fas fa-box"></i> Kelola Produk</h1>
                <button onclick="showAddModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Produk
                </button>
            </div>

            <!-- Filters -->
            <div class="card mb-20">
                <div class="card-body">
                    <div class="filters-row">
                        <div class="filter-item">
                            <input type="text" id="search" placeholder="Cari produk..." class="form-control">
                        </div>
                        <div class="filter-item">
                            <select id="category-filter" class="form-control">
                                <option value="">Semua Kategori</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <select id="stock-filter" class="form-control">
                                <option value="">Semua Stok</option>
                                <option value="low">Stok Menipis (< 10)</option>
                                <option value="out">Stok Habis</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="products-table">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Tambah Produk</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="product-form" onsubmit="saveProduct(event)">
                <div class="modal-body">
                    <input type="hidden" id="product-id">
                    
                    <div class="form-group">
                        <label>Nama Produk *</label>
                        <input type="text" id="nama-barang" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori *</label>
                        <select id="id-kategori" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Harga *</label>
                            <input type="number" id="harga" class="form-control" required min="0">
                        </div>

                        <div class="form-group">
                            <label>Stok *</label>
                            <input type="number" id="stok" class="form-control" required min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea id="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="assets/js/admin.js"></script>
    <script>
        let allProducts = [];
        let categories = [];

        // Load products
        async function loadProducts() {
            try {
                const response = await fetch(API_BASE + '/product.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    allProducts = data.data;
                    displayProducts(allProducts);
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showError('Gagal memuat data produk');
            }
        }

        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch(API_BASE + '/category.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    categories = data.data;
                    
                    // Populate category selects
                    const categorySelect = document.getElementById('id-kategori');
                    const filterSelect = document.getElementById('category-filter');
                    
                    categories.forEach(cat => {
                        categorySelect.innerHTML += `<option value="${cat.id}">${cat.nama_kategori}</option>`;
                        filterSelect.innerHTML += `<option value="${cat.id}">${cat.nama_kategori}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Display products
        function displayProducts(products) {
            const tbody = document.getElementById('products-table');
            
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => {
                const category = categories.find(c => c.id == product.id_kategori);
                const stockClass = product.stok < 10 ? 'badge-danger' : product.stok < 50 ? 'badge-warning' : 'badge-success';
                
                return `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.nama_barang}</td>
                        <td>${category ? category.nama_kategori : 'N/A'}</td>
                        <td>Rp ${formatPrice(product.harga)}</td>
                        <td><span class="badge ${stockClass}">${product.stok}</span></td>
                        <td>${product.deskripsi || '-'}</td>
                        <td>
                            <button onclick="editProduct(${product.id})" class="btn btn-xs btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteProduct(${product.id})" class="btn btn-xs btn-danger" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Show add modal
        function showAddModal() {
            document.getElementById('modal-title').textContent = 'Tambah Produk';
            document.getElementById('product-form').reset();
            document.getElementById('product-id').value = '';
            document.getElementById('product-modal').classList.add('show');
        }

        // Edit product
        async function editProduct(id) {
            const product = allProducts.find(p => p.id === id);
            if (!product) return;
            
            document.getElementById('modal-title').textContent = 'Edit Produk';
            document.getElementById('product-id').value = product.id;
            document.getElementById('nama-barang').value = product.nama_barang;
            document.getElementById('id-kategori').value = product.id_kategori;
            document.getElementById('harga').value = product.harga;
            document.getElementById('stok').value = product.stok;
            document.getElementById('deskripsi').value = product.deskripsi || '';
            
            document.getElementById('product-modal').classList.add('show');
        }

        // Save product
        async function saveProduct(e) {
            e.preventDefault();
            
            const id = document.getElementById('product-id').value;
            const formData = new FormData();
            
            if (id) {
                formData.append('_method', 'PUT');
            }
            
            formData.append('nama_barang', document.getElementById('nama-barang').value);
            formData.append('id_kategori', document.getElementById('id-kategori').value);
            formData.append('harga', document.getElementById('harga').value);
            formData.append('stok', document.getElementById('stok').value);
            formData.append('deskripsi', document.getElementById('deskripsi').value);
            
            try {
                const url = id ? `${API_BASE}/product.php?id=${id}` : `${API_BASE}/product.php`;
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess(id ? 'Produk berhasil diupdate' : 'Produk berhasil ditambahkan');
                    closeModal();
                    loadProducts();
                } else {
                    showError(data.message || 'Gagal menyimpan produk');
                }
            } catch (error) {
                console.error('Error saving product:', error);
                showError('Gagal menyimpan produk');
            }
        }

        // Delete product
        async function deleteProduct(id) {
            if (!confirm('Yakin ingin menghapus produk ini?')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`${API_BASE}/product.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Produk berhasil dihapus');
                    loadProducts();
                } else {
                    showError(data.message || 'Gagal menghapus produk');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showError('Gagal menghapus produk');
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('product-modal').classList.remove('show');
        }

        // Filters
        document.getElementById('search').addEventListener('input', filterProducts);
        document.getElementById('category-filter').addEventListener('change', filterProducts);
        document.getElementById('stock-filter').addEventListener('change', filterProducts);

        function filterProducts() {
            const search = document.getElementById('search').value.toLowerCase();
            const categoryFilter = document.getElementById('category-filter').value;
            const stockFilter = document.getElementById('stock-filter').value;
            
            let filtered = allProducts;
            
            if (search) {
                filtered = filtered.filter(p => 
                    p.nama_barang.toLowerCase().includes(search) ||
                    (p.deskripsi && p.deskripsi.toLowerCase().includes(search))
                );
            }
            
            if (categoryFilter) {
                filtered = filtered.filter(p => p.id_kategori == categoryFilter);
            }
            
            if (stockFilter === 'low') {
                filtered = filtered.filter(p => p.stok < 10 && p.stok > 0);
            } else if (stockFilter === 'out') {
                filtered = filtered.filter(p => p.stok === 0);
            }
            
            displayProducts(filtered);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadCategories();
            loadProducts();
        });
    </script>
</body>
</html>
