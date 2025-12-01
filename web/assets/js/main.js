// API Base URL
const API_BASE = 'http://localhost/belajar-web/toko-online-php/api';

// Format price to Indonesian Rupiah
function formatPrice(price) {
    return new Intl.NumberFormat('id-ID').format(price);
}

// Show success message
function showSuccess(message) {
    showToast(message, 'success');
}

// Show error message
function showError(message) {
    showToast(message, 'error');
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

// Add to cart
async function addToCart(productId, qty = 1) {
    // Check if user is logged in
    if (!isLoggedIn()) {
        showError('Silakan login terlebih dahulu');
        setTimeout(() => {
            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
        }, 1500);
        return;
    }
    
    const memberId = getMemberId();
    
    const formData = new FormData();
    formData.append('id_member', memberId);
    formData.append('id_barang', productId);
    formData.append('qty', qty);
    
    try {
        const response = await fetch(API_BASE + '/cart.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status) {
            showSuccess('Produk ditambahkan ke keranjang');
            updateCartCount();
        } else {
            showError(data.message || 'Gagal menambahkan ke keranjang');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showError('Gagal menambahkan ke keranjang');
    }
}

// Update cart count
async function updateCartCount() {
    if (!isLoggedIn()) {
        document.getElementById('cart-count').textContent = '0';
        return;
    }
    
    const memberId = getMemberId();
    
    try {
        const response = await fetch(`${API_BASE}/cart.php?id_member=${memberId}`);
        const data = await response.json();
        
        if (data.status && data.data) {
            const totalItems = data.data.reduce((sum, item) => sum + item.qty, 0);
            document.getElementById('cart-count').textContent = totalItems;
        }
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

// Check if user is logged in
function isLoggedIn() {
    // This should check session, for now we'll use a simple check
    return document.body.dataset.memberId !== undefined;
}

// Get member ID
function getMemberId() {
    return document.body.dataset.memberId || null;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Update cart count if logged in
    if (isLoggedIn()) {
        updateCartCount();
    }
    
    // Add smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

// Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate phone number
function validatePhone(phone) {
    const re = /^(\+62|62|0)[0-9]{9,12}$/;
    return re.test(phone);
}
