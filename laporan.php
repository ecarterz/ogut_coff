<?php
require_once "function.php"; // Memastikan fungsi login dan register terhubung

// Mengambil periode dan tanggal yang dipilih dari URL
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'harian';
$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Menentukan tanggal awal dan akhir untuk laporan harian atau bulanan
if ($periode == 'harian') {
    $start_date = $selected_date;
    $end_date = $selected_date;
} elseif ($periode == 'bulanan') {
    $start_date = date('Y-m-01', strtotime($selected_date)); // Hari pertama bulan
    $end_date = date('Y-m-t', strtotime($selected_date)); // Hari terakhir bulan
}

// Menampilkan periode yang dipilih
echo "<h1 class='text-center text-primary'>Laporan Transaksi - $periode</h1>";
echo "<p class='text-center'>Periode: $start_date - $end_date</p>";

// Query untuk mengambil data transaksi berdasarkan rentang tanggal
$sql = "SELECT * FROM pesanan
        JOIN transaksi ON pesanan.kode_pesanan = transaksi.kode_pesanan
        JOIN menu ON menu.kode_menu = pesanan.kode_menu
        WHERE DATE(transaksi.tanggal_transaksi) BETWEEN '$start_date' AND '$end_date'
        ORDER BY transaksi.tanggal_transaksi DESC"; // Urutkan berdasarkan waktu transaksi
$menu = ambil_data($sql);

// Periksa apakah ada data yang ditemukan
if (empty($menu)) {
    echo "<p class='text-center'>Data tidak ditemukan untuk periode $periode.</p>";
} else {
    // Menampilkan laporan dalam bentuk tabel menggunakan Bootstrap
    echo "<div class='container mt-4'>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead><tr><th>No</th><th>Kode Pesanan</th><th>Nama Pelanggan</th><th>Waktu</th><th>Total Harga</th></tr></thead>";
    echo "<tbody>";

    $i = 1;
    foreach ($menu as $m) {
        // Query untuk menghitung total harga per pesanan
        $total_pembayaran = ambil_data("SELECT DISTINCT * FROM pesanan
                                        JOIN transaksi ON pesanan.kode_pesanan = transaksi.kode_pesanan
                                        JOIN menu ON menu.kode_menu = pesanan.kode_menu
                                        WHERE transaksi.kode_pesanan = '{$m["kode_pesanan"]}'");

        $total = 0;
        // Menghitung total harga untuk setiap item dalam pesanan
        foreach ($total_pembayaran as $tp) {
            $total += $tp["qty"] * $tp["harga"];
        }

        // Menampilkan setiap baris transaksi
        echo "<tr><td>$i</td><td>{$m['kode_pesanan']}</td><td>{$m['nama_pelanggan']}</td><td>{$m['tanggal_transaksi']}</td><td>Rp. $total</td></tr>";  
        $i++;
    }

    echo "</tbody></table></div>";
}
?>

<!-- Link untuk Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- CSS Styling -->
<style>
    body {
        font-family: Arial, sans-serif;
    }

    h1 {
        color: #007bff;
        text-align: center;
        margin-top: 20px;
    }

    p {
        text-align: center;
        font-size: 16px;
        margin-bottom: 30px;
    }

    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f8f9fa;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .btn {
        font-size: 16px;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f2f2f2;
    }

    .table th, .table td {
        vertical-align: middle;
    }

</style>
