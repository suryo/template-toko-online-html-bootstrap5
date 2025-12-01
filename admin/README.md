# Admin Panel - Toko Online

Admin panel untuk mengelola konten dan transaksi Toko Online.

## 📁 Struktur Folder

```
admin/
├── index.php              # Login Page
├── dashboard.php          # Dashboard Utama
├── products.php           # Kelola Produk
├── categories.php         # Kelola Kategori
├── members.php            # Kelola Member
├── orders.php             # Kelola Pesanan
├── suppliers.php          # Kelola Supplier
├── includes/
│   ├── sidebar.php        # Sidebar Menu
│   └── topbar.php         # Topbar & User Menu
├── assets/
│   ├── css/
│   │   └── admin.css      # Admin Styles
│   └── js/
│       └── admin.js       # Admin Scripts
└── README.md              # This file
```

## 🚀 Fitur

### 1. Dashboard
- Statistik ringkas (Total Pesanan, Produk, Member, Pendapatan)
- Tabel pesanan terbaru
- Peringatan stok menipis

### 2. Manajemen Produk (`products.php`)
- List produk dengan pagination (client-side)
- Filter by Category, Stock Status, Search
- Tambah Produk Baru
- Edit Produk
- Hapus Produk
- Indikator stok (Aman, Menipis, Habis)

### 3. Manajemen Kategori (`categories.php`)
- List kategori
- Tambah Kategori
- Edit Kategori
- Hapus Kategori

### 4. Manajemen Member (`members.php`)
- List member terdaftar
- Tambah Member Baru
- Edit Member
- Hapus Member
- Reset Password Member

### 5. Manajemen Pesanan (`orders.php`)
- List semua pesanan
- Filter by Status, Search
- Detail Pesanan (Modal View)
- Update Status Pesanan (Pending -> Paid -> Shipped -> Completed)
- Batalkan Pesanan

### 6. Manajemen Supplier (`suppliers.php`)
- List supplier
- Tambah Supplier
- Edit Supplier
- Hapus Supplier

## 🔐 Keamanan

- **Authentication**: Menggunakan session PHP (`admin_id`).
- **Authorization**: Halaman admin diproteksi, redirect ke login jika belum login.
- **Client-side Check**: JavaScript juga mengecek session storage untuk UX yang lebih baik.

## 🔧 Setup

1. Pastikan database sudah terisi dengan tabel yang sesuai.
2. Untuk login pertama kali, Anda mungkin perlu insert data admin ke database secara manual jika belum ada fitur register admin.
   ```sql
   INSERT INTO member (nama, email, password, role) VALUES ('Admin', 'admin@tokoonline.com', 'admin123', 'admin');
   ```
   *(Note: Sesuaikan dengan struktur tabel member Anda, pastikan ada kolom role atau tabel admin terpisah)*

## 📝 Notes

- Admin panel ini menggunakan **Vanilla JavaScript** dan **Fetch API** untuk komunikasi dengan backend.
- Styling menggunakan **CSS Native** dengan variabel CSS untuk kemudahan theming.
- Tidak ada dependency eksternal selain **Font Awesome** (CDN).
