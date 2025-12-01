# Panduan Pengetesan API - Toko Online

## 🎯 Cara Menjalankan Test

### 1. **Test Menggunakan Browser (Paling Mudah)**

Buka file test HTML di browser:
```
http://localhost/belajar-web/toko-online-php/api/test.html
```

**Keuntungan:**
- ✅ User-friendly interface
- ✅ Tidak perlu install tools tambahan
- ✅ Hasil langsung terlihat
- ✅ Cocok untuk demo

---

### 2. **Test Menggunakan Postman (Recommended)**

#### Langkah-langkah:

1. **Download & Install Postman**
   - Download dari: https://www.postman.com/downloads/

2. **Import Collection**
   - Buka Postman
   - Klik "Import"
   - Pilih file: `Toko_Online_API.postman_collection.json`
   - Collection akan muncul di sidebar

3. **Set Environment Variable**
   - Klik "Environments" → "Create Environment"
   - Nama: `Toko Online Local`
   - Variable:
     - Key: `base_url`
     - Value: `http://localhost/belajar-web/toko-online-php/api`
   - Save

4. **Jalankan Test**
   - Pilih environment "Toko Online Local"
   - Buka collection "Toko Online - Complete API"
   - Klik request yang ingin ditest
   - Klik "Send"

**Keuntungan:**
- ✅ Professional tool
- ✅ Save request history
- ✅ Environment management
- ✅ Test automation
- ✅ Team collaboration

---

### 3. **Test Menggunakan cURL (Command Line)**

#### Contoh Commands:

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

**Get Cart:**
```bash
curl "http://localhost/belajar-web/toko-online-php/api/cart.php?id_member=1"
```

**Create Order:**
```bash
curl -X POST http://localhost/belajar-web/toko-online-php/api/penjualan.php \
  -d "id_member=1" \
  -d 'items=[{"id_barang":2,"qty":1}]'
```

**Keuntungan:**
- ✅ Fast & lightweight
- ✅ Scriptable
- ✅ CI/CD integration
- ✅ No GUI needed

---

### 4. **Test Otomatis dengan PHP Script**

#### Jalankan Script:

**Windows (PowerShell):**
```powershell
cd C:\laragon\www\belajar-web\toko-online-php\api
php run_tests.php
```

**Output:**
```
╔══════════════════════════════════════════════════════════╗
║         TOKO ONLINE API - AUTOMATED TEST SUITE          ║
╚══════════════════════════════════════════════════════════╝

============================================================
1. AUTHENTICATION TESTS
============================================================

Testing: Member Login - Success
✓ PASSED

Testing: Member Login - Wrong Password
✓ PASSED

...

============================================================
TEST SUMMARY
============================================================

Total Tests: 35
Passed: 35
Failed: 0
Success Rate: 100%
```

**Keuntungan:**
- ✅ Automated testing
- ✅ Batch testing
- ✅ Quick validation
- ✅ CI/CD ready

---

## 📋 Skenario Test Prioritas

### **Priority 1: Critical Flow (Harus Test)**

1. ✅ **Authentication**
   - Login member
   - Register member
   - Admin login

2. ✅ **Shopping Flow**
   - Browse products
   - Add to cart
   - View cart
   - Checkout
   - Payment
   - Shipment

3. ✅ **Stock Management**
   - Check stock before purchase
   - Stock reduced after sale
   - Stock restored after cancellation

### **Priority 2: Important Features**

4. ✅ **Order Management**
   - View orders
   - Update order status
   - Cancel order

5. ✅ **Review System**
   - Write review
   - View reviews
   - Calculate average rating

6. ✅ **Voucher System**
   - Validate voucher
   - Apply discount

### **Priority 3: Supporting Features**

7. ✅ **Wishlist**
   - Add to wishlist
   - Remove from wishlist

8. ✅ **Notifications**
   - View notifications
   - Mark as read

9. ✅ **Address Management**
   - Add shipping address
   - Update address

---

## 🧪 Test Cases Quick Reference

### **Positive Tests (Happy Path)**

| API | Test Case | Expected Result |
|-----|-----------|----------------|
| Auth | Login dengan credentials benar | Status 200, data user |
| Product | Get all products | Status 200, array products |
| Cart | Add item dengan stock cukup | Status 201, item added |
| Order | Checkout dengan items valid | Status 201, order created |
| Payment | Create payment untuk order | Status 201, payment created |

### **Negative Tests (Error Cases)**

| API | Test Case | Expected Result |
|-----|-----------|----------------|
| Auth | Login dengan password salah | Status 401, error message |
| Product | Get product ID tidak ada | Status 404, not found |
| Cart | Add item stock tidak cukup | Status 400, error message |
| Order | Checkout dengan items kosong | Status 400, error message |
| Review | Duplicate review | Status 400, error message |

### **Boundary Tests**

| API | Test Case | Expected Result |
|-----|-----------|----------------|
| Cart | Add qty = max stock | Success |
| Cart | Add qty > max stock | Error |
| Review | Rating = 1 | Success |
| Review | Rating = 5 | Success |
| Review | Rating = 6 | Error |
| Voucher | Use on valid date | Success |
| Voucher | Use after expiry | Error |

---

## 🔍 Verification Checklist

Setelah menjalankan test, verifikasi hal-hal berikut:

### **Database Verification**

- [ ] Stock berkurang setelah checkout
- [ ] Stock bertambah setelah purchase
- [ ] Stock kembali setelah cancel order
- [ ] Order status berubah sesuai flow
- [ ] Cart kosong setelah checkout
- [ ] Stok_log tercatat dengan benar

### **Response Verification**

- [ ] HTTP status code sesuai
- [ ] Response format JSON valid
- [ ] Field 'status' selalu ada
- [ ] Field 'message' ada untuk error
- [ ] Field 'data' ada untuk success

### **Business Logic Verification**

- [ ] Tidak bisa checkout jika stock habis
- [ ] Tidak bisa review produk 2x
- [ ] Tidak bisa add wishlist duplicate
- [ ] Voucher expired tidak bisa dipakai
- [ ] Payment duplicate ditolak
- [ ] Shipment duplicate ditolak

---

## 📊 Test Report Template

Setelah testing, buat report dengan format:

```
LAPORAN PENGETESAN API
Tanggal: [DD/MM/YYYY]
Tester: [Nama]

1. SUMMARY
   - Total Test Cases: XX
   - Passed: XX
   - Failed: XX
   - Success Rate: XX%

2. FAILED TESTS (jika ada)
   - Test Case: [nama]
   - Expected: [hasil yang diharapkan]
   - Actual: [hasil yang didapat]
   - Issue: [deskripsi masalah]

3. BUGS FOUND (jika ada)
   - Bug ID: [nomor]
   - Severity: [Critical/High/Medium/Low]
   - Description: [deskripsi bug]
   - Steps to Reproduce: [langkah-langkah]

4. RECOMMENDATIONS
   - [Saran perbaikan]
```

---

## 🚀 Quick Start Testing

### **5 Menit Test (Minimal)**

```bash
# 1. Test login
curl -X POST http://localhost/belajar-web/toko-online-php/api/auth.php \
  -d "action=login" -d "email=andi@example.com" -d "password=pass123"

# 2. Test get products
curl http://localhost/belajar-web/toko-online-php/api/product.php

# 3. Test add to cart
curl -X POST http://localhost/belajar-web/toko-online-php/api/cart.php \
  -d "id_member=1" -d "id_barang=3" -d "qty=1"

# 4. Test get cart
curl "http://localhost/belajar-web/toko-online-php/api/cart.php?id_member=1"

# 5. Test validate voucher
curl "http://localhost/belajar-web/toko-online-php/api/voucher.php?kode=PROMO10"
```

### **15 Menit Test (Recommended)**

1. Buka `test.html` di browser
2. Test semua section yang ada
3. Verifikasi response
4. Check database untuk perubahan data

### **30 Menit Test (Comprehensive)**

1. Import Postman collection
2. Run semua request di collection
3. Jalankan `run_tests.php`
4. Verifikasi database
5. Buat test report

---

## 💡 Tips Testing

1. **Gunakan Data Test Terpisah**
   - Jangan test di production database
   - Buat user test khusus
   - Backup database sebelum test

2. **Test Secara Sistematis**
   - Mulai dari authentication
   - Lanjut ke CRUD operations
   - Test integration flows terakhir

3. **Document Everything**
   - Screenshot hasil test
   - Catat error yang ditemukan
   - Simpan test data yang digunakan

4. **Cleanup After Testing**
   - Hapus data test yang tidak perlu
   - Reset auto-increment jika perlu
   - Restore database jika perlu

---

## 🐛 Troubleshooting

### **Error: Connection Refused**
```
Solusi:
- Pastikan Laragon/XAMPP running
- Check Apache & MySQL service
- Verify base URL correct
```

### **Error: 404 Not Found**
```
Solusi:
- Check file path benar
- Verify .htaccess jika ada
- Check URL spelling
```

### **Error: 500 Internal Server Error**
```
Solusi:
- Check PHP error log
- Verify database connection
- Check SQL syntax
```

### **Error: CORS**
```
Solusi:
- Sudah dihandle di semua API
- Check browser console
- Verify headers sent
```

---

## 📞 Support

Jika menemukan bug atau ada pertanyaan:

1. Check dokumentasi: `API_DOCUMENTATION.md`
2. Check test scenarios: `TEST_SCENARIOS.md`
3. Review code di file API terkait
4. Check database schema

---

**Happy Testing! 🧪✨**
