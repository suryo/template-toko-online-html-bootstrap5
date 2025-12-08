<?php
session_start();

require_once __DIR__ . '/../config/koneksi.php'; 

$error = '';

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

   
    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
       
        // Use 'users' table
        $sql  = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $res  = mysqli_stmt_get_result($stmt);

        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);

            // Plain text comparison
            if ($password === $user['password']) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: ../index.php');
                }
                exit;
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Email tidak ditemukan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Nama Toko Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style-kopi.css">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="col-lg-4 col-md-6 col-sm-8">
        <div class="card shadow-lg p-4">
            <h2 class="text-center mb-4 text-primary">Login ke Akun Anda</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        required
                        autofocus
                    >
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        required
                    >
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Ingat Saya</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
            </form>

            <p class="text-center small">
                Belum punya akun? <a href="#">Daftar Sekarang</a>
            </p>
            <p class="text-center small mt-2">
                <a href="#">Lupa Password?</a>
            </p>
        </div>
        <p class="text-center mt-3"><a href="../index.php" class="text-muted small">Kembali ke Beranda</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
