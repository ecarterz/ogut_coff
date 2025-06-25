<?php
session_start();
require_once "function.php";

// Cek apakah token valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Cek token dalam database
    $stmt = $pdo->prepare("SELECT * FROM reset_password WHERE token = ? AND expiry > NOW()");
    $stmt->execute([$token]);
    $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset_data) {
        // Token valid, tampilkan formulir untuk mengatur ulang password
        if (isset($_POST["submit"])) {
            $password = md5(htmlspecialchars($_POST["password"])); // Enkripsi password baru
            $email = $reset_data['email'];

            // Update password di tabel user
            $stmt = $pdo->prepare("UPDATE user SET password = ? WHERE email = ?");
            $stmt->execute([$password, $email]);

            // Hapus token setelah password berhasil direset
            $stmt = $pdo->prepare("DELETE FROM reset_password WHERE token = ?");
            $stmt->execute([$token]);

            echo "<script>alert('Password berhasil direset!'); location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Token tidak valid atau sudah kadaluarsa!'); location.href = 'login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form action="reset-password.php?token=<?php echo $_GET['token']; ?>" method="POST">
            <input type="password" name="password" placeholder="Password Baru" required><br>
            <button type="submit" name="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
