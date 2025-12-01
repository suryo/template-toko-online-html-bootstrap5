<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
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

// Deteksi method “asli”
$method = $_SERVER['REQUEST_METHOD'];

// Override method kalau ada _method di POST (PUT / DELETE)
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

switch ($method) {

    // =======================
    // READ (GET)
    // =======================
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM barang WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Data tidak ditemukan']);
            }
        } else {
            $res = $conn->query("SELECT * FROM barang");
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // =======================
    // CREATE (POST) - form-data
    // =======================
    case 'POST':
        // INSERT normal (tanpa _method)
        $nama_barang = $_POST['nama_barang'] ?? null;
        $id_kategori = $_POST['id_kategori'] ?? null;
        $harga       = $_POST['harga'] ?? null;
        $stok        = $_POST['stok'] ?? null;
        $deskripsi   = $_POST['deskripsi'] ?? '';

        if (!$nama_barang || !$id_kategori || !$harga || !$stok) {
            jsonResponse(400, [
                'status'  => false,
                'message' => 'Field wajib: nama_barang, id_kategori, harga, stok'
            ]);
        }

        $stmt = $conn->prepare(
            "INSERT INTO barang (nama_barang, id_kategori, harga, stok, deskripsi)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sidis", $nama_barang, $id_kategori, $harga, $stok, $deskripsi);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status'  => true,
                'message' => 'Barang berhasil ditambahkan',
                'id'      => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status'  => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update product
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id          = (int) $_GET['id'];
        $nama_barang = $_POST['nama_barang'] ?? null;
        $id_kategori = $_POST['id_kategori'] ?? null;
        $harga       = $_POST['harga'] ?? null;
        $stok        = $_POST['stok'] ?? null;
        $deskripsi   = $_POST['deskripsi'] ?? '';

        if (!$nama_barang || !$id_kategori || !$harga) {
            jsonResponse(400, [
                'status'  => false,
                'message' => 'Field wajib: nama_barang, id_kategori, harga'
            ]);
        }

        $stmt = $conn->prepare(
            "UPDATE barang
             SET nama_barang = ?, id_kategori = ?, harga = ?, stok = ?, deskripsi = ?
             WHERE id = ?"
        );
        $stmt->bind_param("sidisi", $nama_barang, $id_kategori, $harga, $stok, $deskripsi, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status'  => true,
                'message' => "Barang ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status'  => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // =======================
    // DELETE (boleh pakai method DELETE asli,
    // atau POST + _method=DELETE)
    // =======================
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status'  => true,
                'message' => "Barang ID $id berhasil dihapus"
            ]);
        } else {
            jsonResponse(404, [
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        break;

    default:
        jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}
