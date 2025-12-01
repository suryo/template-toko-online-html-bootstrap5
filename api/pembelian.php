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

    // GET - Read all purchases or single purchase with details
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            
            // Get purchase header
            $stmt = $conn->prepare("SELECT p.*, s.nama as nama_supplier 
                                    FROM pembelian p 
                                    JOIN supplier s ON p.id_supplier = s.id 
                                    WHERE p.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $purchase = $stmt->get_result()->fetch_assoc();

            if (!$purchase) {
                jsonResponse(404, ['status' => false, 'message' => 'Pembelian tidak ditemukan']);
            }

            // Get purchase details
            $detailStmt = $conn->prepare("SELECT pd.*, b.nama_barang 
                                          FROM pembelian_detail pd 
                                          JOIN barang b ON pd.id_barang = b.id 
                                          WHERE pd.id_pembelian = ?");
            $detailStmt->bind_param("i", $id);
            $detailStmt->execute();
            $detailRes = $detailStmt->get_result();
            
            $details = [];
            while ($detail = $detailRes->fetch_assoc()) {
                $details[] = $detail;
            }

            $purchase['details'] = $details;

            jsonResponse(200, ['status' => true, 'data' => $purchase]);
        } else {
            // Get all purchases with supplier info
            $query = "SELECT p.*, s.nama as nama_supplier 
                      FROM pembelian p 
                      JOIN supplier s ON p.id_supplier = s.id 
                      ORDER BY p.tanggal DESC";
            
            $res = $conn->query($query);
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new purchase
    case 'POST':
        $id_supplier = $_POST['id_supplier'] ?? null;
        $items = $_POST['items'] ?? null; // JSON array of items

        if (!$id_supplier || !$items) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_supplier, items (array)'
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

            // Calculate total
            foreach ($items as $item) {
                $qty = $item['qty'];
                $harga = $item['harga'];
                $total += $qty * $harga;
            }

            // Insert pembelian header
            $stmt = $conn->prepare("INSERT INTO pembelian (id_supplier, tanggal, total) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $id_supplier, $tanggal, $total);
            $stmt->execute();
            $id_pembelian = $conn->insert_id;

            // Insert pembelian details and update stock
            foreach ($items as $item) {
                $id_barang = $item['id_barang'];
                $qty = $item['qty'];
                $harga = $item['harga'];
                $subtotal = $qty * $harga;

                // Insert detail
                $detailStmt = $conn->prepare("INSERT INTO pembelian_detail (id_pembelian, id_barang, qty, harga, subtotal) VALUES (?, ?, ?, ?, ?)");
                $detailStmt->bind_param("iiidd", $id_pembelian, $id_barang, $qty, $harga, $subtotal);
                $detailStmt->execute();

                // Update stock
                $updateStmt = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
                $updateStmt->bind_param("ii", $qty, $id_barang);
                $updateStmt->execute();

                // Log stock movement
                $logStmt = $conn->prepare("INSERT INTO stok_log (id_barang, tipe, qty, keterangan) VALUES (?, 'masuk', ?, ?)");
                $keterangan = "Pembelian ID $id_pembelian";
                $logStmt->bind_param("iis", $id_barang, $qty, $keterangan);
                $logStmt->execute();
            }

            $conn->commit();

            jsonResponse(201, [
                'status' => true,
                'message' => 'Pembelian berhasil dibuat',
                'id' => $id_pembelian,
                'total' => $total
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal membuat pembelian: ' . $e->getMessage()
            ]);
        }
        break;

    // DELETE - Delete purchase (restore stock)
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];

        $conn->begin_transaction();

        try {
            // Restore stock
            $detailStmt = $conn->prepare("SELECT id_barang, qty FROM pembelian_detail WHERE id_pembelian = ?");
            $detailStmt->bind_param("i", $id);
            $detailStmt->execute();
            $details = $detailStmt->get_result();

            while ($detail = $details->fetch_assoc()) {
                $updateStock = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
                $updateStock->bind_param("ii", $detail['qty'], $detail['id_barang']);
                $updateStock->execute();

                // Log stock movement
                $logStmt = $conn->prepare("INSERT INTO stok_log (id_barang, tipe, qty, keterangan) VALUES (?, 'keluar', ?, ?)");
                $keterangan = "Pembatalan pembelian ID $id";
                $logStmt->bind_param("iis", $detail['id_barang'], $detail['qty'], $keterangan);
                $logStmt->execute();
            }

            // Delete details
            $deleteDetails = $conn->prepare("DELETE FROM pembelian_detail WHERE id_pembelian = ?");
            $deleteDetails->bind_param("i", $id);
            $deleteDetails->execute();

            // Delete purchase
            $deletePurchase = $conn->prepare("DELETE FROM pembelian WHERE id = ?");
            $deletePurchase->bind_param("i", $id);
            $deletePurchase->execute();

            if ($deletePurchase->affected_rows > 0) {
                $conn->commit();
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Pembelian ID $id berhasil dihapus dan stok dikembalikan"
                ]);
            } else {
                $conn->rollback();
                jsonResponse(404, [
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }

        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal menghapus pembelian: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}
