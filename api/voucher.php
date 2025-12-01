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

    // GET - Read all vouchers or check specific voucher
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM voucher WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Voucher tidak ditemukan']);
            }
        } elseif (isset($_GET['kode'])) {
            // Validate voucher code
            $kode = $_GET['kode'];
            $today = date('Y-m-d');
            
            $stmt = $conn->prepare("SELECT * FROM voucher 
                                    WHERE kode_voucher = ? 
                                    AND tanggal_mulai <= ? 
                                    AND tanggal_selesai >= ?");
            $stmt->bind_param("sss", $kode, $today, $today);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, [
                    'status' => true,
                    'valid' => true,
                    'data' => $res
                ]);
            } else {
                jsonResponse(200, [
                    'status' => true,
                    'valid' => false,
                    'message' => 'Voucher tidak valid atau sudah kadaluarsa'
                ]);
            }
        } else {
            // Get all vouchers
            $query = "SELECT * FROM voucher";
            
            // Filter active vouchers only
            if (isset($_GET['active']) && $_GET['active'] == '1') {
                $today = date('Y-m-d');
                $query .= " WHERE tanggal_mulai <= '$today' AND tanggal_selesai >= '$today'";
            }
            
            $res = $conn->query($query);
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new voucher
    case 'POST':
        $kode_voucher = $_POST['kode_voucher'] ?? null;
        $deskripsi = $_POST['deskripsi'] ?? null;
        $persentase = $_POST['persentase'] ?? null;
        $max_potongan = $_POST['max_potongan'] ?? null;
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;

        if (!$kode_voucher || !$persentase || !$tanggal_mulai || !$tanggal_selesai) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: kode_voucher, persentase, tanggal_mulai, tanggal_selesai'
            ]);
        }

        if ($persentase < 0 || $persentase > 100) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Persentase harus antara 0-100'
            ]);
        }

        // Check if voucher code already exists
        $checkStmt = $conn->prepare("SELECT id FROM voucher WHERE kode_voucher = ?");
        $checkStmt->bind_param("s", $kode_voucher);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Kode voucher sudah digunakan'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO voucher (kode_voucher, deskripsi, persentase, max_potongan, tanggal_mulai, tanggal_selesai) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddss", $kode_voucher, $deskripsi, $persentase, $max_potongan, $tanggal_mulai, $tanggal_selesai);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Voucher berhasil ditambahkan',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update voucher
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $kode_voucher = $_POST['kode_voucher'] ?? null;
        $deskripsi = $_POST['deskripsi'] ?? null;
        $persentase = $_POST['persentase'] ?? null;
        $max_potongan = $_POST['max_potongan'] ?? null;
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;

        if (!$kode_voucher || !$persentase || !$tanggal_mulai || !$tanggal_selesai) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: kode_voucher, persentase, tanggal_mulai, tanggal_selesai'
            ]);
        }

        if ($persentase < 0 || $persentase > 100) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Persentase harus antara 0-100'
            ]);
        }

        $stmt = $conn->prepare("UPDATE voucher SET kode_voucher = ?, deskripsi = ?, persentase = ?, max_potongan = ?, tanggal_mulai = ?, tanggal_selesai = ? WHERE id = ?");
        $stmt->bind_param("ssddssi", $kode_voucher, $deskripsi, $persentase, $max_potongan, $tanggal_mulai, $tanggal_selesai, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Voucher ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Delete voucher
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM voucher WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Voucher ID $id berhasil dihapus"
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
