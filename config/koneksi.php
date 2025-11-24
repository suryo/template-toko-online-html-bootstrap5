<?php
// config/koneksi.php
$host = "localhost";     // host database
$user = "root";          // username MySQL
$pass = "";              // password MySQL
$db   = "db_toko_online"; // ganti dengan nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
