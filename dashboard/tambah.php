<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $tanggal_mulai = $_POST["tanggal_mulai"];
    $lama_hari = intval($_POST["lama_hari"]);
    $siklus_hari = null; // Default null jika tidak ada data sebelumnya

    // Ambil tanggal_mulai terakhir dari user ini
    $sql_last = "SELECT tanggal_mulai FROM menstruasi WHERE user_id = ? ORDER BY tanggal_mulai DESC LIMIT 1";
    $stmt_last = $conn->prepare($sql_last);
    $stmt_last->bind_param("i", $user_id);
    $stmt_last->execute();
    $result_last = $stmt_last->get_result();
    
    if ($row = $result_last->fetch_assoc()) {
        $last_date = $row["tanggal_mulai"];
        
        // Hitung perbedaan hari (siklus_hari)
        $date1 = new DateTime($last_date);
        $date2 = new DateTime($tanggal_mulai);
        $siklus_hari = $date1->diff($date2)->days; // Hitung selisih hari
    }

    // Simpan data dengan siklus_hari yang dihitung
    $sql = "INSERT INTO menstruasi (user_id, tanggal_mulai, siklus_hari, lama_hari) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $user_id, $tanggal_mulai, $siklus_hari, $lama_hari);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    title: 'Sukses!',
                    text: 'Data berhasil ditambahkan.',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'index.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal menambahkan data: " . $stmt->error . "',
                    icon: 'error'
                });
              </script>";
    }
}
?>
