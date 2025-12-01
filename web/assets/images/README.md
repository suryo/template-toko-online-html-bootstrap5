# Product Placeholder Image

Untuk sementara, gunakan placeholder image dari service online atau buat image sendiri.

## Cara Menambahkan Gambar:

### Option 1: Gunakan Placeholder Service (Temporary)
Ganti di setiap file yang menggunakan gambar:

```html
<!-- Dari -->
<img src="assets/images/product-placeholder.jpg" alt="Product">

<!-- Menjadi -->
<img src="https://via.placeholder.com/400x400/667eea/ffffff?text=Product" alt="Product">
```

### Option 2: Buat Image Sendiri
1. Buat image 400x400px
2. Save sebagai `product-placeholder.jpg`
3. Letakkan di folder ini (`web/assets/images/`)

### Option 3: Gunakan Image dari Database
Jika sudah ada field `gambar` di tabel `barang`:
1. Update database schema untuk menambahkan kolom `gambar`
2. Upload gambar produk
3. Update code untuk menggunakan path dari database

## Recommended Image Specs:

- **Format**: JPG atau WebP
- **Size**: 400x400px (square)
- **Quality**: 80-90%
- **Max file size**: < 200KB

## Untuk Production:

1. Implement image upload di admin panel
2. Store images di folder `/uploads/products/`
3. Generate thumbnails (small, medium, large)
4. Use CDN untuk faster loading
5. Implement lazy loading
6. Add image optimization

---

**Note**: File ini hanya placeholder. Hapus file ini setelah menambahkan gambar produk yang sebenarnya.
