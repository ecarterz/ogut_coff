<!-- beranda.php -->
<div class="text-center mb-4">
    <h2>Selamat Datang di Kopi Ogut</h2>
    <p>Temukan berbagai rasa kopi spesial hanya di Kopi Ogut. Nikmati pengalaman ngopi yang tak terlupakan!</p>
</div>

<!-- Search & Tambah -->
<div class="d-flex flex-wrap justify-content-between">
    <nav class="navbar navbar-light">
        <form action="index.php" method="GET" class="form-inline d-flex">
            <input class="form-control mx-sm-2" type="search" autocomplete="off" name="key-search" placeholder="Cari..">
            <button class="btn btn-success mx-2" name="search">Search</button>
        </form>
    </nav>
    <?php if (isset($_SESSION["akun-admin"])) { ?>
    <nav class="navbar navbar-light">
        <a class="btn btn-success fw-bold mx-2" href="tambah.php">+ Tambah Menu</a>
    </nav>
    <?php } ?>
</div>

<!-- List Menu -->
<div class="container" style="z-index: -1; margin-top: 60px;">
    <?php
    foreach ($menu as $m) {
        echo "
        <div class='col-sm-4'>
            <div class='card'>
                <img src='src/img/{$m['gambar']}' class='card-img-top' alt='...'>
                <div class='card-body'>
                    <h5 class='card-title'>{$m['nama']}</h5>
                    <p class='card-text'>Rp {$m['harga']}</p>
                    <form action='index.php' method='POST'>
                        <input type='hidden' name='product_id' value='{$m['kode_menu']}'>
                        <label for='quantity'>Jumlah:</label>
                        <input type='number' name='quantity' value='1' min='1' class='form-control'>
                        <button type='submit' name='add_to_cart' class='btn btn-primary mt-2'>Tambah ke Keranjang</button>
                    </form>
                </div>
            </div>
        </div>";
    }
    ?>
</div>
