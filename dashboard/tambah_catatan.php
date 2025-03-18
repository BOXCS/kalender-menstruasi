<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

if (isset($_GET["tanggal"])) {
    $user_id = $_SESSION["user_id"];
    $tanggal = $_GET["tanggal"];
    $gejala = isset($_GET["gejala"]) ? $_GET["gejala"] : null;
    $mood = isset($_GET["mood"]) ? $_GET["mood"] : null;

    $sql = "INSERT INTO catatan_menstruasi (user_id, tanggal, gejala, mood) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $tanggal, $gejala, $mood);

    if ($stmt->execute()) {
        echo "<script>
            alert('Catatan berhasil ditambahkan!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menambahkan catatan!');
            window.location.href = 'index.php';
        </script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
