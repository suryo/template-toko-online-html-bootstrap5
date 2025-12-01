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

    // GET - Read all sales or single sale with details
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            
            // Get sale header
            $stmt = $conn->prepare("SELECT p.*, cm.nama as nama_member, cm.email 
                                    FROM penjualan p 
                                    JOIN client_member cm ON p.id_member = cm.id 
                                    WHERE p.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $sale = $stmt->get_result()->fetch_assoc();

            if (!$sale) {
                jsonResponse(404, ['status' => false, 'message' => 'Penjualan tidak ditemukan']);
            }

            // Get sale details
            $detailStmt = $conn->prepare("SELECT pd.*, b.nama_barang 
                                          FROM penjualan_detail pd 
                                          JOIN barang b ON pd.id_barang = b.id 
                                          WHERE pd.id_penjualan = ?");
            $detailStmt->bind_param("i", $id);
            $detailStmt->execute();
            $detailRes = $detailStmt->get_result();
            
            $details = [];
            while ($detail = $detailRes->fetch_assoc()) {
                $details[] = $detail;
            }

            $sale['details'] = $details;

            jsonResponse(200, ['status' => true, 'data' => $sale]);
        } else {
            // Get all sales with member info
            $query = "SELECT p.*, cm.nama as nama_member 
                      FROM penjualan p 
                      JOIN client_member cm ON p.id_member = cm.id";
            
            // Filter by member if provided
            if (isset($_GET['id_member'])) {
                $id_member = (int) $_GET['id_member'];
                $query .= " WHERE p.id_member = $id_member";
            }
            
            // Filter by status if provided
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $connector = strpos($query, 'WHERE') !== false ? ' AND' : ' WHERE';
                $query .= "$connector p.status = '$status'";
            }
            
            $query .= " ORDER BY p.tanggal DESC";
            
            $res = $conn->query($query);
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new sale (checkout)
    case 'POST':
        $id_member = $_POST['id_member'] ?? null;
        $items = $_POST['items'] ?? null; // JSON array of items

        if (!$id_member || !$items) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_member, items (array)'
            ]);
        }

        // Decode items if it's JSON string
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!is_array($items) || empty($items)) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Items harus berupa array dan tidak boleh kosong'
            ]);
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            $total = 0;
            $tanggal = date('Y-m-d');

            // Calculate total and validate stock
            foreach ($items as $item) {
                $id_barang = $item['id_barang'];
                $qty = $item['qty'];

                // Check stock
                $checkStmt = $conn->prepare("SELECT stok, harga FROM barang WHERE id = ?");
                $checkStmt->bind_param("i", $id_barang);
                $checkStmt->execute();
                $product = $checkStmt->get_result()->fetch_assoc();

                if (!$product) {
                    throw new Exception("Barang ID $id_barang tidak ditemukan");
                }

                if ($product['stok'] < $qty) {
                    throw new Exception("Stok barang ID $id_barang tidak mencukupi");
                }

                $total += $product['harga'] * $qty;
            }

            // Insert penjualan header
            $stmt = $conn->prepare("INSERT INTO penjualan (id_member, tanggal, total, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("isd", $id_member, $tanggal, $total);
            $stmt->execute();
            $id_penjualan = $conn->insert_id;

            // Insert penjualan details and update stock
            foreach ($items as $item) {
                $id_barang = $item['id_barang'];
                $qty = $item['qty'];

                // Get current price
                $priceStmt = $conn->prepare("SELECT harga FROM barang WHERE id = ?");
                $priceStmt->bind_param("i", $id_barang);
                $priceStmt->execute();
                $harga = $priceStmt->get_result()->fetch_assoc()['harga'];

                $subtotal = $harga * $qty;

                // Insert detail
                $detailStmt = $conn->prepare("INSERT INTO penjualan_detail (id_penjualan, id_barang, qty, harga, subtotal) VALUES (?, ?, ?, ?, ?)");
                $detailStmt->bind_param("iiidd", $id_penjualan, $id_barang, $qty, $harga, $subtotal);
                $detailStmt->execute();

                // Update stock
                $updateStmt = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
                $updateStmt->bind_param("ii", $qty, $id_barang);
                $updateStmt->execute();
            }

            // Clear cart for this member
            $clearCart = $conn->prepare("DELETE FROM cart WHERE id_member = ?");
            $clearCart->bind_param("i", $id_member);
            $clearCart->execute();

            $conn->commit();

            jsonResponse(201, [
                'status' => true,
                'message' => 'Penjualan berhasil dibuat',
                'id' => $id_penjualan,
                'total' => $total
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal membuat penjualan: ' . $e->getMessage()
            ]);
        }
        break;

    // PUT - Update sale status
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $status = $_POST['status'] ?? null;

        $validStatuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
        
        if (!$status || !in_array($status, $validStatuses)) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Status harus salah satu dari: ' . implode(', ', $validStatuses)
            ]);
        }

        $stmt = $conn->prepare("UPDATE penjualan SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Status penjualan ID $id berhasil diupdate menjadi $status"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Cancel sale (only if status is pending)
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];

        // Check if sale exists and is pending
        $checkStmt = $conn->prepare("SELECT status FROM penjualan WHERE id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();

        if (!$result) {
            jsonResponse(404, ['status' => false, 'message' => 'Penjualan tidak ditemukan']);
        }

        if ($result['status'] !== 'pending') {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Hanya penjualan dengan status pending yang bisa dibatalkan'
            ]);
        }

        $conn->begin_transaction();

        try {
            // Restore stock
            $detailStmt = $conn->prepare("SELECT id_barang, qty FROM penjualan_detail WHERE id_penjualan = ?");
            $detailStmt->bind_param("i", $id);
            $detailStmt->execute();
            $details = $detailStmt->get_result();

            while ($detail = $details->fetch_assoc()) {
                $updateStock = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
                $updateStock->bind_param("ii", $detail['qty'], $detail['id_barang']);
                $updateStock->execute();
            }

            // Delete details
            $deleteDetails = $conn->prepare("DELETE FROM penjualan_detail WHERE id_penjualan = ?");
            $deleteDetails->bind_param("i", $id);
            $deleteDetails->execute();

            // Delete sale
            $deleteSale = $conn->prepare("DELETE FROM penjualan WHERE id = ?");
            $deleteSale->bind_param("i", $id);
            $deleteSale->execute();

            $conn->commit();

            jsonResponse(200, [
                'status' => true,
                'message' => "Penjualan ID $id berhasil dibatalkan dan stok dikembalikan"
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal membatalkan penjualan: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}
