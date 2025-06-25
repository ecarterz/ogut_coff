$(document).ready(function () {
    console.log("beranda.js: Document is ready.");

    // KODE MENU SIDEBAR
    $('#menu-list').on('click', function () {
        $('#dropdown-menu').toggle();
        var icon = $(this).find('i');
        if (icon.hasClass('bi-list')) {
            icon.removeClass('bi-list').addClass('bi-x');
        } else {
            icon.removeClass('bi-x').addClass('bi-list');
        }
        console.log("beranda.js: Menu list toggled.");
    });

    // --- KODE TAMBAH KE KERANJANG (AJAX) ---
    $(document).on('submit', '.add-to-cart-form', function (event) {
        event.preventDefault(); // Mencegah form dari submit biasa

        var form = $(this);
        var formData = form.serialize();
        console.log("beranda.js: Add-to-cart form submitted. Data:", formData);

        $.ajax({
            type: 'POST',
            url: 'handle_cart_ajax.php', // Ini URL yang benar untuk add-to-cart
            data: formData + '&action=add', // Tambahkan action 'add' jika handle_cart_ajax.php memerlukannya
            dataType: 'json',
            success: function (response) {
                console.log("beranda.js: Add-to-cart AJAX Success! Response:", response);
                if (response.success) {
                    alert(response.message);
                    loadCartItems(); // Muat ulang keranjang setelah menambah item
                } else {
                    alert('Gagal menambahkan item ke keranjang: ' + (response.message || 'Unknown error.'));
                }
            },
            error: function (xhr, status, error) {
                console.error('beranda.js: Add-to-cart AJAX Error:', status, error, xhr.responseText);
                alert('Terjadi kesalahan saat menambahkan item ke keranjang. Silakan coba lagi.');
            }
        });
    });

    // --- KODE CHECKOUT (AJAX) ---
    // Gunakan ID form checkout yang sudah Anda tambahkan di index.php
    $(document).on('submit', '#checkoutForm', function (event) {
        event.preventDefault(); // Mencegah form dari submit biasa

        var form = $(this);
        var formData = form.serialize();
        console.log("beranda.js: Checkout form submitted. Data:", formData);

        $.ajax({
            type: 'POST',
            url: 'handle_checkout_ajax.php', // <--- UBAH URL INI KE handle_checkout_ajax.php!
            data: formData,
            dataType: 'json',
            success: function (response) {
                console.log("beranda.js: Checkout AJAX Success! Full Response:", response);

                if (response.success) {
                    let alertMessage = response.message || 'Pesanan berhasil dibuat!';

                    // Pastikan properti ada sebelum digunakan
                    if (response.jenis_pesanan && response.nomor_info) {
                        if (response.jenis_pesanan === 'Takeaway') {
                            alertMessage += " Nomor Antrean Anda: " + response.nomor_info;
                        } else if (response.jenis_pesanan === 'Dine-in') {
                            alertMessage += " Meja: " + response.nomor_info;
                        }
                    } else {
                        console.warn("beranda.js: 'jenis_pesanan' atau 'nomor_info' tidak lengkap dalam respons sukses.");
                        alertMessage += " (Informasi nomor meja/antrean tidak tersedia).";
                    }

                    alert(alertMessage);
                    $('#cartModal').modal('hide'); // Tutup modal keranjang
                    loadCartItems(); // Muat ulang item keranjang (seharusnya kosong sekarang)

                } else {
                    // Jika PHP mengembalikan success: false (misal: keranjang kosong, meja tidak valid)
                    alert('Gagal checkout: ' + (response.message || 'Terjadi kesalahan tidak dikenal.'));
                }
            },
            error: function (xhr, status, error) {
                // Log kesalahan lengkap untuk debugging
                console.error('beranda.js: Checkout AJAX Error:', status, error, xhr.responseText);
                alert('Terjadi kesalahan saat checkout. Silakan coba lagi. Detail: ' + (xhr.responseText || error));
            }
        });
    });

    // --- FUNGSI UNTUK MEMUAT ULANG ISI KERANJANG ---
    function loadCartItems() {
        console.log("beranda.js: Loading cart items via AJAX.");
        var cartItemsContainer = $('#cartItems');
        cartItemsContainer.html('<p class="text-center">Memuat keranjang...</p>'); // Tampilkan loading

        $.ajax({
            url: 'get_cart_items.php', // Panggil file PHP baru
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log("beranda.js: Cart items loaded via AJAX:", response);
                var cartHtml = '';
                var totalPrice = 0;

                if (response.cartItems && response.cartItems.length > 0) {
                    $.each(response.cartItems, function (index, item) {
                        cartHtml += '<div class="cart-item d-flex justify-content-between align-items-center mb-2 py-2 border-bottom">';
                        cartHtml += '<img src="src/img/' + item.img + '" alt="' + item.name + '" class="rounded me-2" style="width: 60px; height: 60px; object-fit: cover;">';
                        cartHtml += '<div class="item-details flex-grow-1">';
                        cartHtml += '<div class="item-name fw-bold">' + item.name + '</div>'; // Mengakses item.name
                        // Perhatikan: sekarang kita menggunakan item.harga di handle_cart_ajax.php dan function.php,
                        // Jadi di sini juga harusnya item.harga jika Anda ingin tampilkan 'harga' satuan
                        cartHtml += '<div class="item-quantity text-muted">Qty: ' + item.quantity + ' @ Rp ' + new Intl.NumberFormat('id-ID').format(item.price) + '</div>'; // Mengakses item.quantity, item.price
                        cartHtml += '</div>';
                        cartHtml += '<div class="item-price text-end me-2">Rp ' + new Intl.NumberFormat('id-ID').format(item.subtotal) + '</div>'; // Mengakses item.subtotal
                        cartHtml += '<button type="button" class="btn btn-sm btn-outline-danger remove-from-cart" data-kode-menu="' + item.kode_menu + '"><i class="bi bi-trash"></i></button>'; // Mengakses item.kode_menu
                        cartHtml += '</div>';
                        // totalPrice += item.subtotal; // Sudah dihitung di PHP dan dikirim
                    });
                    cartHtml += '<hr class="my-3">';
                    cartHtml += '<div class="total-price fw-bold fs-5 text-end">Total: Rp ' + new Intl.NumberFormat('id-ID').format(response.totalPrice) + '</div>'; // Menggunakan response.totalPrice
                } else {
                    cartHtml = '<p class="text-center text-muted fst-italic py-3">Keranjang kosong.</p>';
                }

                cartItemsContainer.html(cartHtml);
                $('#cartCount').text(response.cartCount);
                // ... (sisanya sama) ...

                // Sembunyikan/tampilkan tombol checkout berdasarkan isi keranjang
                if (response.cartCount > 0) {
                    $('#checkoutForm button[name="checkout"]').prop('disabled', false);
                    $('#checkoutForm #pelanggan').prop('required', true); // Nama pelanggan wajib jika ada item
                    $('#checkoutForm #nomor_meja').prop('required', $('#dine_in').is(':checked')); // Meja wajib jika dine-in
                } else {
                    $('#checkoutForm button[name="checkout"]').prop('disabled', true);
                    $('#checkoutForm #pelanggan').prop('required', false); // Tidak wajib jika keranjang kosong
                    $('#checkoutForm #nomor_meja').prop('required', false); // Tidak wajib jika keranjang kosong
                }

            },
            error: function (xhr, status, error) {
                console.error('beranda.js: Error loading cart items:', status, error, xhr.responseText);
                cartItemsContainer.html('<p class="text-danger text-center">Gagal memuat item keranjang. Silakan coba lagi.</p>');
            }
        });
    }

    // KODE MODAL KERANJANG - Akan memuat konten setiap kali modal dibuka
    $('#cartModal').on('show.bs.modal', function (event) {
        console.log("beranda.js: Cart modal is about to be shown.");
        loadCartItems(); // Panggil fungsi untuk memuat item keranjang
    });

    // --- LOGIKA UNTUK PILIHAN DINE-IN / TAKEAWAY ---
    const dineInRadio = $('#dine_in');
    const takeAwayRadio = $('#take_away');
    const dineInOptions = $('#dine_in_options');

    function toggleFormOptions() {
        console.log("beranda.js: Toggling form options based on radio selection.");
        if (dineInRadio.is(':checked')) {
            dineInOptions.show(); // Tampilkan opsi meja
            $('#nomor_meja').prop('required', true); // Wajibkan pemilihan meja
        } else { // takeAwayRadio.is(':checked')
            dineInOptions.hide(); // Sembunyikan opsi meja
            $('#nomor_meja').prop('required', false); // Meja tidak wajib untuk takeaway
        }
    }

    // Set initial state saat halaman dimuat
    toggleFormOptions();

    // Tambahkan event listeners untuk radio buttons
    dineInRadio.on('change', toggleFormOptions);
    takeAwayRadio.on('change', toggleFormOptions);

    // KODE HAPUS DARI KERANJANG (AJAX)
    $(document).on('click', '.remove-from-cart', function () {
        var kodeMenu = $(this).data('kode-menu');
        console.log("beranda.js: Remove from cart clicked for:", kodeMenu);

        $.ajax({
            url: 'handle_cart_ajax.php', // Atau file PHP yang menangani penghapusan
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'remove', // Penting: Kirim action 'remove'
                kode_menu: kodeMenu
            },
            success: function (response) {
                console.log("beranda.js: Remove from cart success:", response);
                if (response.success) {
                    loadCartItems(); // Muat ulang item keranjang di modal
                    alert('Item berhasil dihapus dari keranjang.');
                } else {
                    alert('Gagal menghapus item: ' + (response.message || 'Unknown error.'));
                }
            },
            error: function (xhr, status, error) {
                console.error('beranda.js: Remove from cart AJAX Error:', status, error, xhr.responseText);
                alert('Terjadi kesalahan saat menghapus item.');
            }
        });
    });

}); // Akhir dari $(document).ready()