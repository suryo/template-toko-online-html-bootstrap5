<header class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h2 class="page-title" id="page-title">Dashboard</h2>
    </div>

    <div class="topbar-right">
        <div class="topbar-item">
            <button class="btn-icon" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </button>
        </div>

        <div class="topbar-item">
            <div class="user-menu">
                <button class="user-menu-btn" onclick="toggleUserMenu()">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name" id="admin-name">Admin</span>
                        <span class="user-role" id="admin-role">Administrator</span>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </button>

                <div class="user-menu-dropdown" id="user-menu-dropdown">
                    <a href="profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="settings.php" class="dropdown-item">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Toggle sidebar
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
        document.querySelector('.main-content').classList.toggle('expanded');
    }

    // Toggle user menu
    function toggleUserMenu() {
        document.getElementById('user-menu-dropdown').classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-menu')) {
            document.getElementById('user-menu-dropdown').classList.remove('show');
        }
    });

    // Load admin info
    const adminUsername = sessionStorage.getItem('admin_username') || 'Admin';
    const adminRole = sessionStorage.getItem('admin_role') || 'Administrator';
    
    document.getElementById('admin-name').textContent = adminUsername;
    document.getElementById('admin-role').textContent = adminRole;
</script>
