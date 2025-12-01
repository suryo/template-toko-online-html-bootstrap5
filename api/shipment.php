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

    // GET - Read shipment info
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT s.*, p.total, a.nama_penerima, a.alamat, a.kota 
                                    FROM shipment s 
                                    JOIN penjualan p ON s.id_penjualan = p.id 
                                    JOIN alamat_pengiriman a ON s.id_alamat = a.id 
                                    WHERE s.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Shipment tidak ditemukan']);
            }
        } elseif (isset($_GET['id_penjualan'])) {
            $id_penjualan = (int) $_GET['id_penjualan'];
            $stmt = $conn->prepare("SELECT s.*, a.nama_penerima, a.alamat, a.kota, a.kode_pos, a.no_hp 
                                    FROM shipment s 
                                    JOIN alamat_pengiriman a ON s.id_alamat = a.id 
                                    WHERE s.id_penjualan = ?");
            $stmt->bind_param("i", $id_penjualan);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            jsonResponse(200, ['status' => true, 'data' => $res]);
        } elseif (isset($_GET['resi'])) {
            // Track shipment by resi number
            $resi = $_GET['resi'];
            $stmt = $conn->prepare("SELECT s.*, p.total, a.nama_penerima, a.alamat, a.kota 
                                    FROM shipment s 
                                    JOIN penjualan p ON s.id_penjualan = p.id 
                                    JOIN alamat_pengiriman a ON s.id_alamat = a.id 
                                    WHERE s.resi = ?");
            $stmt->bind_param("s", $resi);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Resi tidak ditemukan']);
            }
        } else {
            $res = $conn->query("SELECT s.*, p.id_member, a.nama_penerima, a.kota 
                                FROM shipment s 
                                JOIN penjualan p ON s.id_penjualan = p.id 
                                JOIN alamat_pengiriman a ON s.id_alamat = a.id 
                                ORDER BY s.tanggal_kirim DESC");
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new shipment
    case 'POST':
        $id_penjualan = $_POST['id_penjualan'] ?? null;
        $id_alamat = $_POST['id_alamat'] ?? null;
        $kurir = $_POST['kurir'] ?? null;
        $resi = $_POST['resi'] ?? null;
        $tanggal_kirim = $_POST['tanggal_kirim'] ?? date('Y-m-d');
        $status_pengiriman = $_POST['status_pengiriman'] ?? 'dikirim';

        if (!$id_penjualan || !$id_alamat || !$kurir || !$resi) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_penjualan, id_alamat, kurir, resi'
            ]);
        }

        // Check if shipment already exists for this sale
        $checkStmt = $conn->prepare("SELECT id FROM shipment WHERE id_penjualan = ?");
        $checkStmt->bind_param("i", $id_penjualan);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Shipment untuk penjualan ini sudah ada'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO shipment (id_penjualan, id_alamat, kurir, resi, status_pengiriman, tanggal_kirim) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $id_penjualan, $id_alamat, $kurir, $resi, $status_pengiriman, $tanggal_kirim);

        if ($stmt->execute()) {
            // Update penjualan status to shipped
            $updateSale = $conn->prepare("UPDATE penjualan SET status = 'shipped' WHERE id = ?");
            $updateSale->bind_param("i", $id_penjualan);
            $updateSale->execute();

            jsonResponse(201, [
                'status' => true,
                'message' => 'Shipment berhasil dibuat',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update shipment status
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $status_pengiriman = $_POST['status_pengiriman'] ?? null;
        $tanggal_terima = $_POST['tanggal_terima'] ?? null;

        if (!$status_pengiriman) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: status_pengiriman'
            ]);
        }

        // Get id_penjualan
        $getStmt = $conn->prepare("SELECT id_penjualan FROM shipment WHERE id = ?");
        $getStmt->bind_param("i", $id);
        $getStmt->execute();
        $result = $getStmt->get_result()->fetch_assoc();

        if (!$result) {
            jsonResponse(404, ['status' => false, 'message' => 'Shipment tidak ditemukan']);
        }

        if ($tanggal_terima) {
            $stmt = $conn->prepare("UPDATE shipment SET status_pengiriman = ?, tanggal_terima = ? WHERE id = ?");
            $stmt->bind_param("ssi", $status_pengiriman, $tanggal_terima, $id);
        } else {
            $stmt = $conn->prepare("UPDATE shipment SET status_pengiriman = ? WHERE id = ?");
            $stmt->bind_param("si", $status_pengiriman, $id);
        }

        if ($stmt->execute()) {
            // If status is 'diterima', update penjualan status to completed
            if ($status_pengiriman === 'diterima') {
                $updateSale = $conn->prepare("UPDATE penjualan SET status = 'completed' WHERE id = ?");
                $updateSale->bind_param("i", $result['id_penjualan']);
                $updateSale->execute();
            }

            jsonResponse(200, [
                'status' => true,
                'message' => "Status shipment ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal update: ' . $conn->error
            ]);
        }
        break;

    // DELETE - Delete shipment
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM shipment WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Shipment ID $id berhasil dihapus"
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
