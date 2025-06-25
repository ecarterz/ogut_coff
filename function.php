<?php
$pdo = new PDO("mysql:host=localhost;dbname=ogutcoff_ogutdb_n", "ogutcoff_ogutdb_n", "admin123");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Menangani error dengan lebih baik


// Fungsi Register
function register_akun()
{
    global $pdo;

    $username = htmlspecialchars($_POST["username"]);
    $password = md5(htmlspecialchars($_POST["password"]));
    $konfirmasi_password = md5(htmlspecialchars($_POST["konfirmasi-password"]));
    $email = htmlspecialchars($_POST["email"]);
    $nohp = htmlspecialchars($_POST["nohp"]);

    $cek_username = $pdo->prepare("SELECT * FROM `user` WHERE username = ?");
    $cek_username->execute([$username]);
    $cek_username = $cek_username->fetch(PDO::FETCH_ASSOC);

    if ($cek_username != null) {
        return ['status' => -1, 'message' => 'Username sudah ada!']; // Kembalikan array untuk AJAX
    } else if ($password != $konfirmasi_password) {
        return ['status' => -1, 'message' => 'Password Tidak Sesuai!']; // Kembalikan array untuk AJAX
    }

    $stmt = $pdo->prepare("INSERT INTO `user` (username, password, email, nohp) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $password, $email, $nohp]);

    return ['status' => $stmt->rowCount()]; // Kembalikan status
}


// Fungsi Login (Ini biasanya tidak dipanggil oleh AJAX, jadi mungkin tidak perlu diubah)
function login_akun()
{
    global $pdo;

    $username = htmlspecialchars($_POST["username"]);
    $password = md5(htmlspecialchars($_POST["password"]));

    $cek_akun_admin = $pdo->prepare("SELECT * FROM `admin` WHERE username = ? AND password = ?");
    $cek_akun_admin->execute([$username, $password]);
    $cek_akun_user = $pdo->prepare("SELECT * FROM `user` WHERE username = ? AND password = ?");
    $cek_akun_user->execute([$username, $password]);

    $cek_akun_admin = $cek_akun_admin->fetch(PDO::FETCH_ASSOC);
    $cek_akun_user = $cek_akun_user->fetch(PDO::FETCH_ASSOC);

    if ($cek_akun_admin == null && $cek_akun_user == null) return false;

    if ($cek_akun_user != null) {
        $_SESSION["akun-user"] = ["username" => $username, "password" => $password];
    }

    if ($cek_akun_admin != null) {
        $_SESSION["akun-admin"] = ["username" => $username, "password" => $password];
    }

    header("Location: index.php");
    exit;
}

// Dalam ambil_data di function.php, pastikan menerima array params
function ambil_data($query, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        // Tentukan tipe data, PDO::PARAM_INT untuk limit/offset
        $param_type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $param_type);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generate_kode_pesanan($pdo_conn) {
    // Logika untuk membuat kode pesanan unik (misal: "ORD-YYYYMMDD-001")
    $prefix = "ORD-" . date("Ymd") . "-";
    $stmt = $pdo_conn->prepare("SELECT kode_pesanan FROM transaksi WHERE kode_pesanan LIKE ? ORDER BY kode_pesanan DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last_kode = $stmt->fetchColumn();

    $next_num = 1;
    if ($last_kode) {
        $parts = explode('-', $last_kode);
        $last_num = (int)end($parts);
        $next_num = $last_num + 1;
    }
    return $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);
}

function generate_nomor_antrean($pdo_conn) {
    // Logika untuk membuat nomor antrean unik (misal: "YYYYMMDD-001")
    $tanggal_sekarang = date('Ymd');
    $query_last_antrian = "SELECT nomor_antrian FROM transaksi WHERE DATE(tanggal_transaksi) = CURRENT_DATE() AND jenis_pesanan = 'Takeaway' ORDER BY kode_pesanan DESC LIMIT 1";
    $stmt = $pdo_conn->prepare($query_last_antrian);
    $stmt->execute();
    $last_antrian_raw = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_antrian_num = 0;

    if (!empty($last_antrian_raw) && isset($last_antrian_raw['nomor_antrian']) && $last_antrian_raw['nomor_antrian'] !== null) {
        $parts = explode('-', $last_antrian_raw['nomor_antrian']);
        if (count($parts) === 2 && $parts[0] === $tanggal_sekarang) {
            $last_antrian_num = (int)$parts[1];
        }
    }
    $nomor_antrian_baru = $last_antrian_num + 1;
    return $tanggal_sekarang . '-' . str_pad($nomor_antrian_baru, 3, '0', STR_PAD_LEFT);
}

function tambah_data_pesanan($pelanggan, $nomor_meja, $nomor_antrian, $jenis_pesanan)
{
    global $pdo;
    // Generate kode pesanan
    $kode_pesanan = uniqid('OGT-'); // Menambahkan prefix 'OGT-' agar lebih mudah dikenali

    // Mendapatkan data pesanan dari keranjang
    $list_pesanan = [];
    $total_harga = 0; // Inisialisasi total harga
    foreach ($_SESSION['cart'] as $item) {
        // *** PENTING: Pastikan kunci 'harga' ada di $item ***
        // Karena di handle_cart_ajax.php kita sekarang menyimpan dengan kunci 'harga'
        if (!empty($item['kode_menu']) && !empty($item['quantity']) && isset($item['harga'])) { 
            $list_pesanan[] = [
                "kode_menu" => $item['kode_menu'],
                "qty" => $item['quantity'],
                "harga_satuan" => $item['harga'] // Ini sudah benar asalkan $item['harga'] tersedia
            ];
            $total_harga += ($item['quantity'] * $item['harga']);
        } else {
            error_log("DEBUG: Item keranjang tidak lengkap: " . print_r($item, true));
        }
    }

    // Cek jika ada pesanan yang kosong
    if (count($list_pesanan) == 0) {
        return ['status' => -1, 'message' => 'Keranjang kosong, tidak ada item yang dipesan!'];
    }

    // Mulai transaksi database untuk memastikan konsistensi data
    $pdo->beginTransaction();

    try {
        // 1. Tambah data transaksi (utama) terlebih dahulu
        // Kolom 'waktu' diubah menjadi 'tanggal_transaksi' agar lebih jelas, sesuaikan jika nama kolom Anda berbeda
        // Tambahkan 'status_transaksi', 'nomor_meja', 'nomor_antrian', 'jenis_pesanan', dan 'total_harga'
        $stmt_transaksi = $pdo->prepare("INSERT INTO transaksi (kode_pesanan, nama_pelanggan, tanggal_transaksi, total_harga, status_transaksi, nomor_meja, nomor_antrian, jenis_pesanan)
                                          VALUES (:kode_pesanan, :nama_pelanggan, NOW(), :total_harga, 'Pending', :nomor_meja, :nomor_antrian, :jenis_pesanan)");

        $stmt_transaksi->bindValue(':kode_pesanan', $kode_pesanan, PDO::PARAM_STR);
        $stmt_transaksi->bindValue(':nama_pelanggan', $pelanggan, PDO::PARAM_STR);
        $stmt_transaksi->bindValue(':total_harga', $total_harga, PDO::PARAM_STR); // Atau PDO::PARAM_STR/FLOAT tergantung tipe kolom
        $stmt_transaksi->bindValue(':nomor_meja', $nomor_meja, PDO::PARAM_STR);
        $stmt_transaksi->bindValue(':nomor_antrian', $nomor_antrian, PDO::PARAM_STR);
        $stmt_transaksi->bindValue(':jenis_pesanan', $jenis_pesanan, PDO::PARAM_STR);
        $stmt_transaksi->execute();

        // 2. Tambah detail pesanan ke tabel 'pesanan'
        foreach ($list_pesanan as $lp) {
            // Hapus nomor_meja dan nomor_antrian dari tabel pesanan,
            // karena ini adalah detail transaksi, bukan detail item pesanan
            $stmt_pesanan = $pdo->prepare("INSERT INTO pesanan (kode_pesanan, kode_menu, qty)
                                            VALUES (:kode_pesanan, :kode_menu, :qty)");
            $stmt_pesanan->bindValue(':kode_pesanan', $kode_pesanan, PDO::PARAM_STR);
            $stmt_pesanan->bindValue(':kode_menu', $lp["kode_menu"], PDO::PARAM_STR);
            $stmt_pesanan->bindValue(':qty', $lp["qty"], PDO::PARAM_INT);
            $stmt_pesanan->execute();
        }

        // Commit transaksi jika semua query berhasil
        $pdo->commit();

        // Mengembalikan hasil
        return ['status' => $stmt_transaksi->rowCount(), 'kode_pesanan' => $kode_pesanan, 'message' => 'Pesanan berhasil dibuat!'];

    } catch (PDOException $e) {
        // Rollback transaksi jika ada error
        $pdo->rollBack();
        error_log("Error saat menambah pesanan/transaksi: " . $e->getMessage()); // Log error untuk debugging
        return ['status' => 0, 'message' => 'Pesanan Gagal Dikirim: ' . $e->getMessage()]; // Beri pesan error spesifik
    }
}


// Fungsi untuk menghapus pesanan
function hapus_data_pesanan($kode_pesanan)
{
    global $pdo;

    // Pastikan kode_pesanan ada
    if (!$kode_pesanan) {
        return 0; // Tidak ada data untuk dihapus
    }

    // Mulai transaksi database
    $pdo->beginTransaction();

    try {
        // Hapus data pesanan dari tabel 'pesanan'
        $stmt = $pdo->prepare("DELETE FROM pesanan WHERE kode_pesanan = :kode_pesanan");
        $stmt->bindValue(':kode_pesanan', $kode_pesanan, PDO::PARAM_STR);
        $stmt->execute();
        $rows_deleted_pesanan = $stmt->rowCount();

        // Dapatkan ID meja yang terkait dengan transaksi ini sebelum menghapusnya
        $stmt_get_meja = $pdo->prepare("SELECT id_meja FROM transaksi WHERE kode_pesanan = :kode_pesanan");
        $stmt_get_meja->bindValue(':kode_pesanan', $kode_pesanan, PDO::PARAM_STR);
        $stmt_get_meja->execute();
        $transaksi_data = $stmt_get_meja->fetch(PDO::FETCH_ASSOC);

        // Hapus data transaksi yang terkait dengan kode_pesanan
        $stmt2 = $pdo->prepare("DELETE FROM transaksi WHERE kode_pesanan = :kode_pesanan");
        $stmt2->bindValue(':kode_pesanan', $kode_pesanan, PDO::PARAM_STR);
        $stmt2->execute();
        $rows_deleted_transaksi = $stmt2->rowCount();

        // Jika transaksi dihapus dan ada id_meja yang terkait, update status meja menjadi 'Tersedia'
        if ($rows_deleted_transaksi > 0 && isset($transaksi_data['id_meja']) && $transaksi_data['id_meja'] !== null) {
            $stmt_update_meja = $pdo->prepare("UPDATE meja_cafe SET status = 'Tersedia' WHERE id_meja = :id_meja");
            $stmt_update_meja->bindValue(':id_meja', $transaksi_data['id_meja'], PDO::PARAM_INT);
            $stmt_update_meja->execute();
        }

        $pdo->commit(); // Commit transaksi jika semua berhasil

        return $rows_deleted_pesanan + $rows_deleted_transaksi; // Mengembalikan jumlah baris yang dihapus
    } catch (PDOException $e) {
        $pdo->rollBack(); // Rollback jika ada error
        error_log("Error saat menghapus pesanan/transaksi: " . $e->getMessage());
        return 0; // Gagal menghapus
    }
}

function tambah_data_menu()
{
    global $pdo;

    // Mengambil data dari formulir
    $nama = htmlspecialchars($_POST["nama"]);
    $harga = (int) htmlspecialchars($_POST["harga"]);
    $gambar = htmlspecialchars($_FILES["gambar"]["name"]);
    $kategori = htmlspecialchars($_POST["kategori"]);
    $status = htmlspecialchars($_POST["status"]);

    // Cek format gambar
    $format_gambar = ["jpg", "jpeg", "png", "gif"];
    $cek_gambar = explode(".", $gambar);
    $cek_gambar = strtolower(end($cek_gambar));

    if (!in_array($cek_gambar, $format_gambar)) {
        return ['status' => -1, 'message' => 'File yang diupload bukan merupakan image!'];
    }

    // Upload gambar
    $nama_gambar = uniqid() . ".$cek_gambar"; // Nama gambar unik
    if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], "src/img/$nama_gambar")) {
        return ['status' => -1, 'message' => 'Gagal mengupload gambar!'];
    }

    // Ambil kode_menu terakhir dari database
    $stmt = $pdo->prepare("SELECT kode_menu FROM menu ORDER BY kode_menu DESC LIMIT 1");
    $stmt->execute();
    $last_code = $stmt->fetchColumn();

    // Jika ada kode_menu sebelumnya, tambahkan 1
    if ($last_code) {
        $last_number = (int) substr($last_code, 2); // Ambil angka setelah 'MN'
        $new_number = $last_number + 1; // Tambah 1
    } else {
        $new_number = 1; // Jika belum ada data, mulai dengan angka 1
    }

    // Format kode_menu dengan awalan 'MN' dan angka yang sudah ditentukan
    $kode_menu = "MN" . str_pad($new_number, 3, '0', STR_PAD_LEFT); // Misal 'MN001', 'MN002', dst.

    // Insert data ke dalam tabel menu
    $stmt = $pdo->prepare("INSERT INTO menu (kode_menu, nama, harga, gambar, kategori, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$kode_menu, $nama, $harga, $nama_gambar, $kategori, $status]);

    return ['status' => $stmt->rowCount()]; // Mengembalikan jumlah baris yang terpengaruh
}

function edit_data_menu()
{
    global $pdo;

    // Mendapatkan data dari formulir
    $id_menu = $_POST["id_menu"];
    $nama = htmlspecialchars($_POST["nama"]);
    $harga = (int) htmlspecialchars($_POST["harga"]);
    $gambar = htmlspecialchars($_FILES["gambar"]["name"]);
    $kategori = htmlspecialchars($_POST["kategori"]);
    $status = htmlspecialchars($_POST["status"]);
    $gambar_lama = $_POST["gambar-lama"];

    // Default nama gambar jika tidak ada upload baru
    $nama_gambar_final = $gambar_lama;

    // Cek format gambar jika ada gambar baru diupload
    if (!empty($gambar)) {
        $format_gambar = ["jpg", "jpeg", "png", "gif"];
        $cek_gambar = explode(".", $gambar);
        $cek_gambar = strtolower(end($cek_gambar));

        if (!in_array($cek_gambar, $format_gambar)) {
            return ['status' => -1, 'message' => 'File yang diupload bukan merupakan image!'];
        }

        // Upload file gambar baru
        $nama_gambar_final = uniqid() . ".$cek_gambar";
        if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], "src/img/$nama_gambar_final")) {
            return ['status' => -1, 'message' => 'Gagal mengupload gambar baru!'];
        }

        // Hapus gambar lama jika ada gambar baru diupload
        if (!empty($gambar_lama) && file_exists("src/img/$gambar_lama")) {
            unlink("src/img/$gambar_lama");
        }
    }

    // Update data menu
    $stmt = $pdo->prepare("UPDATE menu SET nama = ?, harga = ?, gambar = ?, kategori = ?, `status` = ? WHERE id_menu = ?");
    $stmt->execute([$nama, $harga, $nama_gambar_final, $kategori, $status, $id_menu]);

    return ['status' => $stmt->rowCount()];
}

// Fungsi untuk menghapus menu
function hapus_data_menu($id_menu)
{
    global $pdo;

    // Pastikan id_menu ada
    if (!$id_menu) {
        return 0; // Tidak ada data untuk dihapus
    }

    // Hapus menu dari tabel menu
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id_menu = ?");
    $stmt->execute([$id_menu]);

    return $stmt->rowCount();  // Mengembalikan jumlah baris yang dihapus
}

// Fungsi untuk mengambil semua data meja
function ambil_data_meja() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM meja_cafe ORDER BY nomor_meja ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Log error atau tampilkan pesan yang ramah pengguna
        error_log("Error mengambil data meja: " . $e->getMessage());
        return []; // Kembalikan array kosong jika ada error
    }
}

// Fungsi untuk mengambil data meja yang tersedia
function ambil_meja_tersedia() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM meja_cafe WHERE status = 'Tersedia' ORDER BY nomor_meja ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error mengambil meja tersedia: " . $e->getMessage());
        return [];
    }
}

// Fungsi untuk memperbarui status meja
function update_status_meja($id_meja, $status) {
    global $pdo; // Pastikan $pdo tersedia
    $stmt = $pdo->prepare("UPDATE meja_cafe SET status = :status WHERE id_meja = :id_meja");
    $stmt->execute([':status' => $status, ':id_meja' => $id_meja]);
    return $stmt->rowCount(); // Mengembalikan jumlah baris yang terpengaruh
}

function get_id_meja_by_nomor($nomor_meja) {
    global $pdo; // Pastikan $pdo tersedia
    $stmt = $pdo->prepare("SELECT id_meja FROM meja_cafe WHERE nomor_meja = :nomor_meja AND status = 'Tersedia'");
    $stmt->execute([':nomor_meja' => $nomor_meja]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id_meja'] : null;
}

// BARU: Fungsi untuk mengambil id_meja tanpa mempedulikan status (untuk hapus_data_pesanan)
function get_id_meja_by_nomor_tanpa_status($nomor_meja) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id_meja FROM meja_cafe WHERE nomor_meja = :nomor_meja");
    $stmt->execute([':nomor_meja' => $nomor_meja]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id_meja'] : null;
}