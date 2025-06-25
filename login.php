<?php
session_start();
require_once "function.php";  // Memastikan fungsi login dan register terhubung


if (isset($_POST["login"])) {
    $login = login_akun();
} else if (isset($_POST["register"])) {
    $register = register_akun();
    echo $register > 0
        ? "<script>
            alert('Berhasil Registrasi!');
            location.href = 'login.php';
        </script>"
        : "<script>
            alert('Gagal Registrasi!');
            location.href = 'login.php';
        </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/css/bootstrap-5.2.0/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <title>Login</title>
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

        #login-form-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        #judul-form {
            font-family: "Oswald", sans-serif;
  font-optical-sizing: auto;
  font-weight: <weight>;
  font-style: normal;
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

    <div class="container" id="login-form-container">
        <div style="text-align: center;">
            <img src="ogut.png" alt="Logo" width="30px" />
        </div>
        <div id="judul-form" class="text-center h1 mt-3">OGUT COFFEE</div><br>

        <!-- Tab Login & Register -->
        <div class="d-flex justify-content-between">
            <button id="tab-login" class="btn btn-primary fw-bold" style="width: 190px;">LOGIN</button>
            <button id="tab-register" class="btn btn-outline-primary fw-bold" style="width: 190px;">REGISTER</button>
        </div><br>

        <!-- Jika Username & Password Login Salah -->
        <?php if (isset($_POST["login"])) {
            if (!$login) { ?>
                <div class="alert alert-danger alert-dismissible">
                    * username/password salah
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
        <?php }
        } ?>

        <!-- Form Login -->
        <form id="form-login" action="login.php" method="POST">
            <input class="form-control mx-auto d-block" type="text" autocomplete="off" name="username" placeholder="Username" required><br>
            <input class="form-control mx-auto d-block" type="password" autocomplete="off" name="password" placeholder="Password" required><br>
            <button class="btn btn-primary" name="login">Login</button>
        </form>

        <!-- Form Register -->
<form id="form-register" action="login.php" method="POST" style="display: none;">
    <input class="form-control mx-auto d-block" type="text" autocomplete="off" name="username" placeholder="Username" required><br>
    <input class="form-control mx-auto d-block" type="password" autocomplete="off" name="password" placeholder="Password" required><br>
    <input class="form-control mx-auto d-block" type="password" autocomplete="off" name="konfirmasi-password" placeholder="Konfirmasi Password" required><br>
    <!-- Tambahkan input email -->
    <input class="form-control mx-auto d-block" type="email" autocomplete="off" name="email" placeholder="Email" required><br>
    <!-- Tambahkan input nohp -->
    <input class="form-control mx-auto d-block" type="text" autocomplete="off" name="nohp" placeholder="Nomor Handphone" required><br>
    <button class="btn btn-primary" name="register">Register</button>
</form>

<!-- Link untuk Lupa Password -->
<div class="text-center mt-3">
    <a href="lupa-password.php">Lupa Password?</a>
</div>

    <script src="./src/css/bootstrap-5.2.0/js/bootstrap.bundle.min.js"></script>
    <script src="./src/js/login.js"></script>

    <script>
        // Tab Switching (Login / Register)
        document.getElementById('tab-login').addEventListener('click', function () {
            document.getElementById('form-login').style.display = 'block';
            document.getElementById('form-register').style.display = 'none';
            document.getElementById('tab-login').classList.add('btn-primary');
            document.getElementById('tab-login').classList.remove('btn-outline-primary');
            document.getElementById('tab-register').classList.add('btn-outline-primary');
            document.getElementById('tab-register').classList.remove('btn-primary');
        });

        document.getElementById('tab-register').addEventListener('click', function () {
            document.getElementById('form-login').style.display = 'none';
            document.getElementById('form-register').style.display = 'block';
            document.getElementById('tab-login').classList.add('btn-outline-primary');
            document.getElementById('tab-login').classList.remove('btn-primary');
            document.getElementById('tab-register').classList.add('btn-primary');
            document.getElementById('tab-register').classList.remove('btn-outline-primary');
        });
    </script>

</body>
</html>
