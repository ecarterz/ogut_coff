<?php
ob_start(); // Pastikan ini baris PERTAMA di file ini
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logika Session Timeout (Opsional, tapi konsisten dengan index.php)
// Jika Anda ingin timeout juga berlaku untuk permintaan AJAX, pertahankan ini.
// Jika tidak, Anda bisa menghapusnya, tapi pastikan _SESSION['last_activity'] di index.php di-update.
$timeout = 900; // 15 menit
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    // Jika sesi timeout, kirim respons error JSON
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
    exit;
}
$_SESSION['last_activity'] = time(); // Update waktu aktivitas terakhir

require_once "function.php"; // Pastikan path ini benar dan function.php tidak mencetak apapun

error_log("DEBUG: Checkout POST request detected in handle_checkout_ajax.php.");

// Pastikan ini adalah permintaan POST untuk checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // *** PENTING: ob_clean() di sini, sebelum output apapun ***
    ob_clean(); 
    header('Content-Type: application/json'); // Set header JSON

    $pelanggan = htmlspecialchars($_POST["pelanggan"] ?? '');
    $jenis_pesanan = htmlspecialchars($_POST["jenis_pesanan"] ?? '');

    $nomor_meja = null;
    $nomor_antrian = null;
    $nomor_info_for_js = null;

    if (empty($pelanggan)) {
        echo json_encode(['success' => false, 'message' => 'Nama pelanggan tidak boleh kosong.']);
        exit;
    }

    // Periksa keranjang di sini, sebelum memanggil tambah_data_pesanan
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Keranjang belanja kosong, tidak bisa checkout.']);
        exit;
    }

    // Penentuan nomor meja/antrean dan validasi
    if ($jenis_pesanan === 'Dine-in') {
        $nomor_meja = htmlspecialchars($_POST["nomor_meja"] ?? '');
        $id_meja_terpilih = get_id_meja_by_nomor($nomor_meja); // Pastikan fungsi ini ada dan mengembalikan ID
        if (!$id_meja_terpilih) {
            echo json_encode(['success' => false, 'message' => 'Nomor meja tidak valid atau tidak tersedia!']);
            exit;
        }
        $nomor_info_for_js = $nomor_meja;
    } else { // Asumsi 'Takeaway'
        try {
            $nomor_antrian = generate_nomor_antrean($pdo);
            if (!$nomor_antrian) {
                 echo json_encode(['success' => false, 'message' => 'Gagal membuat nomor antrean.']);
                 exit;
            }
        } catch (PDOException $e) {
            error_log("Error generating queue number: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan internal saat membuat nomor antrean.']);
            exit;
        }
        $nomor_info_for_js = $nomor_antrian;
    }

    error_log("DEBUG: S_SESSION['cart'] before tambah_data_pesanan: " . print_r($_SESSION['cart'] ?? 'Cart is not set', true));

    // Panggil fungsi tambah_data_pesanan dari function.php
    // Pastikan tambah_data_pesanan TIDAK mencetak output apapun
    $result = tambah_data_pesanan($pelanggan, $nomor_meja, $nomor_antrian, $jenis_pesanan);

    if ($result['status'] > 0) { // Asumsi status > 0 berarti sukses
        if ($jenis_pesanan === 'Dine-in' && $id_meja_terpilih) {
            update_status_meja($id_meja_terpilih, 'Terisi'); // Pastikan fungsi ini ada dan tidak ada output
        }

        unset($_SESSION['cart']); // Kosongkan keranjang
        error_log("DEBUG: S_SESSION['cart'] AFTER successful checkout (should be empty): " . print_r($_SESSION['cart'] ?? 'Cart is not set', true));

        echo json_encode([
            'success' => true,
            'message' => 'Pesanan Berhasil Dikirim!',
            'kode_pesanan' => $result['kode_pesanan'],
            'jenis_pesanan' => $jenis_pesanan,
            'nomor_info' => $nomor_info_for_js
        ]);
        exit; // *** PENTING: Pastikan exit() di sini ***
    } else {
        echo json_encode(['success' => false, 'message' => $result['message'] ?: 'Pesanan Gagal Dikirim!']);
        exit; // *** PENTING: Pastikan exit() di sini ***
    }
} else {
    // Jika bukan POST request, kembalikan error JSON atau redirect
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Metode permintaan tidak valid.']);
    exit;
}
?>