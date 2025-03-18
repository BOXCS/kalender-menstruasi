<?php
include "../config/db.php";

if (isset($_GET["id"]) && isset($_GET["tanggal_mulai"]) && isset($_GET["lama_hari"])) {
    $id = intval($_GET["id"]);
    $tanggal_mulai = $_GET["tanggal_mulai"];
    $lama_hari = intval($_GET["lama_hari"]);

    $sql = "UPDATE menstruasi SET tanggal_mulai = ?, lama_hari = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $tanggal_mulai, $lama_hari, $id);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire('Sukses!', 'Data berhasil diperbarui.', 'success')
                .then(() => { window.location.href = 'index.php'; });
              </script>";
    }
}
?>
