<?php

session_start();
require_once "function.php";  // Pastikan file function.php dimuat dengan benar

// Verifikasi jika yang mengakses adalah admin
if (!isset($_SESSION["akun-admin"])) {
    if (isset($_SESSION["akun-user"])) {
        echo "<script>
            alert('Edit data hanya berlaku untuk admin!');
            location.href = 'index.php';
        </script>";
        exit;
    } else {
        header("Location: login.php");
        exit;
    }
}

// Mengecek apakah id_menu ada di parameter URL
if (!isset($_GET["id_menu"])) {
    echo "<script>
        alert('ID menu tidak ditemukan!');
        location.href = 'index.php';
    </script>";
    exit;
}

$id_menu = $_GET["id_menu"];  // Mengambil id_menu dari URL

// Ambil data menu berdasarkan id_menu
$menu = ambil_data("SELECT * FROM menu WHERE id_menu = $id_menu")[0];

// Proses edit menu jika tombol edit ditekan
if (isset($_POST["edit"])) {
    // Ambil data dari formulir
    $nama = htmlspecialchars($_POST["nama"]);
    $harga = (int) htmlspecialchars($_POST["harga"]);
    $gambar = $_FILES["gambar"]["name"];
    $kategori = htmlspecialchars($_POST["kategori"]);
    $status = htmlspecialchars($_POST["status"]);

    // Cek apakah ada gambar baru yang diupload
    if ($gambar != "") {
        // Cek format gambar
        $format_gambar = ["jpg", "jpeg", "png", "gif"];
        $cek_gambar = explode(".", $gambar);
        $cek_gambar = strtolower(end($cek_gambar));

        if (!in_array($cek_gambar, $format_gambar)) {
            echo "<script>
                alert('File yang diupload bukan gambar!');
            </script>";
            exit;
        }

        // Upload gambar
        $nama_gambar = uniqid() . ".$cek_gambar"; // Nama gambar unik
        move_uploaded_file($_FILES["gambar"]["tmp_name"], "src/img/$nama_gambar");
    } else {
        $nama_gambar = $_POST["gambar-lama"];  // Jika gambar tidak diubah, gunakan gambar lama
    }

    // Query untuk mengupdate data menu
    $stmt = $pdo->prepare("UPDATE menu SET nama = ?, harga = ?, gambar = ?, kategori = ?, status = ? WHERE id_menu = ?");
    $stmt->execute([$nama, $harga, $nama_gambar, $kategori, $status, $id_menu]);

    // Cek hasil update
    if ($stmt->rowCount() > 0) {
        echo "<script>
            alert('Data berhasil diubah!');
            location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Data tidak ada yang diubah!');
            location.href = 'index.php';
        </script>";
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
    <title>Edit Data</title>
</head>
<body>

<div class="container">
    <h1>Edit Data Menu</h1>
    <a class="btn btn-success fw-bold" href="index.php">Kembali</a>

    <!-- Form Edit Menu -->
    <form action="edit.php?id_menu=<?= $id_menu; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_menu" value="<?= $menu["id_menu"]; ?>">
        <input type="hidden" name="gambar-lama" value="<?= $menu["gambar"]; ?>">
        <input type="hidden" name="kode_menu" value="<?= $menu["kode_menu"]; ?>">

        <div class="table-responsive-md my-3">
            <table class="table">
                <tr>
                    <td><label for="nama">Nama Makanan</label></td>
                    <td>:</td>
                    <td><input type="text" name="nama" id="nama" value="<?= $menu["nama"]; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="harga">Harga</label></td>
                    <td>:</td>
                    <td><input min="0" type="number" name="harga" id="harga" value="<?= $menu["harga"]; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="gambar">Gambar</label></td>
                    <td>:</td>
                    <td>
                        <img src="src/img/<?= $menu["gambar"]; ?>" width="70"><br><br>
                        <input type="file" name="gambar" accept="image/*">
                    </td>
                </tr>
                <tr>
                    <td><label for="kategori">Kategori</label></td>
                    <td>:</td>
                    <td>
                        <select name="kategori" id="kategori">
                            <option value="Makanan" <?= $menu["kategori"] == "Makanan" ? "selected" : ""; ?>>Makanan</option>
                            <option value="Fast Food" <?= $menu["kategori"] == "Fast Food" ? "selected" : ""; ?>>Fast Food</option>
                            <option value="Snack" <?= $menu["kategori"] == "Snack" ? "selected" : ""; ?>>Snack</option>
                            <option value="Dessert" <?= $menu["kategori"] == "Dessert" ? "selected" : ""; ?>>Dessert</option>
                            <option value="Minuman" <?= $menu["kategori"] == "Minuman" ? "selected" : ""; ?>>Minuman</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="status">Status</label></td>
                    <td>:</td>
                    <td>
                        <label for="tersedia"><input type="radio" name="status" id="tersedia" value="tersedia" <?= $menu["status"] == "tersedia" ? "checked" : ""; ?>>Tersedia</label>
                        <label for="tidak-tersedia"><input type="radio" name="status" id="tidak-tersedia" value="tidak tersedia" <?= $menu["status"] == "tidak tersedia" ? "checked" : ""; ?>>Tidak Tersedia</label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><button class="btn btn-primary" name="edit">Edit</button></td>
                </tr>
            </table>
        </div>
    </form>
</div>

<script src="./src/css/bootstrap-5.2.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
