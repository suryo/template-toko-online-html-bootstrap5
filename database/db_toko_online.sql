/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : localhost:3306
 Source Schema         : db_toko_online

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 01/12/2025 18:58:53
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for alamat_pengiriman
-- ----------------------------
DROP TABLE IF EXISTS `alamat_pengiriman`;
CREATE TABLE `alamat_pengiriman`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_member` int NULL DEFAULT NULL,
  `nama_penerima` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `kota` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kode_pos` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_member`(`id_member` ASC) USING BTREE,
  CONSTRAINT `alamat_pengiriman_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `client_member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of alamat_pengiriman
-- ----------------------------
INSERT INTO `alamat_pengiriman` VALUES (1, 1, 'Andi Wijaya', 'Jl. Melati No. 10', 'Jakarta', '10110', '081234567890');
INSERT INTO `alamat_pengiriman` VALUES (2, 2, 'Siti Aminah', 'Jl. Mawar No. 12', 'Bandung', '40251', '082345678901');

-- ----------------------------
-- Table structure for barang
-- ----------------------------
DROP TABLE IF EXISTS `barang`;
CREATE TABLE `barang`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `id_kategori` int NULL DEFAULT NULL,
  `harga` decimal(12, 2) NULL DEFAULT NULL,
  `stok` int NULL DEFAULT 0,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_kategori`(`id_kategori` ASC) USING BTREE,
  CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `category_barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of barang
-- ----------------------------
INSERT INTO `barang` VALUES (1, 'Kipas Angin', 2, 150000.00, 103, 'Kipas angin 3 tingkat kecepatan');
INSERT INTO `barang` VALUES (2, 'TV LED 32 Inch', 1, 1500000.00, 50, 'TV LED resolusi HD 32 inch');
INSERT INTO `barang` VALUES (3, 'Blender', 2, 300000.00, 80, 'Blender kaca 1.5 liter');
INSERT INTO `barang` VALUES (4, 'Rice Cooker', 2, 400000.00, 60, 'Rice cooker 1.8L multifungsi');
INSERT INTO `barang` VALUES (6, 'coba', 2, 100000.00, 100, 'test');

-- ----------------------------
-- Table structure for cart
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_member` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `qty` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_member`(`id_member` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `client_member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cart
-- ----------------------------
INSERT INTO `cart` VALUES (1, 1, 3, 2);
INSERT INTO `cart` VALUES (2, 2, 1, 1);

-- ----------------------------
-- Table structure for category_barang
-- ----------------------------
DROP TABLE IF EXISTS `category_barang`;
CREATE TABLE `category_barang`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of category_barang
-- ----------------------------
INSERT INTO `category_barang` VALUES (1, 'Elektronik');
INSERT INTO `category_barang` VALUES (2, 'Peralatan Rumah Tangga');

-- ----------------------------
-- Table structure for client_member
-- ----------------------------
DROP TABLE IF EXISTS `client_member`;
CREATE TABLE `client_member`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of client_member
-- ----------------------------
INSERT INTO `client_member` VALUES (1, 'Andi Wijaya', 'andi@example.com', 'pass123', '081234567890', '2025-07-01 14:53:47');
INSERT INTO `client_member` VALUES (2, 'Siti Aminah', 'siti@example.com', 'pass456', '082345678901', '2025-07-01 14:53:47');

-- ----------------------------
-- Table structure for discount
-- ----------------------------
DROP TABLE IF EXISTS `discount`;
CREATE TABLE `discount`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `persentase` decimal(5, 2) NULL DEFAULT NULL,
  `tanggal_mulai` date NULL DEFAULT NULL,
  `tanggal_selesai` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `discount_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of discount
-- ----------------------------
INSERT INTO `discount` VALUES (1, 4, 5.00, '2024-07-01', '2024-07-15');

-- ----------------------------
-- Table structure for discount_category
-- ----------------------------
DROP TABLE IF EXISTS `discount_category`;
CREATE TABLE `discount_category`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_kategori` int NULL DEFAULT NULL,
  `persentase` decimal(5, 2) NULL DEFAULT NULL,
  `tanggal_mulai` date NULL DEFAULT NULL,
  `tanggal_selesai` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_kategori`(`id_kategori` ASC) USING BTREE,
  CONSTRAINT `discount_category_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `category_barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of discount_category
-- ----------------------------
INSERT INTO `discount_category` VALUES (1, 2, 10.00, '2024-07-01', '2024-07-31');

-- ----------------------------
-- Table structure for histori_harga
-- ----------------------------
DROP TABLE IF EXISTS `histori_harga`;
CREATE TABLE `histori_harga`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `harga_lama` decimal(12, 2) NULL DEFAULT NULL,
  `harga_baru` decimal(12, 2) NULL DEFAULT NULL,
  `tanggal_ubah` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `histori_harga_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of histori_harga
-- ----------------------------
INSERT INTO `histori_harga` VALUES (1, 4, 450000.00, 400000.00, '2024-07-01');

-- ----------------------------
-- Table structure for invoice
-- ----------------------------
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int NULL DEFAULT NULL,
  `nomor_invoice` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `total` decimal(12, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nomor_invoice`(`nomor_invoice` ASC) USING BTREE,
  INDEX `id_penjualan`(`id_penjualan` ASC) USING BTREE,
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice
-- ----------------------------
INSERT INTO `invoice` VALUES (1, 1, 'INV-001', '2024-07-03', 1900000.00);
INSERT INTO `invoice` VALUES (2, 2, 'INV-002', '2024-07-04', 400000.00);

-- ----------------------------
-- Table structure for log_aktivitas
-- ----------------------------
DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NULL DEFAULT NULL,
  `aktivitas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_user`(`id_user` ASC) USING BTREE,
  CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of log_aktivitas
-- ----------------------------
INSERT INTO `log_aktivitas` VALUES (1, 1, 'Menambahkan produk baru', '2025-07-01 14:53:47');
INSERT INTO `log_aktivitas` VALUES (2, 2, 'Memproses transaksi penjualan', '2025-07-01 14:53:47');

-- ----------------------------
-- Table structure for notifikasi
-- ----------------------------
DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_member` int NULL DEFAULT NULL,
  `isi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `sudah_dibaca` tinyint(1) NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_member`(`id_member` ASC) USING BTREE,
  CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `client_member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notifikasi
-- ----------------------------
INSERT INTO `notifikasi` VALUES (1, 1, 'Pesanan Anda telah dikirim.', 0, '2025-07-01 14:53:47');
INSERT INTO `notifikasi` VALUES (2, 2, 'Ulasan Anda telah diterima.', 0, '2025-07-01 14:53:47');

-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int NULL DEFAULT NULL,
  `metode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status` enum('belum dibayar','lunas','gagal') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'belum dibayar',
  `tanggal_bayar` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_penjualan`(`id_penjualan` ASC) USING BTREE,
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payment
-- ----------------------------
INSERT INTO `payment` VALUES (1, 1, 'Transfer BCA', 'lunas', '2024-07-03');
INSERT INTO `payment` VALUES (2, 2, 'Transfer Mandiri', 'lunas', '2024-07-04');

-- ----------------------------
-- Table structure for pembelian
-- ----------------------------
DROP TABLE IF EXISTS `pembelian`;
CREATE TABLE `pembelian`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_supplier` int NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `total` decimal(12, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_supplier`(`id_supplier` ASC) USING BTREE,
  CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pembelian
-- ----------------------------
INSERT INTO `pembelian` VALUES (1, 1, '2024-07-01', 3000000.00);
INSERT INTO `pembelian` VALUES (2, 2, '2024-07-02', 2500000.00);
INSERT INTO `pembelian` VALUES (3, 1, '2025-07-01', 150000.00);
INSERT INTO `pembelian` VALUES (4, 1, '2025-07-01', 300000.00);

-- ----------------------------
-- Table structure for pembelian_detail
-- ----------------------------
DROP TABLE IF EXISTS `pembelian_detail`;
CREATE TABLE `pembelian_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pembelian` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `qty` int NULL DEFAULT NULL,
  `harga` decimal(12, 2) NULL DEFAULT NULL,
  `subtotal` decimal(12, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_pembelian`(`id_pembelian` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `pembelian_detail_ibfk_1` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `pembelian_detail_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pembelian_detail
-- ----------------------------
INSERT INTO `pembelian_detail` VALUES (1, 1, 1, 10, 140000.00, 1400000.00);
INSERT INTO `pembelian_detail` VALUES (2, 1, 2, 5, 1400000.00, 7000000.00);
INSERT INTO `pembelian_detail` VALUES (3, 2, 3, 10, 280000.00, 2800000.00);
INSERT INTO `pembelian_detail` VALUES (4, 3, 1, 1, 150000.00, 150000.00);
INSERT INTO `pembelian_detail` VALUES (5, 4, 1, 2, 150000.00, 300000.00);

-- ----------------------------
-- Table structure for pengembalian_uang
-- ----------------------------
DROP TABLE IF EXISTS `pengembalian_uang`;
CREATE TABLE `pengembalian_uang`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_retur` int NULL DEFAULT NULL,
  `jumlah` decimal(12, 2) NULL DEFAULT NULL,
  `metode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal_refund` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_retur`(`id_retur` ASC) USING BTREE,
  CONSTRAINT `pengembalian_uang_ibfk_1` FOREIGN KEY (`id_retur`) REFERENCES `retur` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengembalian_uang
-- ----------------------------
INSERT INTO `pengembalian_uang` VALUES (1, 1, 400000.00, 'Transfer Mandiri', '2024-07-05');

-- ----------------------------
-- Table structure for penjualan
-- ----------------------------
DROP TABLE IF EXISTS `penjualan`;
CREATE TABLE `penjualan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_member` int NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `total` decimal(12, 2) NULL DEFAULT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'pending',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_member`(`id_member` ASC) USING BTREE,
  CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `client_member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of penjualan
-- ----------------------------
INSERT INTO `penjualan` VALUES (1, 1, '2024-07-03', 1900000.00, 'completed');
INSERT INTO `penjualan` VALUES (2, 2, '2024-07-04', 400000.00, 'completed');

-- ----------------------------
-- Table structure for penjualan_detail
-- ----------------------------
DROP TABLE IF EXISTS `penjualan_detail`;
CREATE TABLE `penjualan_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `qty` int NULL DEFAULT NULL,
  `harga` decimal(12, 2) NULL DEFAULT NULL,
  `subtotal` decimal(12, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_penjualan`(`id_penjualan` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `penjualan_detail_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `penjualan_detail_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of penjualan_detail
-- ----------------------------
INSERT INTO `penjualan_detail` VALUES (1, 1, 2, 1, 1500000.00, 1500000.00);
INSERT INTO `penjualan_detail` VALUES (2, 1, 4, 1, 400000.00, 400000.00);
INSERT INTO `penjualan_detail` VALUES (3, 2, 3, 1, 400000.00, 400000.00);

-- ----------------------------
-- Table structure for produk_bundle
-- ----------------------------
DROP TABLE IF EXISTS `produk_bundle`;
CREATE TABLE `produk_bundle`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_bundle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of produk_bundle
-- ----------------------------
INSERT INTO `produk_bundle` VALUES (1, 'Paket Hemat Rumah Tangga', 'Berisi kipas, blender, dan rice cooker');

-- ----------------------------
-- Table structure for produk_bundle_detail
-- ----------------------------
DROP TABLE IF EXISTS `produk_bundle_detail`;
CREATE TABLE `produk_bundle_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_bundle` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `qty` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_bundle`(`id_bundle` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `produk_bundle_detail_ibfk_1` FOREIGN KEY (`id_bundle`) REFERENCES `produk_bundle` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `produk_bundle_detail_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of produk_bundle_detail
-- ----------------------------
INSERT INTO `produk_bundle_detail` VALUES (1, 1, 1, 1);
INSERT INTO `produk_bundle_detail` VALUES (2, 1, 3, 1);
INSERT INTO `produk_bundle_detail` VALUES (3, 1, 4, 1);

-- ----------------------------
-- Table structure for retur
-- ----------------------------
DROP TABLE IF EXISTS `retur`;
CREATE TABLE `retur`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int NULL DEFAULT NULL,
  `tanggal` date NULL DEFAULT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `total` decimal(12, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_penjualan`(`id_penjualan` ASC) USING BTREE,
  CONSTRAINT `retur_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of retur
-- ----------------------------
INSERT INTO `retur` VALUES (1, 2, '2024-07-05', 'Barang rusak', 400000.00);

-- ----------------------------
-- Table structure for retur_detail
-- ----------------------------
DROP TABLE IF EXISTS `retur_detail`;
CREATE TABLE `retur_detail`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_retur` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `qty` int NULL DEFAULT NULL,
  `subtotal` decimal(12, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_retur`(`id_retur` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `retur_detail_ibfk_1` FOREIGN KEY (`id_retur`) REFERENCES `retur` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `retur_detail_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of retur_detail
-- ----------------------------
INSERT INTO `retur_detail` VALUES (1, 1, 3, 1, 400000.00);

-- ----------------------------
-- Table structure for review
-- ----------------------------
DROP TABLE IF EXISTS `review`;
CREATE TABLE `review`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_member` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  `rating` tinyint NULL DEFAULT NULL,
  `komentar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `tanggal_review` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_member`(`id_member` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `client_member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of review
-- ----------------------------
INSERT INTO `review` VALUES (1, 1, 2, 5, 'TV bagus dan tajam', '2024-07-06');
INSERT INTO `review` VALUES (2, 2, 3, 4, 'Blender lumayan', '2024-07-06');

-- ----------------------------
-- Table structure for shipment
-- ----------------------------
DROP TABLE IF EXISTS `shipment`;
CREATE TABLE `shipment`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_penjualan` int NULL DEFAULT NULL,
  `id_alamat` int NULL DEFAULT NULL,
  `kurir` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `resi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `status_pengiriman` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `tanggal_kirim` date NULL DEFAULT NULL,
  `tanggal_terima` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_penjualan`(`id_penjualan` ASC) USING BTREE,
  INDEX `id_alamat`(`id_alamat` ASC) USING BTREE,
  CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `shipment_ibfk_2` FOREIGN KEY (`id_alamat`) REFERENCES `alamat_pengiriman` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of shipment
-- ----------------------------
INSERT INTO `shipment` VALUES (1, 1, 1, 'JNE', 'JNE123456789', 'dikirim', '2024-07-03', '2024-07-04');
INSERT INTO `shipment` VALUES (2, 2, 2, 'SiCepat', 'SCP987654321', 'dikirim', '2024-07-04', '2024-07-05');

-- ----------------------------
-- Table structure for status_pengiriman
-- ----------------------------
DROP TABLE IF EXISTS `status_pengiriman`;
CREATE TABLE `status_pengiriman`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `deskripsi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of status_pengiriman
-- ----------------------------
INSERT INTO `status_pengiriman` VALUES (1, 'dikirim', 'Sedang dikirim');
INSERT INTO `status_pengiriman` VALUES (2, 'diterima', 'Sudah diterima');

-- ----------------------------
-- Table structure for stok_log
-- ----------------------------
DROP TABLE IF EXISTS `stok_log`;
CREATE TABLE `stok_log`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NULL DEFAULT NULL,
  `tipe` enum('masuk','keluar','koreksi') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `qty` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `stok_log_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stok_log
-- ----------------------------
INSERT INTO `stok_log` VALUES (1, 1, 'masuk', 10, 'Pembelian awal', '2025-07-01 14:53:47');
INSERT INTO `stok_log` VALUES (2, 2, 'masuk', 5, 'Pembelian awal', '2025-07-01 14:53:47');
INSERT INTO `stok_log` VALUES (3, 3, 'masuk', 10, 'Pembelian awal', '2025-07-01 14:53:47');
INSERT INTO `stok_log` VALUES (4, 3, 'keluar', 1, 'Retur penjualan', '2025-07-01 14:53:47');
INSERT INTO `stok_log` VALUES (5, 1, 'masuk', 1, 'Restok manual', '2025-07-01 14:56:37');
INSERT INTO `stok_log` VALUES (6, 1, 'masuk', 2, 'Pembelian via SP', '2025-07-01 15:05:55');

-- ----------------------------
-- Table structure for supplier
-- ----------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `kontak` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supplier
-- ----------------------------
INSERT INTO `supplier` VALUES (1, 'PT Maju Jaya', '08211234567', 'Jl. Industri No. 5');
INSERT INTO `supplier` VALUES (2, 'CV Sumber Makmur', '08129876543', 'Jl. Perdagangan No. 10');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `role` enum('admin','staff') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'admin1@gmail.com', 'adminpass', 'admin', '2025-07-01 14:53:47');
INSERT INTO `users` VALUES (2, 'staff1@gmail.com', 'staffpass', 'staff', '2025-07-01 14:53:47');

-- ----------------------------
-- Table structure for voucher
-- ----------------------------
DROP TABLE IF EXISTS `voucher`;
CREATE TABLE `voucher`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_voucher` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `deskripsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `persentase` decimal(5, 2) NULL DEFAULT NULL,
  `max_potongan` decimal(12, 2) NULL DEFAULT NULL,
  `tanggal_mulai` date NULL DEFAULT NULL,
  `tanggal_selesai` date NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `kode_voucher`(`kode_voucher` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of voucher
-- ----------------------------
INSERT INTO `voucher` VALUES (1, 'PROMO10', 'Diskon 10%', 10.00, 50000.00, '2024-07-01', '2024-07-31');

-- ----------------------------
-- Table structure for voucher_pelanggan
-- ----------------------------
DROP TABLE IF EXISTS `voucher_pelanggan`;
CREATE TABLE `voucher_pelanggan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_voucher` int NULL DEFAULT NULL,
  `id_member` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_voucher`(`id_voucher` ASC) USING BTREE,
  INDEX `id_member`(`id_member` ASC) USING BTREE,
  CONSTRAINT `voucher_pelanggan_ibfk_1` FOREIGN KEY (`id_voucher`) REFERENCES `voucher` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `voucher_pelanggan_ibfk_2` FOREIGN KEY (`id_member`) REFERENCES `client_member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of voucher_pelanggan
-- ----------------------------
INSERT INTO `voucher_pelanggan` VALUES (1, 1, 1);

-- ----------------------------
-- Table structure for wishlist
-- ----------------------------
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_member` int NULL DEFAULT NULL,
  `id_barang` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_member`(`id_member` ASC) USING BTREE,
  INDEX `id_barang`(`id_barang` ASC) USING BTREE,
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `client_member` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wishlist
-- ----------------------------
INSERT INTO `wishlist` VALUES (1, 1, 4);
INSERT INTO `wishlist` VALUES (2, 2, 2);

-- ----------------------------
-- View structure for view_penjualan_lengkap
-- ----------------------------
DROP VIEW IF EXISTS `view_penjualan_lengkap`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_penjualan_lengkap` AS select `p`.`id` AS `id`,`cm`.`nama` AS `nama`,`p`.`tanggal` AS `tanggal`,`p`.`total` AS `total` from (`penjualan` `p` join `client_member` `cm` on((`cm`.`id` = `p`.`id_member`)));

-- ----------------------------
-- Procedure structure for laporan_penjualan_harian
-- ----------------------------
DROP PROCEDURE IF EXISTS `laporan_penjualan_harian`;
delimiter ;;
CREATE PROCEDURE `laporan_penjualan_harian`(IN tanggal_input DATE)
BEGIN
    SELECT 
        p.id AS id_penjualan,
        cm.nama AS nama_pelanggan,
        p.tanggal,
        SUM(pd.subtotal) AS total_transaksi
    FROM penjualan p
    JOIN penjualan_detail pd ON pd.id_penjualan = p.id
    JOIN client_member cm ON cm.id = p.id_member
    WHERE p.tanggal = tanggal_input
    GROUP BY p.id;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for simpan_pembelian_dengan_cek_stok
-- ----------------------------
DROP PROCEDURE IF EXISTS `simpan_pembelian_dengan_cek_stok`;
delimiter ;;
CREATE PROCEDURE `simpan_pembelian_dengan_cek_stok`(IN p_barang_id INT,
    IN p_qty INT)
BEGIN
    DECLARE v_stok INT;
    DECLARE v_total DECIMAL(12,2);
    DECLARE v_pembelian_id INT;

    SELECT stok INTO v_stok FROM barang WHERE id = p_barang_id;

    IF v_stok >= p_qty THEN
        START TRANSACTION;

        SET v_total = 150000 * p_qty;

        INSERT INTO pembelian (id_supplier, tanggal, total) VALUES (1, CURDATE(), v_total);
        SET v_pembelian_id = LAST_INSERT_ID();

        INSERT INTO pembelian_detail (id_pembelian, id_barang, qty, harga, subtotal)
        VALUES (v_pembelian_id, p_barang_id, p_qty, 150000, v_total);

        UPDATE barang SET stok = stok + p_qty WHERE id = p_barang_id;

        INSERT INTO stok_log (id_barang, tipe, qty, keterangan)
        VALUES (p_barang_id, 'masuk', p_qty, 'Pembelian via SP');

        COMMIT;
    ELSE
        SELECT CONCAT('Stok tidak cukup. Tersisa: ', v_stok) AS pesan;
    END IF;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table penjualan_detail
-- ----------------------------
DROP TRIGGER IF EXISTS `after_insert_penjualan_detail`;
delimiter ;;
CREATE TRIGGER `after_insert_penjualan_detail` AFTER INSERT ON `penjualan_detail` FOR EACH ROW BEGIN
    INSERT INTO stok_log (id_barang, tipe, qty, keterangan)
    VALUES (NEW.id_barang, 'keluar', NEW.qty, CONCAT('Penjualan ID ', NEW.id_penjualan));
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
