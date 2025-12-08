<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

function sendResponse($statusCode, $data)
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}

$action = isset($_POST['action']) ? $_POST['action'] : null;

if ($action === 'admin_login') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if (empty($username) || empty($password)) {
        sendResponse(400, [
            'status' => false,
            'message' => 'Username dan password wajib diisi'
        ]);
    }

    $sql = "SELECT id, username, password, role FROM users WHERE username = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        sendResponse(500, [
            'status' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        sendResponse(401, [
            'status' => false,
            'message' => 'Username tidak ditemukan atau bukan admin'
        ]);
    }

    if ($password === $user['password']) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];

        sendResponse(200, [
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ]);
    } else {
        sendResponse(401, [
            'status' => false,
            'message' => 'Password salah'
        ]);
    }
} elseif ($action === 'login') {
    $username = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if (empty($username) || empty($password)) {
        sendResponse(400, [
            'status' => false,
            'message' => 'Username dan password wajib diisi'
        ]);
    }

    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        sendResponse(500, [
            'status' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        sendResponse(401, [
            'status' => false,
            'message' => 'Username tidak ditemukan'
        ]);
    }

    if ($password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        sendResponse(200, [
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ]);
    } else {
        sendResponse(401, [
            'status' => false,
            'message' => 'Password salah'
        ]);
    }
} else {
    sendResponse(400, [
        'status' => false,
        'message' => 'Action tidak valid'
    ]);
}
