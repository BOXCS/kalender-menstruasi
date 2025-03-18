<?php
session_start();
include "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        text: 'Silakan login.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ff4081'
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .card-header {
            background: #ff4081;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .btn-pink {
            background-color: #ff4081;
            border: none;
            color: white;
        }
        .btn-pink:hover {
            background-color: #e91e63;
        }
        .form-control {
            border-radius: 10px;
        }
        label {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="card p-4">
        <div class="card-header">Registrasi</div>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-pink w-100">Daftar</button>
            </form>
        </div>
    </div>
</body>
</html>
