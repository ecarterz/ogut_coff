<?php
session_start();
require_once "function.php";

// Proses pengiriman link reset password
if (isset($_POST["reset-password"])) {
    $email = htmlspecialchars($_POST["email"]);

    // Cek apakah email ada dalam database
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Buat token unik untuk reset password
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token kadaluarsa dalam 1 jam

        // Simpan token ke database
        $stmt = $pdo->prepare("INSERT INTO reset_password (email, token, expiry) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expiry]);

        // Kirim email dengan link reset password
        $reset_link = "https://www.ogut-coffee.my.id/reset-password.php?token=$token";
        mail($email, "Reset Password", "Klik link berikut untuk reset password: $reset_link");

        echo "<script>alert('Link reset password telah dikirim ke email Anda!'); location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/css/bootstrap-5.2.0/css/bootstrap.min.css">
    <title>Lupa Password</title>
    <style>
        body {
            background-image: url('bg.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #reset-form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        #judul-form {
            font-size: 2rem;
            color: #333;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            font-weight: bold;
        }

        .alert {
            margin-bottom: 15px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-dismissible .btn-close {
            color: #721c24;
        }

        .btn-primary {
            background-color: rgb(123, 94, 54);
            border: none;
        }

        .btn-outline-primary {
            border: 1px solid rgb(255, 255, 255);
            color: rgb(123, 94, 54);
        }

        .btn-primary:hover,
        .btn-outline-primary:hover {
            background-color: rgb(255, 191, 100);
            color: white;
        }
    </style>
</head>

<body>
    <div id="reset-form-container">
        <h2 id="judul-form" class="text-center">Lupa Password</h2>
        <form action="lupa-password.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Masukkan Email Anda</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
            </div>
            <button type="submit" name="reset-password" class="btn btn-primary">Kirim Link Reset Password</button>
        </form>
    </div>
</body>
</html>
