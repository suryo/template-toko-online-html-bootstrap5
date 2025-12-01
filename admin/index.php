<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Toko Online</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1>Admin Panel</h1>
                <p>Toko Online Management System</p>
            </div>
            
            <form id="admin-login-form" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label>
                        <i class="fas fa-user"></i>
                        Username
                    </label>
                    <input type="text" id="username" placeholder="Masukkan username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <input type="password" id="password" placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="login-footer">
                <p>&copy; 2024 Toko Online. All rights reserved.</p>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        const API_BASE = 'http://localhost/belajar-web/toko-online-php/api';
        
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast toast-${type} show`;
            
            setTimeout(() => {
                toast.className = 'toast';
            }, 3000);
        }
        
        function showSuccess(message) {
            showToast(message, 'success');
        }
        
        function showError(message) {
            showToast(message, 'error');
        }
        
        async function handleLogin(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            const formData = new FormData();
            formData.append('action', 'admin_login');
            formData.append('username', username);
            formData.append('password', password);
            
            try {
                const response = await fetch(API_BASE + '/auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status) {
                    showSuccess('Login berhasil! Mengalihkan...');
                    
                    // Store admin session
                    sessionStorage.setItem('admin_id', data.data.id);
                    sessionStorage.setItem('admin_username', data.data.username);
                    sessionStorage.setItem('admin_role', data.data.role);
                    
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
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
