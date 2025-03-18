<?php
$host = "localhost";
$user = "root";  // Sesuaikan dengan username MySQL
$pass = "";      // Sesuaikan dengan password MySQL
$dbname = "kalender_menstruasi";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
