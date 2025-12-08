<?php
// Get base URL for admin panel
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . '/template-toko-online-html-bootstrap5/admin/';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-store"></i>
            <span>Toko Online</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo $base_url; ?>dashboard.php" class="nav-item active">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section">
            <div class="nav-section-title">Master Data</div>

            <a href="<?php echo $base_url; ?>products.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>

            <a href="<?php echo $base_url; ?>categories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>

            <a href="<?php echo $base_url; ?>members.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Member</span>
            </a>

            <a href="<?php echo $base_url; ?>suppliers.php" class="nav-item">
                <i class="fas fa-truck"></i>
                <span>Supplier</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Transaksi</div>

            <a href="<?php echo $base_url; ?>orders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Pesanan</span>
            </a>

            <a href="<?php echo $base_url; ?>purchases.php" class="nav-item">
                <i class="fas fa-shopping-bag"></i>
                <span>Pembelian</span>
            </a>

            <a href="<?php echo $base_url; ?>payments.php" class="nav-item">
                <i class="fas fa-credit-card"></i>
                <span>Pembayaran</span>
            </a>

            <a href="<?php echo $base_url; ?>shipments.php" class="nav-item">
                <i class="fas fa-shipping-fast"></i>
                <span>Pengiriman</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Marketing</div>

            <a href="<?php echo $base_url; ?>discounts.php" class="nav-item">
                <i class="fas fa-percent"></i>
                <span>Diskon</span>
            </a>

            <a href="<?php echo $base_url; ?>voucher/index.php" class="nav-item">
                <i class="fas fa-ticket-alt"></i>
                <span>Voucher</span>
            </a>

            <a href="<?php echo $base_url; ?>reviews.php" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Ulasan</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Laporan</div>

            <a href="<?php echo $base_url; ?>reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Laporan Penjualan</span>
            </a>

            <a href="<?php echo $base_url; ?>stock-report.php" class="nav-item">
                <i class="fas fa-warehouse"></i>
                <span>Laporan Stok</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="<?php echo $base_url; ?>logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<script>
    // Set active menu based on current URL
    const currentUrl = window.location.href;
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.href === currentUrl || currentUrl.includes(item.getAttribute('href'))) {
            item.classList.add('active');
        }
    });
</script>