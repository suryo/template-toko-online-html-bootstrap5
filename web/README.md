# Web Frontend - Toko Online

Frontend web application untuk Toko Online menggunakan vanilla PHP, HTML, CSS, dan JavaScript.

## 📁 Struktur Folder

```
web/
├── index.php              # Homepage
├── products.php           # Halaman daftar produk
├── product-detail.php     # Halaman detail produk
├── cart.php               # Halaman keranjang belanja
├── checkout.php           # Halaman checkout
├── login.php              # Halaman login
├── includes/
│   ├── header.php         # Header component
│   └── footer.php         # Footer component
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   └── main.js        # Main JavaScript
│   └── images/
│       └── product-placeholder.jpg
└── README.md              # This file
```

## 🚀 Fitur

### ✅ Halaman yang Sudah Dibuat:

1. **Homepage (`index.php`)**
   - Hero section
   - Features section
   - Featured products (4 produk)
   - Categories grid
   - Fully responsive

2. **Products Page (`products.php`)**
   - Filter by category
   - Filter by price range
   - Search functionality
   - Sort options (name, price)
   - Product grid with pagination
   - Responsive sidebar filters

3. **Product Detail (`product-detail.php`)**
   - Product gallery
   - Product information
   - Price & stock display
   - Quantity selector
   - Add to cart / Buy now
   - Product reviews with ratings
   - Related products
   - Breadcrumb navigation

4. **Shopping Cart (`cart.php`)**
   - Cart items list
   - Quantity management
   - Remove items
   - Clear cart
   - Promo code input
   - Order summary
   - Proceed to checkout

5. **Checkout (`checkout.php`)**
   - Step indicator
   - Shipping address selection
   - Add new address form
   - Shipping method selection
   - Payment method selection
   - Order summary
   - Place order

6. **Login Page (`login.php`)**
   - Modern split design
   - Email & password login
   - Remember me option
   - Redirect after login

### 🎨 Design Features:

- **Modern UI/UX**
  - Gradient backgrounds
  - Card-based layouts
  - Smooth animations
  - Hover effects
  - Loading states

- **Responsive Design**
  - Mobile-first approach
  - Breakpoints for tablets & desktop
  - Flexible grid layouts
  - Touch-friendly buttons

- **Color Scheme**
  - Primary: #667eea (Purple-blue)
  - Secondary: #764ba2 (Purple)
  - Success: #10b981 (Green)
  - Danger: #ef4444 (Red)
  - Consistent throughout

## 🔧 Setup & Installation

### 1. Prerequisites
- Laragon/XAMPP running
- API sudah berjalan di `/api`
- Database sudah diimport

### 2. Akses Website
```
http://localhost/belajar-web/toko-online-php/web/
```

### 3. Test Account
Gunakan data dari database:
- Email: `andi@example.com`
- Password: `pass123`

## 📡 API Integration

Website ini terintegrasi dengan REST API yang ada di folder `/api`:

### Endpoints yang Digunakan:

- `GET /api/product.php` - Get all products
- `GET /api/product.php?id={id}` - Get product detail
- `GET /api/category.php` - Get categories
- `GET /api/cart.php?id_member={id}` - Get cart items
- `POST /api/cart.php` - Add to cart
- `PUT /api/cart.php?id={id}` - Update cart quantity
- `DELETE /api/cart.php?id={id}` - Remove from cart
- `POST /api/penjualan.php` - Create order
- `GET /api/review.php?id_barang={id}` - Get product reviews
- `GET /api/voucher.php?kode={code}` - Validate voucher
- `POST /api/auth.php` - Login/Register
- `GET /api/alamat.php?id_member={id}` - Get addresses
- `POST /api/alamat.php` - Add new address

## 🎯 User Flow

### Shopping Flow:
1. Browse products di homepage atau products page
2. Click product untuk lihat detail
3. Pilih quantity & add to cart
4. View cart & apply promo code (optional)
5. Proceed to checkout
6. Select/add shipping address
7. Choose shipping & payment method
8. Place order

### Authentication Flow:
1. Click login di header
2. Enter email & password
3. Redirect ke halaman sebelumnya atau homepage

## 💡 JavaScript Features

### Main Functions (`assets/js/main.js`):

- `formatPrice(price)` - Format harga ke Rupiah
- `showSuccess(message)` - Show success toast
- `showError(message)` - Show error toast
- `addToCart(productId, qty)` - Add product to cart
- `updateCartCount()` - Update cart badge count
- `isLoggedIn()` - Check user login status
- `getMemberId()` - Get logged in member ID

### Page-Specific Functions:

**Products Page:**
- `loadProducts()` - Load all products
- `filterByCategory()` - Filter by selected categories
- `applyPriceFilter()` - Filter by price range
- `searchProducts()` - Search by keyword
- `sortProducts()` - Sort products

**Product Detail:**
- `loadProductDetail()` - Load product info
- `loadReviews()` - Load product reviews
- `loadRelatedProducts()` - Load related products
- `increaseQty()` / `decreaseQty()` - Quantity controls
- `buyNow()` - Quick checkout

**Cart:**
- `loadCart()` - Load cart items
- `updateCartQty()` - Update item quantity
- `removeFromCart()` - Remove item
- `clearCart()` - Clear all items
- `applyPromo()` - Apply promo code

**Checkout:**
- `loadCartItems()` - Load items for checkout
- `loadAddresses()` - Load saved addresses
- `saveAddress()` - Save new address
- `placeOrder()` - Create order

## 🎨 CSS Architecture

### Structure:
1. Reset & Base styles
2. CSS Variables (colors, shadows)
3. Header & Navigation
4. Hero Section
5. Buttons & Forms
6. Product Cards
7. Cart & Checkout
8. Footer
9. Responsive breakpoints

### Key Classes:

**Layout:**
- `.container` - Max-width container
- `.grid` - Grid layout
- `.flex` - Flexbox layout

**Components:**
- `.btn` - Button base
- `.btn-primary` / `.btn-success` / `.btn-danger` - Button variants
- `.btn-lg` / `.btn-sm` - Button sizes
- `.card` - Card component
- `.form-control` - Form input

**Utilities:**
- `.text-center` - Center text
- `.text-muted` - Muted text color
- `.mt-20` / `.mb-20` - Margins

## 📱 Responsive Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

## 🔐 Security Notes

**Important:** Ini adalah versi sederhana untuk development.

Untuk production, tambahkan:
1. ✅ CSRF protection
2. ✅ XSS prevention
3. ✅ SQL injection protection (sudah ada di API)
4. ✅ Session management yang proper
5. ✅ HTTPS
6. ✅ Input validation & sanitization
7. ✅ Rate limiting
8. ✅ Content Security Policy

## 🚧 TODO / Future Improvements

### Halaman yang Belum Dibuat:
- [ ] Register page
- [ ] Profile page
- [ ] Orders history page
- [ ] Order detail page
- [ ] Order success page
- [ ] Wishlist page
- [ ] Product search results page

### Features yang Bisa Ditambahkan:
- [ ] Product image upload
- [ ] Multiple product images
- [ ] Product zoom on hover
- [ ] Advanced filters (brand, rating, etc.)
- [ ] Product comparison
- [ ] Recently viewed products
- [ ] Live chat support
- [ ] Newsletter subscription
- [ ] Social media sharing
- [ ] Product quick view modal
- [ ] Lazy loading images
- [ ] Infinite scroll
- [ ] PWA support

### Improvements:
- [ ] Add loading skeletons
- [ ] Optimize images
- [ ] Add service worker
- [ ] Implement caching
- [ ] Add error boundaries
- [ ] Improve accessibility (ARIA labels)
- [ ] Add keyboard navigation
- [ ] SEO optimization
- [ ] Performance optimization
- [ ] Add analytics

## 📝 Notes

1. **Session Management**: Currently using basic PHP sessions. Untuk production, gunakan secure session handling.

2. **Image Placeholder**: Gunakan placeholder image untuk produk. Untuk production, implement proper image upload & storage.

3. **API Base URL**: Hardcoded di `main.js`. Untuk production, gunakan environment variables.

4. **Error Handling**: Basic error handling sudah ada. Untuk production, tambahkan comprehensive error logging.

5. **Browser Support**: Tested di Chrome, Firefox, Safari modern browsers.

## 🎓 Learning Resources

Teknologi yang digunakan:
- HTML5
- CSS3 (Flexbox, Grid, Animations)
- Vanilla JavaScript (ES6+)
- PHP 7.4+
- Fetch API
- Font Awesome Icons

## 📞 Support

Jika ada pertanyaan atau issue:
1. Check API documentation di `/api/API_DOCUMENTATION.md`
2. Check test scenarios di `/api/TEST_SCENARIOS.md`
3. Review code comments

---

**Happy Coding! 🚀**
