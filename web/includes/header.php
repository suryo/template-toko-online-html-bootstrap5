<header class="main-header">
    <div class="header-top">
        <div class="container">
            <div class="header-top-content">
                <div class="header-contact">
                    <i class="fas fa-phone"></i> +62 812-3456-7890
                    <span class="separator">|</span>
                    <i class="fas fa-envelope"></i> info@tokoonline.com
                </div>
                <div class="header-links">
                    <?php if (isset($_SESSION['member_id'])): ?>
                        <a href="profile.php"><i class="fas fa-user"></i> <?php echo $_SESSION['member_name'] ?? 'Akun Saya'; ?></a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php"><i class="fas fa-user-plus"></i> Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="header-main">
        <div class="container">
            <div class="header-main-content">
                <div class="logo">
                    <a href="index.php">
                        <i class="fas fa-store"></i>
                        <span>Toko Online</span>
                    </a>
                </div>
                
                <div class="search-bar">
                    <input type="text" id="header-search" placeholder="Cari produk..." class="search-input">
                    <button onclick="searchFromHeader()" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <div class="header-actions">
                    <a href="cart.php" class="header-action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge" id="cart-count">0</span>
                        <span class="action-label">Keranjang</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <nav class="main-nav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="products.php"><i class="fas fa-shopping-bag"></i> Produk</a></li>
                <li><a href="products.php?category=1"><i class="fas fa-tv"></i> Elektronik</a></li>
                <li><a href="products.php?category=2"><i class="fas fa-blender"></i> Peralatan RT</a></li>
                <?php if (isset($_SESSION['member_id'])): ?>
                    <li><a href="orders.php"><i class="fas fa-box"></i> Pesanan Saya</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<script>
    // Search from header
    function searchFromHeader() {
        const query = document.getElementById('header-search').value;
        if (query) {
            window.location.href = `products.php?search=${encodeURIComponent(query)}`;
        }
    }
    
    // Search on Enter
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('header-search');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    searchFromHeader();
                }
            });
        }
    });
</script>
