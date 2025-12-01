<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Supplier - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <h1><i class="fas fa-truck"></i> Kelola Supplier</h1>
                <button onclick="showAddModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Supplier
                </button>
            </div>

            <!-- Suppliers Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>No HP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="suppliers-table">
                                <tr>
                                    <td colspan="5" class="text-center">
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
    <div id="supplier-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Tambah Supplier</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="supplier-form" onsubmit="saveSupplier(event)">
                <div class="modal-body">
                    <input type="hidden" id="supplier-id">
                    
                    <div class="form-group">
                        <label>Nama Supplier *</label>
                        <input type="text" id="nama-supplier" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea id="alamat" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>No HP</label>
                        <input type="text" id="no-hp" class="form-control">
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
        let allSuppliers = [];

        // Load suppliers
        async function loadSuppliers() {
            try {
                const response = await fetch(API_BASE + '/supplier.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    allSuppliers = data.data;
                    displaySuppliers(allSuppliers);
                }
            } catch (error) {
                console.error('Error loading suppliers:', error);
                showError('Gagal memuat data supplier');
            }
        }

        // Display suppliers
        function displaySuppliers(suppliers) {
            const tbody = document.getElementById('suppliers-table');
            
            if (suppliers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
                return;
            }
            
            tbody.innerHTML = suppliers.map(supplier => `
                <tr>
                    <td>${supplier.id}</td>
                    <td>${supplier.nama_supplier}</td>
                    <td>${supplier.alamat || '-'}</td>
                    <td>${supplier.no_hp || '-'}</td>
                    <td>
                        <button onclick="editSupplier(${supplier.id})" class="btn btn-xs btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteSupplier(${supplier.id})" class="btn btn-xs btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Show add modal
        function showAddModal() {
            document.getElementById('modal-title').textContent = 'Tambah Supplier';
            document.getElementById('supplier-form').reset();
            document.getElementById('supplier-id').value = '';
            document.getElementById('supplier-modal').classList.add('show');
        }

        // Edit supplier
        function editSupplier(id) {
            const supplier = allSuppliers.find(s => s.id === id);
            if (!supplier) return;
            
            document.getElementById('modal-title').textContent = 'Edit Supplier';
            document.getElementById('supplier-id').value = supplier.id;
            document.getElementById('nama-supplier').value = supplier.nama_supplier;
            document.getElementById('alamat').value = supplier.alamat || '';
            document.getElementById('no-hp').value = supplier.no_hp || '';
            
            document.getElementById('supplier-modal').classList.add('show');
        }

        // Save supplier
        async function saveSupplier(e) {
            e.preventDefault();
            
            const id = document.getElementById('supplier-id').value;
            const formData = new FormData();
            
            if (id) {
                formData.append('_method', 'PUT');
            }
            
            formData.append('nama_supplier', document.getElementById('nama-supplier').value);
            formData.append('alamat', document.getElementById('alamat').value);
            formData.append('no_hp', document.getElementById('no-hp').value);
            
            try {
                const url = id ? `${API_BASE}/supplier.php?id=${id}` : `${API_BASE}/supplier.php`;
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess(id ? 'Supplier berhasil diupdate' : 'Supplier berhasil ditambahkan');
                    closeModal();
                    loadSuppliers();
                } else {
                    showError(data.message || 'Gagal menyimpan supplier');
                }
            } catch (error) {
                console.error('Error saving supplier:', error);
                showError('Gagal menyimpan supplier');
            }
        }

        // Delete supplier
        async function deleteSupplier(id) {
            if (!confirm('Yakin ingin menghapus supplier ini?')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`${API_BASE}/supplier.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Supplier berhasil dihapus');
                    loadSuppliers();
                } else {
                    showError(data.message || 'Gagal menghapus supplier');
                }
            } catch (error) {
                console.error('Error deleting supplier:', error);
                showError('Gagal menghapus supplier');
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('supplier-modal').classList.remove('show');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadSuppliers();
        });
    </script>
</body>
</html>
