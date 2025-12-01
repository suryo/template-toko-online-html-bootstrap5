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

    // GET - Read all discounts or active discounts for a product
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT d.*, b.nama_barang 
                                    FROM discount d 
                                    JOIN barang b ON d.id_barang = b.id 
                                    WHERE d.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Discount tidak ditemukan']);
            }
        } elseif (isset($_GET['id_barang'])) {
            // Get active discount for specific product
            $id_barang = (int) $_GET['id_barang'];
            $today = date('Y-m-d');
            
            $stmt = $conn->prepare("SELECT d.*, b.nama_barang 
                                    FROM discount d 
                                    JOIN barang b ON d.id_barang = b.id 
                                    WHERE d.id_barang = ? 
                                    AND d.tanggal_mulai <= ? 
                                    AND d.tanggal_selesai >= ?");
            $stmt->bind_param("iss", $id_barang, $today, $today);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            jsonResponse(200, ['status' => true, 'data' => $res]);
        } else {
            // Get all discounts
            $query = "SELECT d.*, b.nama_barang 
                      FROM discount d 
                      JOIN barang b ON d.id_barang = b.id";
            
            // Filter active discounts only
            if (isset($_GET['active']) && $_GET['active'] == '1') {
                $today = date('Y-m-d');
                $query .= " WHERE d.tanggal_mulai <= '$today' AND d.tanggal_selesai >= '$today'";
            }
            
            $res = $conn->query($query);
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new discount
    case 'POST':
        $id_barang = $_POST['id_barang'] ?? null;
        $persentase = $_POST['persentase'] ?? null;
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;

        if (!$id_barang || !$persentase || !$tanggal_mulai || !$tanggal_selesai) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_barang, persentase, tanggal_mulai, tanggal_selesai'
            ]);
        }

        if ($persentase < 0 || $persentase > 100) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Persentase harus antara 0-100'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO discount (id_barang, persentase, tanggal_mulai, tanggal_selesai) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $id_barang, $persentase, $tanggal_mulai, $tanggal_selesai);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Discount berhasil ditambahkan',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update discount
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $id_barang = $_POST['id_barang'] ?? null;
        $persentase = $_POST['persentase'] ?? null;
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;

        if (!$id_barang || !$persentase || !$tanggal_mulai || !$tanggal_selesai) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_barang, persentase, tanggal_mulai, tanggal_selesai'
            ]);
        }

        if ($persentase < 0 || $persentase > 100) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Persentase harus antara 0-100'
            ]);
        }

        $stmt = $conn->prepare("UPDATE discount SET id_barang = ?, persentase = ?, tanggal_mulai = ?, tanggal_selesai = ? WHERE id = ?");
        $stmt->bind_param("idssi", $id_barang, $persentase, $tanggal_mulai, $tanggal_selesai, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Discount ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Delete discount
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM discount WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Discount ID $id berhasil dihapus"
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
