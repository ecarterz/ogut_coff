<?php
ob_start(); // Pastikan ini baris PERTAMA
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "function.php"; // Mungkin tidak mutlak perlu jika hanya baca sesi, tapi aman untuk konsistensi

header('Content-Type: application/json');

$cartItems = [];
$cartCount = 0;
$totalPrice = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $kode_menu => $item) {
        $subtotal = $item['harga'] * $item['quantity'];
        $totalPrice += $subtotal;
        
        $cartItems[] = [
            'kode_menu' => $item['kode_menu'] ?? $kode_menu, // Fallback jika 'kode_menu' tidak disimpan
            'name' => $item['name'] ?? 'Unknown Item',    // Sesuai dengan yang disimpan di handle_cart_ajax.php
            'price' => (float)$item['harga'] ?? 0,
            'quantity' => (int)$item['quantity'] ?? 0,
            'img' => $item['img'] ?? 'default.jpg', // Sesuai dengan yang disimpan
            'subtotal' => $subtotal
        ];
    }
    $cartCount = count($_SESSION['cart']);
}

ob_clean(); // Bersihkan buffer sebelum output JSON
echo json_encode([
    'cartItems' => $cartItems,
    'cartCount' => $cartCount,
    'totalPrice' => $totalPrice // Mengirim total harga juga
]);
exit();
?>