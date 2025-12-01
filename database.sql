-- Database Schema for Toko Online

-- Table: member
CREATE TABLE IF NOT EXISTS `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('member','admin') DEFAULT 'member',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: barang
CREATE TABLE IF NOT EXISTS `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(200) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_kategori` (`id_kategori`),
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: alamat
CREATE TABLE IF NOT EXISTS `alamat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_member` int(11) NOT NULL,
  `label` varchar(50) DEFAULT 'Rumah',
  `penerima` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `kota` varchar(100) NOT NULL,
  `kode_pos` varchar(10) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_member` (`id_member`),
  CONSTRAINT `fk_alamat_member` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: keranjang
CREATE TABLE IF NOT EXISTS `keranjang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_member` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_member` (`id_member`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `fk_keranjang_member` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_keranjang_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: voucher
CREATE TABLE IF NOT EXISTS `voucher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) NOT NULL,
  `tipe` enum('percent','fixed') DEFAULT 'percent',
  `nilai` decimal(10,2) NOT NULL,
  `min_belanja` decimal(10,2) DEFAULT 0,
  `stok` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: penjualan (Orders)
CREATE TABLE IF NOT EXISTS `penjualan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_member` int(11) NOT NULL,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `alamat_pengiriman` text DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `metode_pengiriman` varchar(50) DEFAULT NULL,
  `ongkir` decimal(10,2) DEFAULT 0,
  `id_voucher` int(11) DEFAULT NULL,
  `potongan` decimal(10,2) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_member` (`id_member`),
  CONSTRAINT `fk_penjualan_member` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: detail_penjualan
CREATE TABLE IF NOT EXISTS `detail_penjualan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_penjualan` (`id_penjualan`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `fk_detail_penjualan_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_penjualan_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: review
CREATE TABLE IF NOT EXISTS `review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_member` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_member` (`id_member`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `fk_review_member` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: wishlist
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_member` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_member` (`id_member`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `fk_wishlist_member` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: notifikasi
CREATE TABLE IF NOT EXISTS `notifikasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_member` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_member` (`id_member`),
  CONSTRAINT `fk_notifikasi_member` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pembelian (Purchase from Supplier)
CREATE TABLE IF NOT EXISTS `pembelian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) NOT NULL,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_supplier` (`id_supplier`),
  CONSTRAINT `fk_pembelian_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: detail_pembelian
CREATE TABLE IF NOT EXISTS `detail_pembelian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `harga_beli` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_pembelian` (`id_pembelian`),
  KEY `id_barang` (`id_barang`),
  CONSTRAINT `fk_detail_pembelian_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_pembelian_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: shipment
CREATE TABLE IF NOT EXISTS `shipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) NOT NULL,
  `id_alamat` int(11) NOT NULL,
  `kurir` varchar(50) NOT NULL,
  `resi` varchar(100) NOT NULL,
  `status_pengiriman` varchar(50) DEFAULT 'dikirim',
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_terima` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_penjualan` (`id_penjualan`),
  KEY `id_alamat` (`id_alamat`),
  CONSTRAINT `fk_shipment_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_shipment_alamat` FOREIGN KEY (`id_alamat`) REFERENCES `alamat` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: payment
CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT 'belum dibayar',
  `tanggal_bayar` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_penjualan` (`id_penjualan`),
  CONSTRAINT `fk_payment_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin
INSERT INTO `member` (`nama`, `email`, `password`, `role`) VALUES
('Admin Toko', 'admin@tokoonline.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Password is 'password' (hashed)

-- Insert Sample Categories
INSERT INTO `kategori` (`nama_kategori`) VALUES
('Elektronik'),
('Pakaian Pria'),
('Pakaian Wanita'),
('Sepatu'),
('Aksesoris');

-- Insert Sample Suppliers
INSERT INTO `supplier` (`nama_supplier`, `alamat`, `no_hp`) VALUES
('PT. Elektronik Jaya', 'Jl. Mangga Dua No. 1, Jakarta', '081234567890'),
('CV. Fashion Indo', 'Jl. Tanah Abang No. 5, Jakarta', '081987654321');

-- Insert Sample Vouchers
INSERT INTO `voucher` (`kode`, `tipe`, `nilai`, `min_belanja`, `stok`, `start_date`, `end_date`) VALUES
('DISKON10', 'percent', 10.00, 100000, 100, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
('POTONGAN50RB', 'fixed', 50000.00, 500000, 50, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY));
