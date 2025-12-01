<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <h1><i class="fas fa-tags"></i> Kelola Kategori</h1>
                <button onclick="showAddModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </button>
            </div>

            <!-- Categories Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="categories-table">
                                <tr>
                                    <td colspan="3" class="text-center">
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
    <div id="category-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Tambah Kategori</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="category-form" onsubmit="saveCategory(event)">
                <div class="modal-body">
                    <input type="hidden" id="category-id">
                    
                    <div class="form-group">
                        <label>Nama Kategori *</label>
                        <input type="text" id="nama-kategori" class="form-control" required>
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
        let allCategories = [];

        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch(API_BASE + '/category.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    allCategories = data.data;
                    displayCategories(allCategories);
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                showError('Gagal memuat data kategori');
            }
        }

        // Display categories
        function displayCategories(categories) {
            const tbody = document.getElementById('categories-table');
            
            if (categories.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>';
                return;
            }
            
            tbody.innerHTML = categories.map(category => `
                <tr>
                    <td>${category.id}</td>
                    <td>${category.nama_kategori}</td>
                    <td>
                        <button onclick="editCategory(${category.id})" class="btn btn-xs btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCategory(${category.id})" class="btn btn-xs btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Show add modal
        function showAddModal() {
            document.getElementById('modal-title').textContent = 'Tambah Kategori';
            document.getElementById('category-form').reset();
            document.getElementById('category-id').value = '';
            document.getElementById('category-modal').classList.add('show');
        }

        // Edit category
        function editCategory(id) {
            const category = allCategories.find(c => c.id === id);
            if (!category) return;
            
            document.getElementById('modal-title').textContent = 'Edit Kategori';
            document.getElementById('category-id').value = category.id;
            document.getElementById('nama-kategori').value = category.nama_kategori;
            
            document.getElementById('category-modal').classList.add('show');
        }

        // Save category
        async function saveCategory(e) {
            e.preventDefault();
            
            const id = document.getElementById('category-id').value;
            const formData = new FormData();
            
            if (id) {
                formData.append('_method', 'PUT');
            }
            
            formData.append('nama_kategori', document.getElementById('nama-kategori').value);
            
            try {
                const url = id ? `${API_BASE}/category.php?id=${id}` : `${API_BASE}/category.php`;
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess(id ? 'Kategori berhasil diupdate' : 'Kategori berhasil ditambahkan');
                    closeModal();
                    loadCategories();
                } else {
                    showError(data.message || 'Gagal menyimpan kategori');
                }
            } catch (error) {
                console.error('Error saving category:', error);
                showError('Gagal menyimpan kategori');
            }
        }

        // Delete category
        async function deleteCategory(id) {
            if (!confirm('Yakin ingin menghapus kategori ini?')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`${API_BASE}/category.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Kategori berhasil dihapus');
                    loadCategories();
                } else {
                    showError(data.message || 'Gagal menghapus kategori');
                }
            } catch (error) {
                console.error('Error deleting category:', error);
                showError('Gagal menghapus kategori');
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('category-modal').classList.remove('show');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadCategories();
        });
    </script>
</body>
</html>
