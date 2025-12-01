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

    // GET - Read all suppliers or single supplier
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM supplier WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Supplier tidak ditemukan']);
            }
        } else {
            $res = $conn->query("SELECT * FROM supplier");
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new supplier
    case 'POST':
        $nama = $_POST['nama'] ?? null;
        $kontak = $_POST['kontak'] ?? null;
        $alamat = $_POST['alamat'] ?? null;

        if (!$nama || !$kontak) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: nama, kontak'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO supplier (nama, kontak, alamat) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $kontak, $alamat);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Supplier berhasil ditambahkan',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update supplier
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $nama = $_POST['nama'] ?? null;
        $kontak = $_POST['kontak'] ?? null;
        $alamat = $_POST['alamat'] ?? null;

        if (!$nama || !$kontak) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: nama, kontak'
            ]);
        }

        $stmt = $conn->prepare("UPDATE supplier SET nama = ?, kontak = ?, alamat = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama, $kontak, $alamat, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Supplier ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Delete supplier
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM supplier WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Supplier ID $id berhasil dihapus"
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
