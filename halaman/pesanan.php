<table class="table table-bordered table-hover" style="margin-top: 100px;">
    <tr class="text-bg-success">
        <th>No</th>
        <th>Kode Pesanan</th>
        <th>Nama Pelanggan</th>
        <th>Nama Menu</th>
        <th>Qty</th>
        <th>No. Meja</th>
        <th>No. Antrian</th>
        <th>Jenis Pesanan</th> </tr>

    <?php
    $i = 1;
    if (!empty($menu)) {
        foreach ($menu as $m) {
            echo "<tr style='background-color: white;'>";
            echo "<td>{$i}</td>";
            echo "<td>" . htmlspecialchars($m['kode_pesanan']) . "</td>";
            echo "<td>" . htmlspecialchars($m['nama_pelanggan']) . "</td>";
            echo "<td>" . htmlspecialchars($m['nama_menu']) . "</td>";
            echo "<td>" . htmlspecialchars($m['qty']) . "</td>";
            echo "<td>" . (isset($m['nomor_meja']) && $m['nomor_meja'] !== null ? htmlspecialchars($m['nomor_meja']) : '-') . "</td>";
            echo "<td>" . (isset($m['nomor_antrian']) && $m['nomor_antrian'] !== null ? htmlspecialchars($m['nomor_antrian']) : '-') . "</td>";
            echo "<td>" . (isset($m['jenis_pesanan']) ? htmlspecialchars($m['jenis_pesanan']) : '-') . "</td>"; // Tampilkan jenis pesanan
            echo "</tr>";
            $i++;
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>Tidak ada data pesanan.</td></tr>";
    }
    ?>
</table>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= ($halaman_saat_ini_pesanan <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?pesanan&page=<?= $halaman_saat_ini_pesanan - 1; ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_halaman_pesanan; $i++) : ?>
            <li class="page-item <?= ($i == $halaman_saat_ini_pesanan) ? 'active' : ''; ?>">
                <a class="page-link" href="?pesanan&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($halaman_saat_ini_pesanan >= $total_halaman_pesanan) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?pesanan&page=<?= $halaman_saat_ini_pesanan + 1; ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>