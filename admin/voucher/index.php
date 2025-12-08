<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/koneksi.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM voucher WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Voucher berhasil dihapus!";
    } else {
        $error = "Gagal menghapus voucher!";
    }
    mysqli_stmt_close($stmt);
}

// Get all vouchers
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    $sql = "SELECT * FROM voucher WHERE kode_voucher LIKE ? OR deskripsi LIKE ? ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $searchParam = "%$search%";
    mysqli_stmt_bind_param($stmt, 'ss', $searchParam, $searchParam);
} else {
    $sql = "SELECT * FROM voucher ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vouchers = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Voucher - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include '../includes/topbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <h1><i class="fas fa-ticket-alt"></i> Kelola Voucher</h1>
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Voucher
                </a>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Search -->
            <div class="card mb-20">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="filters-row">
                            <div class="filter-item">
                                <input type="text" name="search" placeholder="Cari kode voucher atau deskripsi..."
                                    class="form-control" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="filter-item">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <?php if ($search): ?>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Voucher Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kode Voucher</th>
                                    <th>Deskripsi</th>
                                    <th>Persentase</th>
                                    <th>Max Potongan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($vouchers)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data voucher</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($vouchers as $voucher): ?>
                                        <tr>
                                            <td><?php echo $voucher['id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($voucher['kode_voucher']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($voucher['deskripsi']); ?></td>
                                            <td><?php echo number_format($voucher['persentase'], 2); ?>%</td>
                                            <td>Rp <?php echo number_format($voucher['max_potongan'], 0, ',', '.'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($voucher['tanggal_mulai'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($voucher['tanggal_selesai'])); ?></td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $voucher['id']; ?>"
                                                    class="btn btn-xs btn-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?delete=<?php echo $voucher['id']; ?>"
                                                    class="btn btn-xs btn-danger" title="Hapus"
                                                    onclick="return confirm('Yakin ingin menghapus voucher ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>