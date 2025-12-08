<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM voucher WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['delete_success'] = 'Voucher berhasil dihapus!';
    } else {
        $_SESSION['delete_error'] = 'Gagal menghapus voucher!';
    }
    mysqli_stmt_close($stmt);
}

header('Location: index.php');
exit;
