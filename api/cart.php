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

    // GET - Read cart items for a member
    case 'GET':
        if (!isset($_GET['id_member'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id_member wajib']);
        }

        $id_member = (int) $_GET['id_member'];
        
        // Get cart with product details
        $query = "SELECT c.id, c.id_member, c.id_barang, c.qty, 
                  b.nama_barang, b.harga, b.stok,
                  (c.qty * b.harga) as subtotal
                  FROM cart c
                  JOIN barang b ON c.id_barang = b.id
                  WHERE c.id_member = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_member);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $rows = [];
        $total = 0;
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
            $total += $r['subtotal'];
        }
        
        jsonResponse(200, [
            'status' => true,
            'data' => $rows,
            'total' => $total
        ]);
        break;

    // POST - Add item to cart
    case 'POST':
        $id_member = $_POST['id_member'] ?? null;
        $id_barang = $_POST['id_barang'] ?? null;
        $qty = $_POST['qty'] ?? 1;

        if (!$id_member || !$id_barang) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_member, id_barang'
            ]);
        }

        // Check stock availability
        $checkStock = $conn->prepare("SELECT stok FROM barang WHERE id = ?");
        $checkStock->bind_param("i", $id_barang);
        $checkStock->execute();
        $stockResult = $checkStock->get_result()->fetch_assoc();

        if (!$stockResult) {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Barang tidak ditemukan'
            ]);
        }

        if ($stockResult['stok'] < $qty) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Stok tidak mencukupi. Tersedia: ' . $stockResult['stok']
            ]);
        }

        // Check if item already in cart
        $checkCart = $conn->prepare("SELECT id, qty FROM cart WHERE id_member = ? AND id_barang = ?");
        $checkCart->bind_param("ii", $id_member, $id_barang);
        $checkCart->execute();
        $cartResult = $checkCart->get_result()->fetch_assoc();

        if ($cartResult) {
            // Update quantity
            $newQty = $cartResult['qty'] + $qty;
            
            if ($stockResult['stok'] < $newQty) {
                jsonResponse(400, [
                    'status' => false,
                    'message' => 'Stok tidak mencukupi. Tersedia: ' . $stockResult['stok']
                ]);
            }

            $stmt = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQty, $cartResult['id']);
            
            if ($stmt->execute()) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => 'Jumlah item di cart berhasil diupdate'
                ]);
            }
        } else {
            // Insert new item
            $stmt = $conn->prepare("INSERT INTO cart (id_member, id_barang, qty) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $id_member, $id_barang, $qty);

            if ($stmt->execute()) {
                jsonResponse(201, [
                    'status' => true,
                    'message' => 'Item berhasil ditambahkan ke cart',
                    'id' => $conn->insert_id
                ]);
            } else {
                jsonResponse(500, [
                    'status' => false,
                    'message' => 'Gagal insert: ' . $conn->error
                ]);
            }
        }
        break;

    // PUT - Update cart item quantity
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $qty = $_POST['qty'] ?? null;

        if (!$qty || $qty < 1) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Quantity harus lebih dari 0'
            ]);
        }

        // Check stock
        $checkQuery = "SELECT c.id_barang, b.stok FROM cart c 
                       JOIN barang b ON c.id_barang = b.id 
                       WHERE c.id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();

        if (!$result) {
            jsonResponse(404, ['status' => false, 'message' => 'Item cart tidak ditemukan']);
        }

        if ($result['stok'] < $qty) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Stok tidak mencukupi. Tersedia: ' . $result['stok']
            ]);
        }

        $stmt = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ?");
        $stmt->bind_param("ii", $qty, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Cart item ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Remove item from cart
    case 'DELETE':
        if (isset($_GET['id'])) {
            // Delete single item
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Item cart ID $id berhasil dihapus"
                ]);
            } else {
                jsonResponse(404, [
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        } elseif (isset($_GET['id_member'])) {
            // Clear all cart for member
            $id_member = (int) $_GET['id_member'];
            $stmt = $conn->prepare("DELETE FROM cart WHERE id_member = ?");
            $stmt->bind_param("i", $id_member);

            if ($stmt->execute()) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Cart member ID $id_member berhasil dikosongkan"
                ]);
            } else {
                jsonResponse(500, [
                    'status' => false,
                    'message' => 'Gagal menghapus cart'
                ]);
            }
        } else {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id atau id_member wajib']);
        }
        break;

    default:
        jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}
