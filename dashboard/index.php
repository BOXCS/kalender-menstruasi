<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}
include "../config/db.php";

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM menstruasi WHERE user_id = ? ORDER BY tanggal_mulai DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$sql_chart = "SELECT siklus_hari FROM menstruasi WHERE user_id = ? ORDER BY tanggal_mulai ASC LIMIT 5"; // Ubah DESC menjadi ASC
$stmt_chart = $conn->prepare($sql_chart);
$stmt_chart->bind_param("i", $user_id);
$stmt_chart->execute();
$result_chart = $stmt_chart->get_result();

$data_siklus = [];
while ($row = $result_chart->fetch_assoc()) {
    $data_siklus[] = $row["siklus_hari"];
}


// Ambil data periode terakhir dan rata-rata siklus
$sql_siklus = "SELECT tanggal_mulai, siklus_hari FROM menstruasi WHERE user_id = ? ORDER BY tanggal_mulai DESC LIMIT 1";
$stmt_siklus = $conn->prepare($sql_siklus);
$stmt_siklus->bind_param("i", $user_id);
$stmt_siklus->execute();
$result_siklus = $stmt_siklus->get_result();
$hari_menuju_menstruasi = null;

if ($row = $result_siklus->fetch_assoc()) {
    $tanggal_terakhir = $row["tanggal_mulai"];
    $siklus_hari = $row["siklus_hari"];
    $next_period = date("Y-m-d", strtotime($tanggal_terakhir . " + $siklus_hari days"));

    // Hitung selisih hari
    $today = date("Y-m-d");
    $selisih = (strtotime($next_period) - strtotime($today)) / (60 * 60 * 24);
    $hari_menuju_menstruasi = max(0, $selisih);
}

$masa_subur_start = $next_period ? date("Y-m-d", strtotime($next_period . " -14 days")) : null;
$masa_subur_end = $masa_subur_start ? date("Y-m-d", strtotime($masa_subur_start . " +5 days")) : null;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #ffe6f0;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(255, 105, 180, 0.3);
        }

        .btn-primary {
            background-color: #ff80ab;
            border: none;
        }

        .btn-primary:hover {
            background-color: #f50057;
        }

        .badge {
            font-size: 1rem;
            padding: 8px 12px;
            border-radius: 8px;
        }
    </style>
</head>

<body class="container mt-5">
    <h2 class="text-center" style="color: #d81b60;">Data Menstruasi</h2>

    <button class="btn btn-primary mb-3 w-100" onclick="tambahData()">+ Tambah Data</button>

    <table class="table table-bordered text-center">
        <thead>
            <tr class="bg-pink text-white" style="background-color: #ff80ab;">
                <th>Tanggal Mulai</th>
                <th>Lama Menstruasi (hari)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["tanggal_mulai"]; ?></td>
                    <td><?= $row["lama_hari"]; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editData(<?= $row['id']; ?>, '<?= $row['tanggal_mulai']; ?>', <?= $row['lama_hari']; ?>)">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="hapusData(<?= $row['id']; ?>)">Hapus</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php
    // Hitung siklus terakhir
    $sql_siklus = "SELECT tanggal_mulai, siklus_hari FROM menstruasi WHERE user_id = ? ORDER BY tanggal_mulai DESC LIMIT 1";
    $stmt_siklus = $conn->prepare($sql_siklus);
    $stmt_siklus->bind_param("i", $user_id);
    $stmt_siklus->execute();
    $result_siklus = $stmt_siklus->get_result();
    $next_period = null;

    if ($row = $result_siklus->fetch_assoc()) {
        $tanggal_terakhir = $row["tanggal_mulai"];
        $siklus_hari = $row["siklus_hari"];
        $next_period = date("Y-m-d", strtotime($tanggal_terakhir . " + $siklus_hari days"));
    }
    ?>

    <h4>Perkiraan Menstruasi Berikutnya:
        <span class="badge bg-warning"><?= $next_period ? $next_period : "Belum ada data" ?></span>
    </h4>

    <?php if ($next_period && $next_period == date("Y-m-d", strtotime("+2 days"))) : ?>
        <script>
            Swal.fire({
                title: "Peringatan!",
                text: "Menstruasi Anda diperkirakan mulai dalam 2 hari.",
                icon: "info"
            });
        </script>
    <?php endif; ?>

    <h4>Masa Subur:
        <span class="badge bg-success">
            <?= $masa_subur_start && $masa_subur_end ? "$masa_subur_start - $masa_subur_end" : "Belum ada data" ?>
        </span>
    </h4>


    <canvas id="chartSiklus" width="400" height="200"></canvas>
    <script>
        const dataSiklus = <?= json_encode($data_siklus); ?>;

        const ctx = document.getElementById('chartSiklus').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataSiklus.map((_, i) => `Siklus ${i+1}`),
                datasets: [{
                    label: 'Durasi Siklus',
                    data: dataSiklus,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2
                }]
            }
        });
    </script>



    <script>
        function tambahData() {
            Swal.fire({
                title: "Tambah Data Menstruasi",
                html: `
            <input type="date" id="tanggal_mulai" class="swal2-input">
            <input type="number" id="lama_hari" class="swal2-input" placeholder="Lama Hari">
        `,
                showCancelButton: true,
                confirmButtonText: "Simpan",
                confirmButtonColor: "#ff80ab",
                preConfirm: () => {
                    let tanggal_mulai = document.getElementById("tanggal_mulai").value;
                    let lama_hari = document.getElementById("lama_hari").value;

                    if (!tanggal_mulai || !lama_hari) {
                        Swal.showValidationMessage("Semua field harus diisi!");
                        return false;
                    }

                    // Kirim data ke PHP dengan fetch
                    return fetch("tambah_menstruasi.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `tanggal_mulai=${tanggal_mulai}&lama_hari=${lama_hari}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                Swal.fire("Berhasil!", "Data menstruasi telah ditambahkan.", "success")
                                    .then(() => {
                                        window.location.reload();
                                    });
                            } else {
                                Swal.fire("Error!", "Gagal menambahkan data.", "error");
                            }
                        })
                        .catch(error => {
                            Swal.fire("Error!", "Terjadi kesalahan koneksi.", "error");
                        });
                }
            });
        }


        function editData(id, tanggal_mulai, lama_hari) {
            Swal.fire({
                title: "Edit Data Menstruasi",
                html: `
                    <input type="date" id="tanggal_mulai" class="swal2-input" value="${tanggal_mulai}">
                    <input type="number" id="lama_hari" class="swal2-input" value="${lama_hari}">
                `,
                showCancelButton: true,
                confirmButtonText: "Update",
                preConfirm: () => {
                    let new_tanggal_mulai = document.getElementById("tanggal_mulai").value;
                    let new_lama_hari = document.getElementById("lama_hari").value;

                    if (!new_tanggal_mulai || !new_lama_hari) {
                        Swal.showValidationMessage("Semua field harus diisi!");
                        return false;
                    }

                    window.location.href = `edit.php?id=${id}&tanggal_mulai=${new_tanggal_mulai}&lama_hari=${new_lama_hari}`;
                }
            });
        }

        function hapusData(id) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `hapus.php?id=${id}`;
                }
            });
        }
    </script>
</body>

<?php if ($hari_menuju_menstruasi !== null) : ?>
    <script>
        Swal.fire({
            title: "Pengingat!",
            text: "Menstruasi Anda diperkirakan mulai dalam <?= $next_period ?> hari.",
            icon: "info"
        });
    </script>
<?php endif; ?>


</html>