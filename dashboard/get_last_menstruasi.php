<?php
session_start();
include "../config/db.php";

$user_id = $_SESSION["user_id"];

// Ambil data menstruasi terakhir berdasarkan user_id
$sql = "SELECT tanggal_mulai FROM menstruasi WHERE user_id = ? ORDER BY tanggal_mulai DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data ? $data : ["tanggal_mulai" => null]);
?>
