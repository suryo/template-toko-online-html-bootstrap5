# Toko Online - Complete REST API

API lengkap untuk sistem toko online yang dibangun dengan PHP native dan MySQL.

## 📋 Daftar API

Berikut adalah daftar lengkap API yang telah dibuat:

### 1. **Authentication** (`auth.php`)
- Login Member
- Register Member
- Admin Login
- Verify Token

### 2. **Products** (`product.php`)
- Get All Products
- Get Single Product
- Create Product
- Update Product
- Delete Product

### 3. **Categories** (`category.php`)
- Get All Categories
- Get Single Category
- Create Category
- Update Category
- Delete Category

### 4. **Members** (`member.php`)
- Get All Members
- Get Single Member
- Create Member
- Update Member
- Delete Member

### 5. **Shopping Cart** (`cart.php`)
- Get Cart Items (with total calculation)
- Add to Cart (with stock validation)
- Update Cart Quantity
- Remove from Cart
- Clear Cart

### 6. **Sales/Orders** (`penjualan.php`)
- Get All Sales
- Get Sales by Member
- Get Sales by Status
- Get Single Sale with Details
- Create Sale (Checkout with transaction)
- Update Sale Status
- Cancel Sale (restore stock)

### 7. **Suppliers** (`supplier.php`)
- Get All Suppliers
- Get Single Supplier
- Create Supplier
- Update Supplier
- Delete Supplier

### 8. **Purchases** (`pembelian.php`)
- Get All Purchases
- Get Single Purchase with Details
- Create Purchase (with stock update & logging)
- Delete Purchase (restore stock)

### 9. **Discounts** (`discount.php`)
- Get All Discounts
- Get Active Discounts
- Get Discount for Product
- Create Discount
- Update Discount
- Delete Discount

### 10. **Reviews** (`review.php`)
- Get All Reviews
- Get Reviews for Product (with average rating)
- Get Reviews by Member
- Create Review (prevent duplicate)
- Update Review
- Delete Review

### 11. **Shipping Addresses** (`alamat.php`)
- Get All Addresses
- Get Addresses by Member
- Create Address
- Update Address
- Delete Address

### 12. **Vouchers** (`voucher.php`)
- Get All Vouchers
- Get Active Vouchers
- Validate Voucher Code
- Create Voucher
- Update Voucher
- Delete Voucher

### 13. **Wishlist** (`wishlist.php`)
- Get Wishlist
- Add to Wishlist (prevent duplicate)
- Remove from Wishlist

### 14. **Notifications** (`notifikasi.php`)
- Get Notifications (with unread count)
- Get Unread Notifications
- Create Notification
- Mark as Read (single/all)
- Delete Notification

### 15. **Shipments** (`shipment.php`)
- Get All Shipments
- Get Shipment by Sale
- Track by Resi Number
- Create Shipment (auto update order status)
- Update Shipment Status
- Delete Shipment

### 16. **Payments** (`payment.php`)
- Get All Payments
- Get Payment by Status
- Get Payment by Sale
- Create Payment (auto update order status)
- Update Payment Status
- Delete Payment

## 🚀 Fitur Utama

### ✅ Security
- Password hashing menggunakan `password_hash()`
- Prepared statements untuk mencegah SQL injection
- CORS enabled untuk development

### ✅ Data Integrity
- Database transactions untuk operasi kompleks
- Foreign key constraints
- Automatic stock management
- Stock logging system

### ✅ Business Logic
- Automatic stock validation saat checkout
- Automatic stock update saat purchase/sale
- Automatic order status update berdasarkan payment/shipment
- Duplicate prevention (reviews, wishlist, voucher codes)
- Date-based discount & voucher activation

### ✅ Response Format
Semua API menggunakan format JSON yang konsisten:
```json
{
  "status": true/false,
  "message": "Response message",
  "data": {} // Optional
}
```

## 📁 Struktur File

```
api/
├── auth.php              # Authentication
├── product.php           # Products management
├── category.php          # Categories management
├── member.php            # Members management
├── cart.php              # Shopping cart
├── penjualan.php         # Sales/Orders
├── supplier.php          # Suppliers management
├── pembelian.php         # Purchases management
├── discount.php          # Discounts management
├── review.php            # Product reviews
├── alamat.php            # Shipping addresses
├── voucher.php           # Vouchers management
├── wishlist.php          # Wishlist management
├── notifikasi.php        # Notifications
├── shipment.php          # Shipments tracking
├── payment.php           # Payments management
├── API_DOCUMENTATION.md  # Complete API documentation
├── test.html             # Interactive API tester
└── README.md             # This file
```

## 🔧 Setup

1. **Import Database**
   ```bash
   mysql -u root -p < database/db_toko_online.sql
   ```

2. **Configure Database Connection**
   Edit `config/koneksi.php`:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "db_toko_online";
   ```

3. **Start Server**
   Pastikan Laragon/XAMPP sudah running

4. **Test API**
   Buka browser: `http://localhost/belajar-web/toko-online-php/api/test.html`

## 📖 Dokumentasi

Lihat dokumentasi lengkap di: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

## 🧪 Testing

### Menggunakan API Tester (Recommended)
Buka file `test.html` di browser untuk testing interaktif dengan UI yang user-friendly.

### Menggunakan cURL

**Login:**
```bash
curl -X POST http://localhost/belajar-web/toko-online-php/api/auth.php \
  -d "action=login" \
  -d "email=andi@example.com" \
  -d "password=pass123"
```

**Get Products:**
```bash
curl http://localhost/belajar-web/toko-online-php/api/product.php
```

**Add to Cart:**
```bash
curl -X POST http://localhost/belajar-web/toko-online-php/api/cart.php \
  -d "id_member=1" \
  -d "id_barang=3" \
  -d "qty=2"
```

### Menggunakan Postman
Import collection dari dokumentasi atau buat request manual sesuai dokumentasi.

## 📊 Database Schema

Database memiliki 25+ tabel dengan relasi yang kompleks:

**Core Tables:**
- `barang` - Products
- `category_barang` - Categories
- `client_member` - Customers
- `users` - Admin users

**Transaction Tables:**
- `penjualan` & `penjualan_detail` - Sales
- `pembelian` & `pembelian_detail` - Purchases
- `cart` - Shopping cart
- `payment` - Payments
- `shipment` - Shipments

**Supporting Tables:**
- `discount` - Product discounts
- `voucher` - Voucher codes
- `review` - Product reviews
- `wishlist` - Customer wishlist
- `alamat_pengiriman` - Shipping addresses
- `notifikasi` - Notifications
- `stok_log` - Stock movement log
- `supplier` - Suppliers
- Dan lainnya...

## 🎯 Use Cases

### Customer Flow:
1. Register/Login (`auth.php`)
2. Browse products (`product.php`)
3. Add to cart (`cart.php`)
4. Checkout (`penjualan.php`)
5. Make payment (`payment.php`)
6. Track shipment (`shipment.php`)
7. Write review (`review.php`)

### Admin Flow:
1. Admin login (`auth.php`)
2. Manage products (`product.php`)
3. Manage categories (`category.php`)
4. Process orders (`penjualan.php`)
5. Manage suppliers (`supplier.php`)
6. Create purchases (`pembelian.php`)
7. Set discounts (`discount.php`)
8. Manage vouchers (`voucher.php`)

## 🔐 HTTP Methods

- **GET** - Retrieve data
- **POST** - Create new data
- **PUT** - Update existing data (via POST with `_method=PUT`)
- **DELETE** - Delete data (via POST with `_method=DELETE`)

## ⚠️ Error Handling

Semua API mengembalikan error dengan format yang konsisten:

```json
{
  "status": false,
  "message": "Error message description"
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `405` - Method Not Allowed
- `500` - Internal Server Error

## 💡 Tips

1. **Stock Management**: Stock otomatis terupdate saat ada transaksi
2. **Order Status**: Status order otomatis berubah berdasarkan payment/shipment
3. **Validation**: Semua input divalidasi sebelum diproses
4. **Transactions**: Operasi kompleks menggunakan database transaction
5. **Logging**: Perubahan stock tercatat di `stok_log`

## 🛠️ Maintenance

### Update Password Hashing
Untuk user yang sudah ada dengan password plain text, jalankan:
```php
UPDATE client_member SET password = PASSWORD_HASH(password, PASSWORD_DEFAULT);
UPDATE users SET password = PASSWORD_HASH(password, PASSWORD_DEFAULT);
```

### Clear Old Notifications
```sql
DELETE FROM notifikasi WHERE sudah_dibaca = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

## 📝 License

Free to use for learning purposes.

## 👨‍💻 Author

Created for learning PHP REST API development.

---

**Happy Coding! 🚀**
