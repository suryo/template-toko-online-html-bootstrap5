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

    // GET - Read payment info
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT pay.*, p.total, p.id_member 
                                    FROM payment pay 
                                    JOIN penjualan p ON pay.id_penjualan = p.id 
                                    WHERE pay.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Payment tidak ditemukan']);
            }
        } elseif (isset($_GET['id_penjualan'])) {
            $id_penjualan = (int) $_GET['id_penjualan'];
            $stmt = $conn->prepare("SELECT * FROM payment WHERE id_penjualan = ?");
            $stmt->bind_param("i", $id_penjualan);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            jsonResponse(200, ['status' => true, 'data' => $res]);
        } else {
            // Get all payments
            $query = "SELECT pay.*, p.total, p.id_member 
                      FROM payment pay 
                      JOIN penjualan p ON pay.id_penjualan = p.id";
            
            // Filter by status if provided
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $query .= " WHERE pay.status = '$status'";
            }
            
            $query .= " ORDER BY pay.tanggal_bayar DESC";
            
            $res = $conn->query($query);
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new payment
    case 'POST':
        $id_penjualan = $_POST['id_penjualan'] ?? null;
        $metode = $_POST['metode'] ?? null;
        $status = $_POST['status'] ?? 'belum dibayar';
        $tanggal_bayar = $_POST['tanggal_bayar'] ?? null;

        if (!$id_penjualan || !$metode) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_penjualan, metode'
            ]);
        }

        $validStatuses = ['belum dibayar', 'lunas', 'gagal'];
        if (!in_array($status, $validStatuses)) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Status harus salah satu dari: ' . implode(', ', $validStatuses)
            ]);
        }

        // Check if payment already exists for this sale
        $checkStmt = $conn->prepare("SELECT id FROM payment WHERE id_penjualan = ?");
        $checkStmt->bind_param("i", $id_penjualan);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Payment untuk penjualan ini sudah ada'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO payment (id_penjualan, metode, status, tanggal_bayar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_penjualan, $metode, $status, $tanggal_bayar);

        if ($stmt->execute()) {
            // If status is lunas, update penjualan status to paid
            if ($status === 'lunas') {
                $updateSale = $conn->prepare("UPDATE penjualan SET status = 'paid' WHERE id = ?");
                $updateSale->bind_param("i", $id_penjualan);
                $updateSale->execute();
            }

            jsonResponse(201, [
                'status' => true,
                'message' => 'Payment berhasil dibuat',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update payment status
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $status = $_POST['status'] ?? null;
        $tanggal_bayar = $_POST['tanggal_bayar'] ?? null;

        if (!$status) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: status'
            ]);
        }

        $validStatuses = ['belum dibayar', 'lunas', 'gagal'];
        if (!in_array($status, $validStatuses)) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Status harus salah satu dari: ' . implode(', ', $validStatuses)
            ]);
        }

        // Get id_penjualan
        $getStmt = $conn->prepare("SELECT id_penjualan FROM payment WHERE id = ?");
        $getStmt->bind_param("i", $id);
        $getStmt->execute();
        $result = $getStmt->get_result()->fetch_assoc();

        if (!$result) {
            jsonResponse(404, ['status' => false, 'message' => 'Payment tidak ditemukan']);
        }

        if ($tanggal_bayar) {
            $stmt = $conn->prepare("UPDATE payment SET status = ?, tanggal_bayar = ? WHERE id = ?");
            $stmt->bind_param("ssi", $status, $tanggal_bayar, $id);
        } else {
            $stmt = $conn->prepare("UPDATE payment SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
        }

        if ($stmt->execute()) {
            // Update penjualan status based on payment status
            if ($status === 'lunas') {
                $updateSale = $conn->prepare("UPDATE penjualan SET status = 'paid' WHERE id = ?");
                $updateSale->bind_param("i", $result['id_penjualan']);
                $updateSale->execute();
            } elseif ($status === 'gagal') {
                $updateSale = $conn->prepare("UPDATE penjualan SET status = 'cancelled' WHERE id = ?");
                $updateSale->bind_param("i", $result['id_penjualan']);
                $updateSale->execute();
            }

            jsonResponse(200, [
                'status' => true,
                'message' => "Status payment ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal update: ' . $conn->error
            ]);
        }
        break;

    // DELETE - Delete payment
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM payment WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Payment ID $id berhasil dihapus"
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
