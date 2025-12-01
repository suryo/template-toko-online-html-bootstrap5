<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_username'])) {
    // Check sessionStorage via JavaScript (will be handled in script)
    ?>
    <script>
        if (!sessionStorage.getItem('admin_id')) {
            window.location.href = 'index.php';
        }
    </script>
    <?php
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <?php include 'includes/topbar.php'; ?>

        <!-- Dashboard Content -->
        <div class="content-wrapper">
            <div class="page-header">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <p>Selamat datang di Admin Panel Toko Online</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-details">
                        <h3 id="total-orders">0</h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-details">
                        <h3 id="total-products">0</h3>
                        <p>Total Produk</p>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h3 id="total-members">0</h3>
                        <p>Total Member</p>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-details">
                        <h3 id="total-revenue">Rp 0</h3>
                        <p>Total Pendapatan</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-row">
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> Pesanan Terbaru</h3>
                        <a href="orders.php" class="btn btn-sm btn-outline">Lihat Semua</a>
                    </div>
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
                                    </tr>
                                </thead>
                                <tbody id="recent-orders">
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

                <!-- Low Stock Products -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> Stok Menipis</h3>
                        <a href="products.php" class="btn btn-sm btn-outline">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="low-stock-products">
                                    <tr>
                                        <td colspan="4" class="text-center">
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
    </div>

    <script src="assets/js/admin.js"></script>
    <script>
        // Load dashboard data
        async function loadDashboardData() {
            try {
                // Load total orders
                const ordersRes = await fetch(API_BASE + '/penjualan.php');
                const ordersData = await ordersRes.json();
                if (ordersData.status && ordersData.data) {
                    document.getElementById('total-orders').textContent = ordersData.data.length;
                    displayRecentOrders(ordersData.data.slice(0, 5));
                    
                    // Calculate revenue
                    const revenue = ordersData.data.reduce((sum, order) => sum + parseFloat(order.total), 0);
                    document.getElementById('total-revenue').textContent = 'Rp ' + formatPrice(revenue);
                }

                // Load total products
                const productsRes = await fetch(API_BASE + '/product.php');
                const productsData = await productsRes.json();
                if (productsData.status && productsData.data) {
                    document.getElementById('total-products').textContent = productsData.data.length;
                    
                    // Find low stock products
                    const lowStock = productsData.data.filter(p => p.stok < 10).slice(0, 5);
                    displayLowStockProducts(lowStock);
                }

                // Load total members
                const membersRes = await fetch(API_BASE + '/member.php');
                const membersData = await membersRes.json();
                if (membersData.status && membersData.data) {
                    document.getElementById('total-members').textContent = membersData.data.length;
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }

        // Display recent orders
        function displayRecentOrders(orders) {
            const tbody = document.getElementById('recent-orders');
            
            if (orders.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Belum ada pesanan</td></tr>';
                return;
            }
            
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td>#${order.id}</td>
                    <td>${order.nama_member || 'N/A'}</td>
                    <td>${formatDate(order.tanggal)}</td>
                    <td>Rp ${formatPrice(order.total)}</td>
                    <td><span class="badge badge-${getStatusColor(order.status)}">${order.status}</span></td>
                </tr>
            `).join('');
        }

        // Display low stock products
        function displayLowStockProducts(products) {
            const tbody = document.getElementById('low-stock-products');
            
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Semua produk stok aman</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => `
                <tr>
                    <td>${product.nama_barang}</td>
                    <td>Kategori ${product.id_kategori}</td>
                    <td><span class="badge badge-danger">${product.stok}</span></td>
                    <td>
                        <a href="products.php?edit=${product.id}" class="btn btn-xs btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            `).join('');
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

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadDashboardData();
        });
    </script>
</body>
</html>
