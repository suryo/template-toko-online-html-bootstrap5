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

    // GET - Read all addresses or addresses for specific member
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM alamat_pengiriman WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Alamat tidak ditemukan']);
            }
        } elseif (isset($_GET['id_member'])) {
            $id_member = (int) $_GET['id_member'];
            $stmt = $conn->prepare("SELECT * FROM alamat_pengiriman WHERE id_member = ?");
            $stmt->bind_param("i", $id_member);
            $stmt->execute();
            $res = $stmt->get_result();
            
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        } else {
            $res = $conn->query("SELECT * FROM alamat_pengiriman");
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new shipping address
    case 'POST':
        $id_member = $_POST['id_member'] ?? null;
        $nama_penerima = $_POST['nama_penerima'] ?? null;
        $alamat = $_POST['alamat'] ?? null;
        $kota = $_POST['kota'] ?? null;
        $kode_pos = $_POST['kode_pos'] ?? null;
        $no_hp = $_POST['no_hp'] ?? null;

        if (!$id_member || !$nama_penerima || !$alamat || !$kota || !$no_hp) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_member, nama_penerima, alamat, kota, no_hp'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO alamat_pengiriman (id_member, nama_penerima, alamat, kota, kode_pos, no_hp) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $id_member, $nama_penerima, $alamat, $kota, $kode_pos, $no_hp);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Alamat pengiriman berhasil ditambahkan',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update shipping address
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $nama_penerima = $_POST['nama_penerima'] ?? null;
        $alamat = $_POST['alamat'] ?? null;
        $kota = $_POST['kota'] ?? null;
        $kode_pos = $_POST['kode_pos'] ?? null;
        $no_hp = $_POST['no_hp'] ?? null;

        if (!$nama_penerima || !$alamat || !$kota || !$no_hp) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: nama_penerima, alamat, kota, no_hp'
            ]);
        }

        $stmt = $conn->prepare("UPDATE alamat_pengiriman SET nama_penerima = ?, alamat = ?, kota = ?, kode_pos = ?, no_hp = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nama_penerima, $alamat, $kota, $kode_pos, $no_hp, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Alamat pengiriman ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Delete shipping address
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM alamat_pengiriman WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Alamat pengiriman ID $id berhasil dihapus"
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
