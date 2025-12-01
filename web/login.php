<?php
session_start();

// If already logged in, redirect to index
if (isset($_SESSION['member_id'])) {
    header('Location: index.php');
    exit;
}

$redirect = $_GET['redirect'] ?? 'index.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 20px;
        }
        
        .login-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        
        .login-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-banner h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        
        .login-banner p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .login-form-container {
            padding: 60px 40px;
        }
        
        .login-form-container h1 {
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .login-form-container .subtitle {
            color: var(--gray);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }
            
            .login-banner {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-banner">
                <h2>Selamat Datang Kembali!</h2>
                <p>Login untuk melanjutkan belanja dan nikmati berbagai promo menarik dari kami.</p>
                <div style="margin-top: 40px;">
                    <i class="fas fa-shopping-cart" style="font-size: 100px; opacity: 0.3;"></i>
                </div>
            </div>
            
            <div class="login-form-container">
                <h1>Login</h1>
                <p class="subtitle">Masuk ke akun Anda</p>
                
                <form id="login-form" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" placeholder="nama@email.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <label class="remember-me">
                            <input type="checkbox" id="remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="forgot-password">Lupa password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    
                    <div class="register-link">
                        Belum punya akun? <a href="register.php">Daftar sekarang</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script src="assets/js/main.js"></script>
    <script>
        async function handleLogin(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);
            
            try {
                const response = await fetch('<?php echo dirname($_SERVER['PHP_SELF'], 2); ?>/api/auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    // Store session data (in real app, this should be done server-side)
                    showSuccess('Login berhasil! Mengalihkan...');
                    
                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.href = '<?php echo $redirect; ?>';
                    }, 1000);
                } else {
                    showError(data.message || 'Login gagal');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Terjadi kesalahan saat login');
            }
        }
    </script>
</body>
</html>
