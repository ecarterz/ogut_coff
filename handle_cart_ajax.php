<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "function.php"; // Pastikan path ini benar

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid action or request.'];

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $product_id = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if ($product_id && $quantity >= 1) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Cek apakah item sudah ada di keranjang untuk update kuantitas
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                $response = ['success' => true, 'message' => 'Jumlah item di keranjang berhasil diperbarui!', 'cartCount' => count($_SESSION['cart'])];
            } else {
                // Jika item belum ada, ambil data dari database
                $stmt = $pdo->prepare("SELECT kode_menu, nama, harga, gambar FROM menu WHERE kode_menu = :id");
                $stmt->execute([':id' => $product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    // Simpan data ke SESSION['cart'] dengan kunci 'harga'
                    $_SESSION['cart'][$product_id] = [
                        'kode_menu' => $product['kode_menu'],
                        'name' => $product['nama'],
                        'harga' => (float)$product['harga'], // <--- KOREKSI INI: Gunakan kunci 'harga'
                        'quantity' => (int)$quantity,
                        'img' => $product['gambar'],
                    ];
                    $response = ['success' => true, 'message' => 'Item berhasil ditambahkan ke keranjang!', 'cartCount' => count($_SESSION['cart'])];
                } else {
                    $response = ['success' => false, 'message' => 'Menu tidak ditemukan di database.'];
                }
            }
        } else {
            $response = ['success' => false, 'message' => 'Product ID atau quantity tidak valid untuk penambahan.'];
        }
    } elseif ($action === 'remove') {
        $kode_menu_to_remove = $_POST['kode_menu'] ?? null;

        if ($kode_menu_to_remove && isset($_SESSION['cart'][$kode_menu_to_remove])) {
            unset($_SESSION['cart'][$kode_menu_to_remove]);
            $response = ['success' => true, 'message' => 'Item berhasil dihapus dari keranjang.', 'cartCount' => count($_SESSION['cart'])];
        } else {
            $response = ['success' => false, 'message' => 'Item tidak ditemukan di keranjang atau kode menu tidak valid untuk penghapusan.'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Aksi tidak dikenal.'];
    }
} else {
    $response = ['success' => false, 'message' => 'Tidak ada aksi yang ditentukan.'];
}

ob_clean();
echo json_encode($response);
exit();
?>