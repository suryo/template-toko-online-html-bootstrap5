<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <h1><i class="fas fa-shopping-cart"></i> Kelola Pesanan</h1>
            </div>

            <!-- Filters -->
            <div class="card mb-20">
                <div class="card-body">
                    <div class="filters-row">
                        <div class="filter-item">
                            <input type="text" id="search" placeholder="Cari ID Pesanan / Member..." class="form-control">
                        </div>
                        <div class="filter-item">
                            <select id="status-filter" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="shipped">Shipped</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Member</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table">
                                <tr>
                                    <td colspan="6" class="text-center">
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

    <!-- Order Detail Modal -->
    <div id="order-modal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3>Detail Pesanan #<span id="order-id-display"></span></h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-row mb-20">
                    <div>
                        <strong>Member:</strong> <span id="detail-member"></span><br>
                        <strong>Tanggal:</strong> <span id="detail-date"></span>
                    </div>
                    <div class="text-right" style="text-align: right;">
                        <strong>Status:</strong> <span id="detail-status" class="badge"></span>
                    </div>
                </div>

                <h4>Item Pesanan</h4>
                <div class="table-responsive mb-20">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-table"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align: right;">Total:</th>
                                <th id="detail-total"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group">
                    <label>Update Status</label>
                    <select id="update-status" class="form-control" onchange="updateOrderStatus()">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="shipped">Shipped</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="assets/js/admin.js"></script>
    <script>
        let allOrders = [];
        let currentOrderId = null;

        // Load orders
        async function loadOrders() {
            try {
                const response = await fetch(API_BASE + '/penjualan.php');
                const data = await response.json();
                
                if (data.status && data.data) {
                    allOrders = data.data;
                    displayOrders(allOrders);
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                showError('Gagal memuat data pesanan');
            }
        }

        // Display orders
        function displayOrders(orders) {
            const tbody = document.getElementById('orders-table');
            
            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td>#${order.id}</td>
                    <td>${order.nama_member || 'N/A'}</td>
                    <td>${formatDate(order.tanggal)}</td>
                    <td>Rp ${formatPrice(order.total)}</td>
                    <td><span class="badge badge-${getStatusColor(order.status)}">${order.status}</span></td>
                    <td>
                        <button onclick="viewOrder(${order.id})" class="btn btn-xs btn-info" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${order.status === 'pending' ? `
                        <button onclick="cancelOrder(${order.id})" class="btn btn-xs btn-danger" title="Batalkan">
                            <i class="fas fa-times"></i>
                        </button>` : ''}
                    </td>
                </tr>
            `).join('');
        }

        // View order detail
        async function viewOrder(id) {
            currentOrderId = id;
            const order = allOrders.find(o => o.id === id);
            if (!order) return;
            
            document.getElementById('order-id-display').textContent = order.id;
            document.getElementById('detail-member').textContent = order.nama_member || 'N/A';
            document.getElementById('detail-date').textContent = formatDate(order.tanggal);
            document.getElementById('detail-status').textContent = order.status;
            document.getElementById('detail-status').className = `badge badge-${getStatusColor(order.status)}`;
            document.getElementById('detail-total').textContent = `Rp ${formatPrice(order.total)}`;
            document.getElementById('update-status').value = order.status;
            
            // Load items (simulated since API might not return items in list)
            // In a real app, you might need a separate endpoint for order details
            // For now, we'll assume we need to fetch details or use what we have
            // Since the current API structure for list might not include items, let's try to fetch detail
            
            try {
                // Assuming we can get details by ID or filter from list if items are included
                // If items are not in the list response, we'd need a specific endpoint
                // Let's assume for now we just show basic info or fetch again
                
                // If the API supports getting a single order with items:
                // const response = await fetch(`${API_BASE}/penjualan.php?id=${id}`);
                // const data = await response.json();
                
                // Since our current API implementation for GET /penjualan.php returns all orders
                // and might not include items in the list view, we'll check if items are present
                
                const tbody = document.getElementById('order-items-table');
                if (order.items && order.items.length > 0) {
                    tbody.innerHTML = order.items.map(item => `
                        <tr>
                            <td>${item.nama_barang}</td>
                            <td>Rp ${formatPrice(item.harga)}</td>
                            <td>${item.qty}</td>
                            <td>Rp ${formatPrice(item.subtotal)}</td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Detail item tidak tersedia</td></tr>';
                }
                
                document.getElementById('order-modal').classList.add('show');
            } catch (error) {
                console.error('Error loading order details:', error);
            }
        }

        // Update order status
        async function updateOrderStatus() {
            const newStatus = document.getElementById('update-status').value;
            if (!currentOrderId) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('status', newStatus);
                
                const response = await fetch(`${API_BASE}/penjualan.php?id=${currentOrderId}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Status pesanan diperbarui');
                    document.getElementById('detail-status').textContent = newStatus;
                    document.getElementById('detail-status').className = `badge badge-${getStatusColor(newStatus)}`;
                    loadOrders(); // Refresh list
                } else {
                    showError(data.message || 'Gagal memperbarui status');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showError('Gagal memperbarui status');
            }
        }

        // Cancel order
        async function cancelOrder(id) {
            if (!confirm('Yakin ingin membatalkan pesanan ini?')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('status', 'cancelled');
                
                const response = await fetch(`${API_BASE}/penjualan.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Pesanan dibatalkan');
                    loadOrders();
                } else {
                    showError(data.message || 'Gagal membatalkan pesanan');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                showError('Gagal membatalkan pesanan');
            }
        }

        // Get status color
        function getStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'paid': 'info',
                'shipped': 'primary',
                'completed': 'success',
                'cancelled': 'danger'
            };
            return colors[status] || 'secondary';
        }

        // Close modal
        function closeModal() {
            document.getElementById('order-modal').classList.remove('show');
        }

        // Filters
        document.getElementById('search').addEventListener('input', filterOrders);
        document.getElementById('status-filter').addEventListener('change', filterOrders);

        function filterOrders() {
            const search = document.getElementById('search').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            
            let filtered = allOrders;
            
            if (search) {
                filtered = filtered.filter(o => 
                    o.id.toString().includes(search) ||
                    (o.nama_member && o.nama_member.toLowerCase().includes(search))
                );
            }
            
            if (statusFilter) {
                filtered = filtered.filter(o => o.status === statusFilter);
            }
            
            displayOrders(filtered);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadOrders();
        });
    </script>
</body>
</html>
