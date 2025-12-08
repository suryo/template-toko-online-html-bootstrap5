<?php
// config/koneksi.php
$host = "localhost";     // host database
$user = "root";          // username MySQL
$pass = "";              // password MySQL
$db   = "toko_online"; // nama database yang sebenarnya

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Return JSON if this is an API request (simple check)
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'status' => false,
            'message' => 'Koneksi database gagal: ' . mysqli_connect_error()
        ]);
        exit;
    } else {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
}
