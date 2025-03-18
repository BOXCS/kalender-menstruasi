<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET["tanggal_mulai"]) && isset($_GET["lama_hari"])) {
    $user_id = $_SESSION["user_id"];
    $tanggal_mulai = $_GET["tanggal_mulai"];
    $lama_hari = intval($_GET["lama_hari"]);

    $sql = "INSERT INTO menstruasi (user_id, tanggal_mulai, lama_hari) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $user_id, $tanggal_mulai, $lama_hari);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire('Sukses!', 'Data berhasil ditambahkan.', 'success')
                .then(() => { window.location.href = 'index.php'; });
              </script>";
    }
}
?>
