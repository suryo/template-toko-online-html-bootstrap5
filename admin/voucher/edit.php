<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/koneksi.php';

$error = '';
$success = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Get voucher data
$sql = "SELECT * FROM voucher WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$voucher = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$voucher) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_voucher = trim($_POST['kode_voucher'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $persentase = trim($_POST['persentase'] ?? '');
    $max_potongan = trim($_POST['max_potongan'] ?? '');
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');

    // Validation
    if (empty($kode_voucher) || empty($deskripsi) || empty($persentase) || empty($max_potongan) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
        $error = 'Semua field wajib diisi!';
    } elseif (!is_numeric($persentase) || $persentase < 0 || $persentase > 100) {
        $error = 'Persentase harus antara 0-100!';
    } elseif (!is_numeric($max_potongan) || $max_potongan < 0) {
        $error = 'Max potongan harus berupa angka positif!';
    } elseif (strtotime($tanggal_selesai) < strtotime($tanggal_mulai)) {
        $error = 'Tanggal selesai harus setelah tanggal mulai!';
    } else {
        // Check if voucher code already exists (except current voucher)
        $checkSql = "SELECT id FROM voucher WHERE kode_voucher = ? AND id != ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, 'si', $kode_voucher, $id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            $error = 'Kode voucher sudah digunakan!';
        } else {
            // Update voucher
            $sql = "UPDATE voucher SET kode_voucher = ?, deskripsi = ?, persentase = ?, max_potongan = ?, 
                    tanggal_mulai = ?, tanggal_selesai = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                'ssddssi',
                $kode_voucher,
                $deskripsi,
                $persentase,
                $max_potongan,
                $tanggal_mulai,
                $tanggal_selesai,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                $success = 'Voucher berhasil diupdate!';
                // Reload voucher data
                $voucher['kode_voucher'] = $kode_voucher;
                $voucher['deskripsi'] = $deskripsi;
                $voucher['persentase'] = $persentase;
                $voucher['max_potongan'] = $max_potongan;
                $voucher['tanggal_mulai'] = $tanggal_mulai;
                $voucher['tanggal_selesai'] = $tanggal_selesai;
            } else {
                $error = 'Gagal mengupdate voucher: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($checkStmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Voucher - Admin Panel</title>
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
                <h1><i class="fas fa-edit"></i> Edit Voucher</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Kode Voucher *</label>
                            <input type="text" name="kode_voucher" class="form-control" required
                                value="<?php echo htmlspecialchars($voucher['kode_voucher']); ?>"
                                placeholder="Contoh: PROMO10">
                        </div>

                        <div class="form-group">
                            <label>Deskripsi *</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required
                                placeholder="Deskripsi voucher"><?php echo htmlspecialchars($voucher['deskripsi']); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Persentase (%) *</label>
                                <input type="number" name="persentase" class="form-control" required
                                    min="0" max="100" step="0.01"
                                    value="<?php echo htmlspecialchars($voucher['persentase']); ?>"
                                    placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label>Max Potongan (Rp) *</label>
                                <input type="number" name="max_potongan" class="form-control" required
                                    min="0" step="0.01"
                                    value="<?php echo htmlspecialchars($voucher['max_potongan']); ?>"
                                    placeholder="50000">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Tanggal Mulai *</label>
                                <input type="date" name="tanggal_mulai" class="form-control" required
                                    value="<?php echo htmlspecialchars($voucher['tanggal_mulai']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Tanggal Selesai *</label>
                                <input type="date" name="tanggal_selesai" class="form-control" required
                                    value="<?php echo htmlspecialchars($voucher['tanggal_selesai']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>