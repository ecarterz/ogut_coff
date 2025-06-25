<?php

session_start();


require_once "function.php";

// Mengecek apakah yang mengakses adalah admin
if (!isset($_SESSION["akun-admin"])) {
    if (isset($_SESSION["akun-user"])) {
        echo "<script>
            alert('Hapus data hanya berlaku untuk admin!');
            location.href = 'index.php';
        </script>";
        exit;
    } else {
        header("Location: login.php");
        exit;
    }
}

// Proses Penghapusan Data Menu atau Pesanan
$hapus = 0; // Default

// Cek jika id_menu ada, artinya ingin menghapus menu
if (isset($_GET["id_menu"])) {
    $hapus = hapus_data_menu($_GET["id_menu"]);
} 
// Cek jika kode_pesanan ada, artinya ingin menghapus pesanan
else if (isset($_GET["kode_pesanan"])) {
    $hapus = hapus_data_pesanan($_GET["kode_pesanan"]);
}

// Cek apakah penghapusan berhasil
if ($hapus > 0) {
    echo "<script>
        alert('Data berhasil dihapus!');
        location.href = 'index.php'; // Redirect ke halaman utama setelah berhasil
    </script>";
} else {
    echo "<script>
        alert('Data gagal dihapus!');
        location.href = 'index.php'; // Redirect ke halaman utama setelah gagal
    </script>";
}

?>
