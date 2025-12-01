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

    // GET - Read notifications for a member
    case 'GET':
        if (!isset($_GET['id_member'])) {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id_member wajib']);
        }

        $id_member = (int) $_GET['id_member'];
        
        // Filter by read status if provided
        $query = "SELECT * FROM notifikasi WHERE id_member = ?";
        
        if (isset($_GET['sudah_dibaca'])) {
            $sudah_dibaca = (int) $_GET['sudah_dibaca'];
            $query .= " AND sudah_dibaca = $sudah_dibaca";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_member);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $rows = [];
        $unread_count = 0;
        
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
            if ($r['sudah_dibaca'] == 0) {
                $unread_count++;
            }
        }
        
        jsonResponse(200, [
            'status' => true,
            'data' => $rows,
            'unread_count' => $unread_count
        ]);
        break;

    // POST - Create new notification
    case 'POST':
        $id_member = $_POST['id_member'] ?? null;
        $isi = $_POST['isi'] ?? null;

        if (!$id_member || !$isi) {
            jsonResponse(400, [
                'status' => false,
                'message' => 'Field wajib: id_member, isi'
            ]);
        }

        $stmt = $conn->prepare("INSERT INTO notifikasi (id_member, isi, sudah_dibaca) VALUES (?, ?, 0)");
        $stmt->bind_param("is", $id_member, $isi);

        if ($stmt->execute()) {
            jsonResponse(201, [
                'status' => true,
                'message' => 'Notifikasi berhasil ditambahkan',
                'id' => $conn->insert_id
            ]);
        } else {
            jsonResponse(500, [
                'status' => false,
                'message' => 'Gagal insert: ' . $conn->error
            ]);
        }
        break;

    // PUT - Mark notification as read
    case 'PUT':
        if (isset($_GET['id'])) {
            // Mark single notification as read
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("UPDATE notifikasi SET sudah_dibaca = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Notifikasi ID $id berhasil ditandai sudah dibaca"
                ]);
            } else {
                jsonResponse(404, [
                    'status' => false,
                    'message' => 'Data tidak ditemukan atau sudah dibaca'
                ]);
            }
        } elseif (isset($_GET['id_member'])) {
            // Mark all notifications as read for member
            $id_member = (int) $_GET['id_member'];
            $stmt = $conn->prepare("UPDATE notifikasi SET sudah_dibaca = 1 WHERE id_member = ? AND sudah_dibaca = 0");
            $stmt->bind_param("i", $id_member);

            if ($stmt->execute()) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Semua notifikasi member ID $id_member berhasil ditandai sudah dibaca",
                    'affected_rows' => $stmt->affected_rows
                ]);
            } else {
                jsonResponse(500, [
                    'status' => false,
                    'message' => 'Gagal update notifikasi'
                ]);
            }
        } else {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id atau id_member wajib']);
        }
        break;

    // DELETE - Delete notification
    case 'DELETE':
        if (isset($_GET['id'])) {
            // Delete single notification
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM notifikasi WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Notifikasi ID $id berhasil dihapus"
                ]);
            } else {
                jsonResponse(404, [
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        } elseif (isset($_GET['id_member'])) {
            // Delete all read notifications for member
            $id_member = (int) $_GET['id_member'];
            $stmt = $conn->prepare("DELETE FROM notifikasi WHERE id_member = ? AND sudah_dibaca = 1");
            $stmt->bind_param("i", $id_member);

            if ($stmt->execute()) {
                jsonResponse(200, [
                    'status' => true,
                    'message' => "Notifikasi yang sudah dibaca berhasil dihapus",
                    'affected_rows' => $stmt->affected_rows
                ]);
            } else {
                jsonResponse(500, [
                    'status' => false,
                    'message' => 'Gagal menghapus notifikasi'
                ]);
            }
        } else {
            jsonResponse(400, ['status' => false, 'message' => 'Parameter id atau id_member wajib']);
        }
        break;

    default:
        jsonResponse(405, ['status' => false, 'message' => 'Method tidak diizinkan']);
}
