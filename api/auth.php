<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

function jsonResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}

// Get action type
$action = $_POST['action'] ?? null;

switch ($action) {
    
    // Member Login
    case 'login':
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$email || !$password) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: email, password'
            ]);
        }

        $stmt = $conn->prepare("SELECT id, nama, email, password, no_hp FROM client_member WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            jsonResponse(401, [
                'status' => false,
                'message' => 'Email atau password salah'
            ]);
        }

        // Verify password (assuming password is hashed with password_hash)
        if (password_verify($password, $result['password'])) {
            // Remove password from response
            unset($result['password']);
            
            jsonResponse(200, [
                'status' => true,
                'message' => 'Login berhasil',
                'data' => $result
            ]);
        } else {
            // Fallback for plain text password (not recommended for production)
            if ($password === $result['password']) {
                unset($result['password']);
                
                jsonResponse(200, [
                    'status' => true,
                    'message' => 'Login berhasil',
                    'data' => $result
                ]);
            } else {
                jsonResponse(401, [
                    'status' => false,
                    'message' => 'Email atau password salah'
                ]);
            }
        }
        break;

    // Member Register
    case 'register':
        $nama = $_POST['nama'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $no_hp = $_POST['no_hp'] ?? null;

        if (!$nama || !$email || !$password) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: nama, email, password'
            ]);
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Format email tidak valid'
            ]);
        }

        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT id FROM client_member WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Email sudah terdaftar'
            ]);
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO client_member (nama, email, password, no_hp) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $hashedPassword, $no_hp);

        if ($stmt->execute()) {
            $id = $conn->insert_id;
            
            jsonResponse(201, [
                'status' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'id' => $id,
                    'nama' => $nama,
                    'email' => $email,
                    'no_hp' => $no_hp
                ]
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal registrasi: ' . $conn->error
            ]);
        }
        break;

    // Admin Login
    case 'admin_login':
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$username || !$password) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: username, password'
            ]);
        }

        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            jsonResponse(401, [
                'status' => false,
                'message' => 'Username atau password salah'
            ]);
        }

        // Verify password (assuming password is hashed with password_hash)
        if (password_verify($password, $result['password'])) {
            unset($result['password']);
            
            jsonResponse(200, [
                'status' => true,
                'message' => 'Login berhasil',
                'data' => $result
            ]);
        } else {
            // Fallback for plain text password
            if ($password === $result['password']) {
                unset($result['password']);
                
                jsonResponse(200, [
                    'status' => true,
                    'message' => 'Login berhasil',
                    'data' => $result
                ]);
            } else {
                jsonResponse(401, [
                    'status' => false,
                    'message' => 'Username atau password salah'
                ]);
            }
        }
        break;

    // Verify Token (placeholder - implement JWT if needed)
    case 'verify':
        $id_member = $_POST['id_member'] ?? null;

        if (!$id_member) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_member'
            ]);
        }

        $stmt = $conn->prepare("SELECT id, nama, email, no_hp FROM client_member WHERE id = ?");
        $stmt->bind_param("i", $id_member);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            jsonResponse(200, [
                'status' => true,
                'message' => 'Token valid',
                'data' => $result
            ]);
        } else {
            jsonResponse(401, [
                'status' => false,
                'message' => 'Token tidak valid'
            ]);
        }
        break;

    default:
        jsonResponse(400, [
            'status' => false,
            'message' => 'Action tidak valid. Gunakan: login, register, admin_login, verify'
        ]);
}
