<?php
ob_start(); // Pastikan ini baris PERTAMA
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan session_start() ada dan di paling atas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logika Session Timeout - PASTIKAN INI DI SINI, BUKAN DI function.php
$timeout = 900; // 15 menit
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$_SESSION['last_activity'] = time(); // Update waktu aktivitas terakhir

require_once "function.php";

$isBeranda = !isset($_GET['transaksi']) && !isset($_GET['pesanan']); // Jika tidak ada 'transaksi' atau 'pesanan', anggap ini beranda

// Tambahkan ini:
$meja_tersedia = ambil_meja_tersedia(); // Untuk dropdown di form checkout
$semua_meja = ambil_data_meja(); // Jika Anda butuh daftar semua meja untuk alasan lain (misal, di halaman admin)

// Pengecekan login untuk Admin dan User biasa
if (!isset($_SESSION["akun-admin"]) && !isset($_SESSION["akun-user"])) {
    header("Location: login.php");
    exit;
}

// Menangani berbagai jenis request untuk menampilkan data
if (isset($_GET["transaksi"])) {
    // --- Logika Pagination Dimulai ---
    $data_per_halaman = 10; // Jumlah data yang ingin ditampilkan per halaman

    // Ambil halaman saat ini dari URL, default ke halaman 1
    $halaman_saat_ini = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    // Pastikan halaman tidak kurang dari 1
    $halaman_saat_ini = max(1, $halaman_saat_ini);

    // Hitung offset
    $offset = ($halaman_saat_ini - 1) * $data_per_halaman;

    // Hitung total baris untuk pagination
    $total_data_query = "SELECT COUNT(*) AS total_baris FROM transaksi";
    $total_data_result = ambil_data($total_data_query);
    $total_data = $total_data_result[0]['total_baris'] ?? 0;

    // Hitung total halaman
    $total_halaman = ceil($total_data / $data_per_halaman);
    // Pastikan total_halaman tidak kurang dari 1 jika tidak ada data
    $total_halaman = max(1, $total_halaman);

    // Jika halaman saat ini melebihi total halaman, set ke total halaman
    if ($halaman_saat_ini > $total_halaman && $total_halaman > 0) {
        $halaman_saat_ini = $total_halaman;
        // Hitung ulang offset jika halaman diatur ulang
        $offset = ($halaman_saat_ini - 1) * $data_per_halaman;
    }


    // Menampilkan data transaksi dengan LIMIT dan OFFSET
    $menu = ambil_data("SELECT kode_pesanan, nama_pelanggan, tanggal_transaksi, total_harga, status_transaksi, jenis_pesanan, nomor_meja, nomor_antrian 
                        FROM transaksi 
                        ORDER BY tanggal_transaksi DESC 
                        LIMIT :limit OFFSET :offset", 
                        [
                            ':limit' => $data_per_halaman, 
                            ':offset' => $offset
                        ]);

    // --- Logika Pagination Berakhir ---
} else if (isset($_GET["pesanan"])) {
    // --- Logika Pagination untuk Pesanan Dimulai ---
    $data_per_halaman_pesanan = 20; // Jumlah data yang ingin ditampilkan per halaman untuk pesanan
                                   // Anda bisa sesuaikan ini jika perlu

    // Ambil halaman saat ini dari URL, default ke halaman 1
    $halaman_saat_ini_pesanan = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    // Pastikan halaman tidak kurang dari 1
    $halaman_saat_ini_pesanan = max(1, $halaman_saat_ini_pesanan);

    // Hitung offset
    $offset_pesanan = ($halaman_saat_ini_pesanan - 1) * $data_per_halaman_pesanan;

    // Hitung total baris untuk pagination (hanya dari tabel pesanan)
    $total_data_query_pesanan = "SELECT COUNT(*) AS total_baris FROM pesanan";
    $total_data_result_pesanan = ambil_data($total_data_query_pesanan);
    $total_data_pesanan = $total_data_result_pesanan[0]['total_baris'] ?? 0;

    // Hitung total halaman
    $total_halaman_pesanan = ceil($total_data_pesanan / $data_per_halaman_pesanan);
    // Pastikan total_halaman tidak kurang dari 1 jika tidak ada data
    $total_halaman_pesanan = max(1, $total_halaman_pesanan);

    // Jika halaman saat ini melebihi total halaman, set ke total halaman
    if ($halaman_saat_ini_pesanan > $total_halaman_pesanan && $total_halaman_pesanan > 0) {
        $halaman_saat_ini_pesanan = $total_halaman_pesanan;
        // Hitung ulang offset jika halaman diatur ulang
        $offset_pesanan = ($halaman_saat_ini_pesanan - 1) * $data_per_halaman_pesanan;
    }

    // Menampilkan data pesanan dengan LIMIT dan OFFSET
    $menu = ambil_data("SELECT 
                            p.kode_pesanan, 
                            tk.nama_pelanggan, 
                            m.nama AS nama_menu,   
                            p.qty, 
                            m.harga AS harga_satuan, 
                            (p.qty * m.harga) AS sub_total_item, 
                            tk.nomor_meja, 
                            tk.nomor_antrian, 
                            tk.jenis_pesanan
                        FROM pesanan AS p
                        JOIN transaksi AS tk ON (tk.kode_pesanan = p.kode_pesanan)
                        JOIN menu AS m ON (m.kode_menu = p.kode_menu)
                        ORDER BY p.id_pesanan DESC 
                        LIMIT :limit_pesanan OFFSET :offset_pesanan", 
                        [
                            ':limit_pesanan' => $data_per_halaman_pesanan, 
                            ':offset_pesanan' => $offset_pesanan
                        ]);

    // --- Logika Pagination untuk Pesanan Berakhir ---

    $active_tab = 'pesanan';
} else {
    // Menampilkan menu masakan
    if (!isset($_GET["search"])) {
         $menu = ambil_data("SELECT * FROM menu ORDER BY kode_menu DESC");
    } else {
        $key_search = $_GET["key-search"];
        // Prepared statement untuk menghindari SQL Injection
        $query = "SELECT * FROM menu WHERE nama LIKE :key_search OR harga LIKE :key_search OR kategori LIKE :key_search OR `status` LIKE :key_search ORDER BY kode_menu DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':key_search', '%' . $key_search . '%', PDO::PARAM_STR);
        $stmt->execute();
        $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="./src/css/bootstrap-icons-1.8.3/bootstrap-icons.css">
    <title>Beranda</title>
</head>

<body class="bg-light">
    <div class="container-fluid mt-5">
        <div class="text-center mb-4">
            <img src="iklan-kopi.jpg" alt="Selamat Datang di Kopi Ogut" class="img-fluid w-100" style="max-height: 300px; object-fit: cover;">
        </div>
    </div>
    <div class="container-fluid position-fixed top-0 bg-dark p-2 d-flex justify-content-between" style="z-index: 2;">
        <div class="text-white h3 d-flex">
            <span id="menu-list" role="button"><i class="bi bi-list"></i></span>
            <img src="ogut.png" width="25px">
            <span class="mx-3"><a href="index.php" class="text-white text-decoration-none d-flex align-items-center">Ogut Coffee</span>
            
        </div>
        <a class="btn btn-danger fw-bold" href="logout.php" onclick="return confirm('Ingin Logout?')">Logout</a>
    </div>

    <?php if ($isBeranda) { // Ini adalah pembuka untuk bagian Beranda saja ?>
        <div class="text-center mb-4">
            <h2>Selamat Datang di Kopi Ogut</h2>
            <p>Temukan berbagai rasa kopi spesial hanya di Kopi Ogut. Nikmati pengalaman ngopi yang tak terlupakan!</p>
        </div>

        <div class="container-fluid mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="index.php" method="GET" class="d-flex flex-grow-1 me-2">
                    <input class="form-control me-2" type="search" autocomplete="off" name="key-search" placeholder="Cari..">
                    <button class="btn btn-success" name="search">Search</button>
                </form>

                <?php if (isset($_SESSION["akun-admin"])) { ?>
                    <a class="btn btn-success fw-bold" href="tambah.php">
                        + Tambah Menu
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php } // Ini adalah penutup untuk bagian Beranda saja ?>

    <div id="dropdown-menu" class="position-fixed h-100 bg-dark text-white p-3" style="display: none; z-index: 1; top: 50px; left: 0; width: 250px;">
        <ul class="list-unstyled">
            <li class="mb-3"><a class="text-decoration-none h5 text-light d-block" href="index.php">MENU</a></li>
            <?php if (isset($_SESSION["akun-admin"])) { ?>
                <li class="mb-3"><a class="text-decoration-none h5 text-light d-block" href="index.php?pesanan">PESANAN</a></li>
                <li class="mb-3"><a class="text-decoration-none h5 text-light d-block" href="index.php?transaksi">TRANSAKSI</a></li>
            <?php } ?>
        </ul>
    </div>

    <div class="container" style="z-index: -1; margin-top: 60px;">
        <?php
        if (isset($_GET["pesanan"])) {
            include "halaman/pesanan.php";
        } else if (isset($_GET["transaksi"])) {
            include "halaman/transaksi.php";
        } else {
            // Menampilkan data menu masakan
            echo "<div class='row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4'>"; // Memperbaiki grid layout
            foreach ($menu as $m) {
                // Tutup blok PHP untuk menulis HTML murni
                ?>
                <div class='col'>
                    <div class='card'>
                        <img src='src/img/<?php echo htmlspecialchars($m['gambar']); ?>' class='card-img-top menu-img' alt='<?php echo htmlspecialchars($m['nama']); ?>'>
                        <div class='card-body'>
                            <h5 class='card-title'><?php echo htmlspecialchars($m['nama']); ?></h5>
                            <p class='card-text'>Rp <?php echo number_format($m['harga'], 0, ',', '.'); ?></p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <form class="add-to-cart-form me-2" method="POST" style="flex-grow: 1;">
                                    <input type='hidden' name='product_id' value='<?php echo htmlspecialchars($m['kode_menu']); ?>'>
                                    <label for='quantity-<?php echo htmlspecialchars($m['kode_menu']); ?>' class="form-label visually-hidden">Jumlah:</label>
                                    <input type='number' name='quantity' value='1' min='1' class='form-control form-control-sm mb-2' id='quantity-<?php echo htmlspecialchars($m['kode_menu']); ?>'>
                                    <button type='submit' name='add_to_cart' class='btn btn-primary btn-sm w-100'>Tambah ke Keranjang</button>
                                </form>

                                <?php
                                if (isset($_SESSION["akun-admin"])) {
                                ?>
                                    <div class='d-flex flex-column'>
                                        <a class='btn btn-warning btn-sm mb-2' title='Edit' href='edit.php?id_menu=<?php echo htmlspecialchars($m['id_menu']); ?>'>
                                            <i class='bi bi-pencil-fill'></i> Edit
                                        </a>
                                        <a class='btn btn-danger btn-sm' title='Hapus' href='hapus.php?id_menu=<?php echo htmlspecialchars($m['id_menu']); ?>' onclick='return confirm("Ingin Menghapus Menu?")'>
                                            <i class='bi bi-trash3-fill'></i> Hapus
                                        </a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo "</div>";
        }
        ?>
    </div>

    <button type="button" class="btn btn-success position-fixed bottom-0 end-0 m-4" data-bs-toggle="modal" data-bs-target="#cartModal" id="cartButton">
        Keranjang <span class="badge bg-light text-dark" id="cartCount"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
    </button>

    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Keranjang Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cartItems">
                        </div>

                    <form action="index.php" method="POST" id="checkoutForm"> <div class="form-group mb-3">
                            <label for="pelanggan">Nama Pelanggan</label>
                            <input type="text" name="pelanggan" id="pelanggan" class="form-control" required placeholder="Masukkan Nama Pelanggan">
                        </div>

                        <div class="form-group mb-3">
                            <label>Jenis Pesanan</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_pesanan" id="dine_in" value="Dine-in" checked>
                                <label class="form-check-label" for="dine_in">Dine-in</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_pesanan" id="take_away" value="Takeaway">
                                <label class="form-check-label" for="take_away">Takeaway</label>
                            </div>
                        </div>

                        <div id="dine_in_options" class="form-group mb-3">
                            <label for="nomor_meja">Nomor Meja</label>
                            <select class="form-control" id="nomor_meja" name="nomor_meja" required>
                                <option value="">Pilih Meja</option>
                                <?php foreach ($meja_tersedia as $meja) { ?>
                                    <option value="<?= htmlspecialchars($meja['nomor_meja']) ?>"><?= htmlspecialchars($meja['nomor_meja']) ?> (Kapasitas: <?= htmlspecialchars($meja['kapasitas']) ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" name="checkout">Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="src/js/beranda.js"></script>
</body>
<style>
    .menu-img {
        width: 100%;
        /* Gambar akan mengisi lebar card */
        height: 150px;
        /* Tentukan tinggi gambar untuk menyamakan ukuran */
        object-fit: cover;
        /* Memastikan gambar tetap terpotong dan menutupi area */
        object-position: center;
        /* Memusatkan gambar jika terpotong */
    }
</style>
</html>