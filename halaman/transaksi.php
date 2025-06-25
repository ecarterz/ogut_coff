<div style="margin-top: 20px;">
    <form method="GET" action="laporan.php" target="_blank">
        <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>" required>
        <button type="submit" name="periode" value="harian" class="btn btn-success">Cetak Laporan Harian</button>
        <button type="submit" name="periode" value="bulanan" class="btn btn-warning">Cetak Laporan Bulanan</button>
    </form>
</div>

<table class="table table-bordered table-hover" style="margin-top: 30px;">
    <tr class="text-bg-success">
        <th>No</th>
        <th>Kode Pesanan</th>
        <th>Nama Pelanggan</th>
        <th>Waktu Transaksi</th> <th>Total Harga</th>
        <th>Pembayaran</th>
        <th>Aksi</th> </tr>
    
    <?php
    $i = 1;
    // Pastikan variabel $menu sudah didefinisikan dari index.php
    if (isset($menu) && is_array($menu)) {
        foreach ($menu as $m) {
            // Menggunakan langsung kolom total_harga dari tabel transaksi
            // Asumsi nama kolom di DB Anda adalah 'total_harga'
            $display_total_harga = $m["total_harga"]; 
    ?>

            <form action="cetak/cetak.php" target="_blank" method="GET">
                <input type="hidden" name="kode_pesanan" value="<?= htmlspecialchars($m["kode_pesanan"]); ?>">
                <tr style="background-color: white;">
                    <td><?= $i; ?></td>
                    <td><?= htmlspecialchars($m["kode_pesanan"]); ?></td>
                    <td><?= htmlspecialchars($m["nama_pelanggan"]); ?></td>
                    <td><?= htmlspecialchars($m["tanggal_transaksi"]); ?></td> 
                    <td>Rp. <?= number_format($display_total_harga, 0, ',', '.'); ?></td> <td><input name="pembayaran" min="0" type="number" class="form-control" placeholder="Masukkan Pembayaran"></td>
                    <td>
                        <button type="submit" class="btn btn-primary">Cetak</button>
                        <a class="btn btn-danger" href="hapus.php?kode_pesanan=<?= htmlspecialchars($m["kode_pesanan"]); ?>" onclick="return confirm('Hapus Data Transaksi?')">Hapus</a>
                    </td>
                </tr>
            </form>
    <?php 
            $i++; 
        }
    } else {
        echo '<tr><td colspan="7" class="text-center">Tidak ada data transaksi.</td></tr>';
    }
    ?>
</table>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= ($halaman_saat_ini <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?transaksi&page=<?= $halaman_saat_ini - 1; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_halaman; $i++) : ?>
            <li class="page-item <?= ($i == $halaman_saat_ini) ? 'active' : ''; ?>">
                <a class="page-link" href="?transaksi&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($halaman_saat_ini >= $total_halaman) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?transaksi&page=<?= $halaman_saat_ini + 1; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>