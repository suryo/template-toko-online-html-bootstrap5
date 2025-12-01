// API Base URL
const API_BASE = 'http://localhost/belajar-web/toko-online-php/api';

// Format price to Indonesian Rupiah
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID').format(price);
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('id-ID', options);
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast toast-${type} show`;
    
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000);
}

function showSuccess(message) {
    showToast(message, 'success');
}

function showError(message) {
    showToast(message, 'error');
}

// Check authentication
function checkAuth() {
    if (!sessionStorage.getItem('admin_id') && !window.location.pathname.includes('index.php')) {
        window.location.href = 'index.php';
    }
}

// Logout
function logout() {
    sessionStorage.clear();
    window.location.href = 'index.php';
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    // Check auth on every page load (except login)
    if (!window.location.pathname.includes('index.php')) {
        checkAuth();
    }
    
    // Add logout handler
    const logoutLinks = document.querySelectorAll('a[href="logout.php"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            logout();
        });
    });
});
