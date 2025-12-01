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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

switch ($method) {

    // GET - Read wishlist for a member
    case 'GET':
        if (!isset($_GET['id_member'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id_member wajib']);
        }

        $id_member = (int) $_GET['id_member'];
        
        // Get wishlist with product details
        $query = "SELECT w.id, w.id_member, w.id_barang, 
                  b.nama_barang, b.harga, b.stok, b.deskripsi
                  FROM wishlist w
                  JOIN barang b ON w.id_barang = b.id
                  WHERE w.id_member = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_member);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        
        jsonResponse(200, [
            'status' => true,
            'data' => $rows,
            'total_items' => count($rows)
        ]);
        break;

    // POST - Add item to wishlist
    case 'POST':
        $id_member = $_POST['id_member'] ?? null;
        $id_barang = $_POST['id_barang'] ?? null;

        if (!$id_member || !$id_barang) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_member, id_barang'
            ]);
        }

        // Check if product exists
        $checkProduct = $conn->prepare("SELECT id FROM barang WHERE id = ?");
        $checkProduct->bind_param("i", $id_barang);
        $checkProduct->execute();
        if ($checkProduct->get_result()->num_rows == 0) {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Barang tidak ditemukan'
            ]);
        }

        // Check if item already in wishlist
        $checkWishlist = $conn->prepare("SELECT id FROM wishlist WHERE id_member = ? AND id_barang = ?");
        $checkWishlist->bind_param("ii", $id_member, $id_barang);
        $checkWishlist->execute();
        if ($checkWishlist->get_result()->num_rows > 0) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Barang sudah ada di wishlist'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO wishlist (id_member, id_barang) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_member, $id_barang);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Item berhasil ditambahkan ke wishlist',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // DELETE - Remove item from wishlist
    case 'DELETE':
        if (isset($_GET['id'])) {
            // Delete by wishlist id
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Item wishlist ID $id berhasil dihapus"
                ]);
            } else {
                jsonResponse(404, [
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        } elseif (isset($_GET['id_member']) && isset($_GET['id_barang'])) {
            // Delete by member and product
            $id_member = (int) $_GET['id_member'];
            $id_barang = (int) $_GET['id_barang'];
            
            $stmt = $conn->prepare("DELETE FROM wishlist WHERE id_member = ? AND id_barang = ?");
            $stmt->bind_param("ii", $id_member, $id_barang);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Item berhasil dihapus dari wishlist"
                ]);
            } else {
                jsonResponse(404, [
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        } else {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id atau (id_member dan id_barang) wajib']);
        }
        break;

    default:
        jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}
