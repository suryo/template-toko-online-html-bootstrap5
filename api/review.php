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

    // GET - Read all reviews or reviews for specific product
    case 'GET':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("SELECT r.*, cm.nama as nama_member, b.nama_barang 
                                    FROM review r 
                                    JOIN client_member cm ON r.id_member = cm.id 
                                    JOIN barang b ON r.id_barang = b.id 
                                    WHERE r.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res) {
                jsonResponse(200, ['status' => true, 'data' => $res]);
            } else {
                jsonResponse(404, ['status' => false, 'message' => 'Review tidak ditemukan']);
            }
        } elseif (isset($_GET['id_barang'])) {
            // Get all reviews for specific product
            $id_barang = (int) $_GET['id_barang'];
            
            $stmt = $conn->prepare("SELECT r.*, cm.nama as nama_member 
                                    FROM review r 
                                    JOIN client_member cm ON r.id_member = cm.id 
                                    WHERE r.id_barang = ? 
                                    ORDER BY r.tanggal_review DESC");
            $stmt->bind_param("i", $id_barang);
            $stmt->execute();
            $res = $stmt->get_result();
            
            $rows = [];
            $totalRating = 0;
            $count = 0;
            
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
                $totalRating += $r['rating'];
                $count++;
            }
            
            $avgRating = $count > 0 ? round($totalRating / $count, 2) : 0;
            
            jsonResponse(200, [
                'status' => true,
                'data' => $rows,
                'average_rating' => $avgRating,
                'total_reviews' => $count
            ]);
        } elseif (isset($_GET['id_member'])) {
            // Get all reviews by specific member
            $id_member = (int) $_GET['id_member'];
            
            $stmt = $conn->prepare("SELECT r.*, b.nama_barang 
                                    FROM review r 
                                    JOIN barang b ON r.id_barang = b.id 
                                    WHERE r.id_member = ? 
                                    ORDER BY r.tanggal_review DESC");
            $stmt->bind_param("i", $id_member);
            $stmt->execute();
            $res = $stmt->get_result();
            
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        } else {
            // Get all reviews
            $res = $conn->query("SELECT r.*, cm.nama as nama_member, b.nama_barang 
                                FROM review r 
                                JOIN client_member cm ON r.id_member = cm.id 
                                JOIN barang b ON r.id_barang = b.id 
                                ORDER BY r.tanggal_review DESC");
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            jsonResponse(200, ['status' => true, 'data' => $rows]);
        }
        break;

    // POST - Create new review
    case 'POST':
        $id_member = $_POST['id_member'] ?? null;
        $id_barang = $_POST['id_barang'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $komentar = $_POST['komentar'] ?? '';
        $tanggal_review = date('Y-m-d');

        if (!$id_member || !$id_barang || !$rating) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_member, id_barang, rating'
            ]);
        }

        if ($rating < 1 || $rating > 5) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Rating harus antara 1-5'
            ]);
        }

        // Check if member already reviewed this product
        $checkStmt = $conn->prepare("SELECT id FROM review WHERE id_member = ? AND id_barang = ?");
        $checkStmt->bind_param("ii", $id_member, $id_barang);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Anda sudah memberikan review untuk produk ini'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO review (id_member, id_barang, rating, komentar, tanggal_review) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $id_member, $id_barang, $rating, $komentar, $tanggal_review);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Review berhasil ditambahkan',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Update review
    case 'PUT':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $rating = $_POST['rating'] ?? null;
        $komentar = $_POST['komentar'] ?? '';

        if (!$rating) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: rating'
            ]);
        }

        if ($rating < 1 || $rating > 5) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Rating harus antara 1-5'
            ]);
        }

        $stmt = $conn->prepare("UPDATE review SET rating = ?, komentar = ? WHERE id = ?");
        $stmt->bind_param("isi", $rating, $komentar, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Review ID $id berhasil diupdate"
            ]);
        } else {
            jsonResponse(404, [
                'status' => false,
                'message' => 'Data tidak ditemukan atau tidak berubah'
            ]);
        }
        break;

    // DELETE - Delete review
    case 'DELETE':
        if (!isset($_GET['id'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id wajib']);
        }

        $id = (int) $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM review WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            jsonResponse(200, [
                'status' => true,
                'message' => "Review ID $id berhasil dihapus"
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
