<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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

if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

switch ($method) {

    // GET - Read all members or single member
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT id, nama, email, no_hp, created_at FROM client_member WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Member tidak ditemukan']);
            }
        } else {
            $res = $conn->query("SELECT id, nama, email, no_hp, created_at FROM client_member");
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Register new member
    case 'POST':
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

        // Hash password (gunakan password_hash untuk keamanan lebih baik)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO client_member (nama, email, password, no_hp) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $hashedPassword, $no_hp);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Member berhasil terdaftar',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update member
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $nama = $_POST['nama'] ?? null;
        $email = $_POST['email'] ?? null;
        $no_hp = $_POST['no_hp'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$nama || !$email) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: nama, email'
            ]);
        }

        // Update with or without password
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE client_member SET nama = ?, email = ?, password = ?, no_hp = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nama, $email, $hashedPassword, $no_hp, $id);
        } else {
            $stmt = $conn->prepare("UPDATE client_member SET nama = ?, email = ?, no_hp = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nama, $email, $no_hp, $id);
        }

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Member ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Delete member
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM client_member WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Member ID $id berhasil dihapus"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        break;

    default:
        jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}
