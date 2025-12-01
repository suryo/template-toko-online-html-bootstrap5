# API Documentation - Toko Online

## Base URL
```
http://localhost/belajar-web/toko-online-php/api/
```

## Response Format
Semua response menggunakan format JSON dengan struktur:
```json
{
  "status": true/false,
  "message": "Pesan response",
  "data": {} // Optional, berisi data hasil query
}
```

---

## 1. Authentication API (`auth.php`)

### Login Member
**Endpoint:** `POST /api/auth.php`
```
action=login
email=user@example.com
password=password123
```

**Response Success:**
```json
{
  "status": true,
  "message": "Login berhasil",
  "data": {
    "id": 1,
    "nama": "Andi Wijaya",
    "email": "andi@example.com",
    "no_hp": "081234567890"
  }
}
```

### Register Member
**Endpoint:** `POST /api/auth.php`
```
action=register
nama=John Doe
email=john@example.com
password=password123
no_hp=08123456789
```

### Admin Login
**Endpoint:** `POST /api/auth.php`
```
action=admin_login
username=admin@gmail.com
password=adminpass
```

---

## 2. Product API (`product.php`)

### Get All Products
**Endpoint:** `GET /api/product.php`

### Get Single Product
**Endpoint:** `GET /api/product.php?id=1`

### Create Product
**Endpoint:** `POST /api/product.php`
```
nama_barang=Laptop
id_kategori=1
harga=5000000
stok=10
deskripsi=Laptop gaming
```

### Update Product
**Endpoint:** `POST /api/product.php?id=1`
```
_method=PUT
nama_barang=Laptop Updated
id_kategori=1
harga=5500000
stok=15
deskripsi=Laptop gaming updated
```

### Delete Product
**Endpoint:** `POST /api/product.php?id=1`
```
_method=DELETE
```

---

## 3. Category API (`category.php`)

### Get All Categories
**Endpoint:** `GET /api/category.php`

### Get Single Category
**Endpoint:** `GET /api/category.php?id=1`

### Create Category
**Endpoint:** `POST /api/category.php`
```
nama_kategori=Elektronik
```

### Update Category
**Endpoint:** `POST /api/category.php?id=1`
```
_method=PUT
nama_kategori=Elektronik Updated
```

### Delete Category
**Endpoint:** `POST /api/category.php?id=1`
```
_method=DELETE
```

---

## 4. Member API (`member.php`)

### Get All Members
**Endpoint:** `GET /api/member.php`

### Get Single Member
**Endpoint:** `GET /api/member.php?id=1`

### Create Member
**Endpoint:** `POST /api/member.php`
```
nama=John Doe
email=john@example.com
password=password123
no_hp=08123456789
```

### Update Member
**Endpoint:** `POST /api/member.php?id=1`
```
_method=PUT
nama=John Doe Updated
email=john@example.com
no_hp=08123456789
password=newpassword (optional)
```

### Delete Member
**Endpoint:** `POST /api/member.php?id=1`
```
_method=DELETE
```

---

## 5. Cart API (`cart.php`)

### Get Cart Items
**Endpoint:** `GET /api/cart.php?id_member=1`

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "id_member": 1,
      "id_barang": 3,
      "qty": 2,
      "nama_barang": "Blender",
      "harga": 300000,
      "stok": 80,
      "subtotal": 600000
    }
  ],
  "total": 600000
}
```

### Add to Cart
**Endpoint:** `POST /api/cart.php`
```
id_member=1
id_barang=3
qty=2
```

### Update Cart Quantity
**Endpoint:** `POST /api/cart.php?id=1`
```
_method=PUT
qty=5
```

### Remove from Cart
**Endpoint:** `POST /api/cart.php?id=1`
```
_method=DELETE
```

### Clear Cart
**Endpoint:** `POST /api/cart.php?id_member=1`
```
_method=DELETE
```

---

## 6. Sales API (`penjualan.php`)

### Get All Sales
**Endpoint:** `GET /api/penjualan.php`

### Get Sales by Member
**Endpoint:** `GET /api/penjualan.php?id_member=1`

### Get Sales by Status
**Endpoint:** `GET /api/penjualan.php?status=pending`

Status options: `pending`, `paid`, `shipped`, `completed`, `cancelled`

### Get Single Sale with Details
**Endpoint:** `GET /api/penjualan.php?id=1`

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "id_member": 1,
    "tanggal": "2024-07-03",
    "total": 1900000,
    "status": "completed",
    "nama_member": "Andi Wijaya",
    "email": "andi@example.com",
    "details": [
      {
        "id": 1,
        "id_penjualan": 1,
        "id_barang": 2,
        "qty": 1,
        "harga": 1500000,
        "subtotal": 1500000,
        "nama_barang": "TV LED 32 Inch"
      }
    ]
  }
}
```

### Create Sale (Checkout)
**Endpoint:** `POST /api/penjualan.php`
```
id_member=1
items=[{"id_barang":2,"qty":1},{"id_barang":4,"qty":1}]
```

**Note:** Items harus dalam format JSON array

### Update Sale Status
**Endpoint:** `POST /api/penjualan.php?id=1`
```
_method=PUT
status=paid
```

### Cancel Sale
**Endpoint:** `POST /api/penjualan.php?id=1`
```
_method=DELETE
```

---

## 7. Supplier API (`supplier.php`)

### Get All Suppliers
**Endpoint:** `GET /api/supplier.php`

### Get Single Supplier
**Endpoint:** `GET /api/supplier.php?id=1`

### Create Supplier
**Endpoint:** `POST /api/supplier.php`
```
nama=PT Maju Jaya
kontak=08211234567
alamat=Jl. Industri No. 5
```

### Update Supplier
**Endpoint:** `POST /api/supplier.php?id=1`
```
_method=PUT
nama=PT Maju Jaya Updated
kontak=08211234567
alamat=Jl. Industri No. 5
```

### Delete Supplier
**Endpoint:** `POST /api/supplier.php?id=1`
```
_method=DELETE
```

---

## 8. Purchase API (`pembelian.php`)

### Get All Purchases
**Endpoint:** `GET /api/pembelian.php`

### Get Single Purchase with Details
**Endpoint:** `GET /api/pembelian.php?id=1`

### Create Purchase
**Endpoint:** `POST /api/pembelian.php`
```
id_supplier=1
items=[{"id_barang":1,"qty":10,"harga":140000}]
```

### Delete Purchase
**Endpoint:** `POST /api/pembelian.php?id=1`
```
_method=DELETE
```

---

## 9. Discount API (`discount.php`)

### Get All Discounts
**Endpoint:** `GET /api/discount.php`

### Get Active Discounts Only
**Endpoint:** `GET /api/discount.php?active=1`

### Get Discount for Product
**Endpoint:** `GET /api/discount.php?id_barang=4`

### Create Discount
**Endpoint:** `POST /api/discount.php`
```
id_barang=4
persentase=10
tanggal_mulai=2024-07-01
tanggal_selesai=2024-07-31
```

### Update Discount
**Endpoint:** `POST /api/discount.php?id=1`
```
_method=PUT
id_barang=4
persentase=15
tanggal_mulai=2024-07-01
tanggal_selesai=2024-07-31
```

### Delete Discount
**Endpoint:** `POST /api/discount.php?id=1`
```
_method=DELETE
```

---

## 10. Review API (`review.php`)

### Get All Reviews
**Endpoint:** `GET /api/review.php`

### Get Reviews for Product
**Endpoint:** `GET /api/review.php?id_barang=2`

**Response:**
```json
{
  "status": true,
  "data": [...],
  "average_rating": 4.5,
  "total_reviews": 10
}
```

### Get Reviews by Member
**Endpoint:** `GET /api/review.php?id_member=1`

### Create Review
**Endpoint:** `POST /api/review.php`
```
id_member=1
id_barang=2
rating=5
komentar=Produk bagus sekali
```

### Update Review
**Endpoint:** `POST /api/review.php?id=1`
```
_method=PUT
rating=4
komentar=Produk cukup bagus
```

### Delete Review
**Endpoint:** `POST /api/review.php?id=1`
```
_method=DELETE
```

---

## 11. Shipping Address API (`alamat.php`)

### Get All Addresses
**Endpoint:** `GET /api/alamat.php`

### Get Addresses by Member
**Endpoint:** `GET /api/alamat.php?id_member=1`

### Create Address
**Endpoint:** `POST /api/alamat.php`
```
id_member=1
nama_penerima=John Doe
alamat=Jl. Melati No. 10
kota=Jakarta
kode_pos=10110
no_hp=081234567890
```

### Update Address
**Endpoint:** `POST /api/alamat.php?id=1`
```
_method=PUT
nama_penerima=John Doe Updated
alamat=Jl. Melati No. 10
kota=Jakarta
kode_pos=10110
no_hp=081234567890
```

### Delete Address
**Endpoint:** `POST /api/alamat.php?id=1`
```
_method=DELETE
```

---

## 12. Voucher API (`voucher.php`)

### Get All Vouchers
**Endpoint:** `GET /api/voucher.php`

### Get Active Vouchers Only
**Endpoint:** `GET /api/voucher.php?active=1`

### Validate Voucher Code
**Endpoint:** `GET /api/voucher.php?kode=PROMO10`

**Response:**
```json
{
  "status": true,
  "valid": true,
  "data": {
    "id": 1,
    "kode_voucher": "PROMO10",
    "deskripsi": "Diskon 10%",
    "persentase": 10,
    "max_potongan": 50000,
    "tanggal_mulai": "2024-07-01",
    "tanggal_selesai": "2024-07-31"
  }
}
```

### Create Voucher
**Endpoint:** `POST /api/voucher.php`
```
kode_voucher=PROMO10
deskripsi=Diskon 10%
persentase=10
max_potongan=50000
tanggal_mulai=2024-07-01
tanggal_selesai=2024-07-31
```

### Update Voucher
**Endpoint:** `POST /api/voucher.php?id=1`
```
_method=PUT
kode_voucher=PROMO15
deskripsi=Diskon 15%
persentase=15
max_potongan=75000
tanggal_mulai=2024-07-01
tanggal_selesai=2024-07-31
```

### Delete Voucher
**Endpoint:** `POST /api/voucher.php?id=1`
```
_method=DELETE
```

---

## 13. Wishlist API (`wishlist.php`)

### Get Wishlist
**Endpoint:** `GET /api/wishlist.php?id_member=1`

### Add to Wishlist
**Endpoint:** `POST /api/wishlist.php`
```
id_member=1
id_barang=4
```

### Remove from Wishlist (by ID)
**Endpoint:** `POST /api/wishlist.php?id=1`
```
_method=DELETE
```

### Remove from Wishlist (by Member & Product)
**Endpoint:** `POST /api/wishlist.php?id_member=1&id_barang=4`
```
_method=DELETE
```

---

## 14. Notification API (`notifikasi.php`)

### Get Notifications
**Endpoint:** `GET /api/notifikasi.php?id_member=1`

### Get Unread Notifications Only
**Endpoint:** `GET /api/notifikasi.php?id_member=1&sudah_dibaca=0`

### Create Notification
**Endpoint:** `POST /api/notifikasi.php`
```
id_member=1
isi=Pesanan Anda telah dikirim
```

### Mark as Read (Single)
**Endpoint:** `POST /api/notifikasi.php?id=1`
```
_method=PUT
```

### Mark All as Read
**Endpoint:** `POST /api/notifikasi.php?id_member=1`
```
_method=PUT
```

### Delete Notification
**Endpoint:** `POST /api/notifikasi.php?id=1`
```
_method=DELETE
```

### Delete All Read Notifications
**Endpoint:** `POST /api/notifikasi.php?id_member=1`
```
_method=DELETE
```

---

## 15. Shipment API (`shipment.php`)

### Get All Shipments
**Endpoint:** `GET /api/shipment.php`

### Get Shipment by Sale
**Endpoint:** `GET /api/shipment.php?id_penjualan=1`

### Track by Resi
**Endpoint:** `GET /api/shipment.php?resi=JNE123456789`

### Create Shipment
**Endpoint:** `POST /api/shipment.php`
```
id_penjualan=1
id_alamat=1
kurir=JNE
resi=JNE123456789
tanggal_kirim=2024-07-03
status_pengiriman=dikirim
```

### Update Shipment Status
**Endpoint:** `POST /api/shipment.php?id=1`
```
_method=PUT
status_pengiriman=diterima
tanggal_terima=2024-07-05
```

### Delete Shipment
**Endpoint:** `POST /api/shipment.php?id=1`
```
_method=DELETE
```

---

## 16. Payment API (`payment.php`)

### Get All Payments
**Endpoint:** `GET /api/payment.php`

### Get Payment by Status
**Endpoint:** `GET /api/payment.php?status=lunas`

Status options: `belum dibayar`, `lunas`, `gagal`

### Get Payment by Sale
**Endpoint:** `GET /api/payment.php?id_penjualan=1`

### Create Payment
**Endpoint:** `POST /api/payment.php`
```
id_penjualan=1
metode=Transfer BCA
status=lunas
tanggal_bayar=2024-07-03
```

### Update Payment Status
**Endpoint:** `POST /api/payment.php?id=1`
```
_method=PUT
status=lunas
tanggal_bayar=2024-07-03
```

### Delete Payment
**Endpoint:** `POST /api/payment.php?id=1`
```
_method=DELETE
```

---

## Error Codes

- **200**: Success
- **201**: Created
- **400**: Bad Request (Missing required fields)
- **401**: Unauthorized (Invalid credentials)
- **404**: Not Found
- **405**: Method Not Allowed
- **500**: Internal Server Error

---

## Testing dengan cURL

### Example: Login
```bash
curl -X POST http://localhost/belajar-web/toko-online-php/api/auth.php \
  -d "action=login" \
  -d "email=andi@example.com" \
  -d "password=pass123"
```

### Example: Get All Products
```bash
curl http://localhost/belajar-web/toko-online-php/api/product.php
```

### Example: Create Product
```bash
curl -X POST http://localhost/belajar-web/toko-online-php/api/product.php \
  -d "nama_barang=Laptop" \
  -d "id_kategori=1" \
  -d "harga=5000000" \
  -d "stok=10" \
  -d "deskripsi=Laptop gaming"
```

### Example: Update Product
```bash
curl -X POST "http://localhost/belajar-web/toko-online-php/api/product.php?id=1" \
  -d "_method=PUT" \
  -d "nama_barang=Laptop Updated" \
  -d "id_kategori=1" \
  -d "harga=5500000" \
  -d "stok=15"
```

---

## Notes

1. Semua API menggunakan **CORS** yang sudah diaktifkan untuk development
2. Password di-hash menggunakan `password_hash()` untuk keamanan
3. Transaksi menggunakan **database transaction** untuk menjaga integritas data
4. Stock otomatis terupdate saat ada transaksi penjualan/pembelian
5. Status penjualan otomatis berubah saat payment/shipment diupdate
