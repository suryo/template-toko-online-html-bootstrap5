<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-store"></i>
            <span>Toko Online</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item active">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section">
            <div class="nav-section-title">Master Data</div>
            
            <a href="products.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>

            <a href="categories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>

            <a href="members.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Member</span>
            </a>

            <a href="suppliers.php" class="nav-item">
                <i class="fas fa-truck"></i>
                <span>Supplier</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Transaksi</div>
            
            <a href="orders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Pesanan</span>
            </a>

            <a href="purchases.php" class="nav-item">
                <i class="fas fa-shopping-bag"></i>
                <span>Pembelian</span>
            </a>

            <a href="payments.php" class="nav-item">
                <i class="fas fa-credit-card"></i>
                <span>Pembayaran</span>
            </a>

            <a href="shipments.php" class="nav-item">
                <i class="fas fa-shipping-fast"></i>
                <span>Pengiriman</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Marketing</div>
            
            <a href="discounts.php" class="nav-item">
                <i class="fas fa-percent"></i>
                <span>Diskon</span>
            </a>

            <a href="vouchers.php" class="nav-item">
                <i class="fas fa-ticket-alt"></i>
                <span>Voucher</span>
            </a>

            <a href="reviews.php" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Ulasan</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Laporan</div>
            
            <a href="reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan Penjualan</span>
            </a>

            <a href="stock-report.php" class="nav-item">
                <i class="fas fa-warehouse"></i>
                <span>Laporan Stok</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<script>
    // Set active menu
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
        }
    });
</script>
