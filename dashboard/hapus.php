<?php
include "../config/db.php";

if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $sql = "DELETE FROM menstruasi WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire('Dihapus!', 'Data berhasil dihapus.', 'success')
                .then(() => { window.location.href = 'index.php'; });
              </script>";
    }
}
?>
