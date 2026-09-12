<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../includes/export_helper.php';
requireLogin();

$stmt = $pdo->query("SELECT * FROM barang ORDER BY nama ASC");
$barangList = $stmt->fetchAll();

$subtitle = 'Tanggal Cetak: ' . date('d/m/Y H:i');

excelHeaders('laporan_stok_' . date('Ymd_His') . '.xls');
excelStart('LAPORAN STOK BARANG', $subtitle);
?>

<table>
    <thead>
        <tr>
            <th style="width:50px;">No</th>
            <th style="width:100px;">Kode</th>
            <th style="width:200px;">Nama</th>
            <th style="width:100px;">Kategori</th>
            <th style="width:80px;">Satuan</th>
            <th style="width:110px;">Harga Beli</th>
            <th style="width:110px;">Harga Jual</th>
            <th style="width:80px;">Stok</th>
            <th style="width:140px;">Nilai Persediaan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $totalNilai = 0;
        $totalStok  = 0;
        foreach ($barangList as $b):
            $nilai = $b['stok'] * $b['harga_beli'];
            $totalNilai += $nilai;
            $totalStok  += $b['stok'];
            $rowClass   = $b['stok'] <= 5 ? 'bg-danger' : '';
        ?>
            <tr class="<?= $rowClass ?>">
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($b['kode']) ?></td>
                <td><?= htmlspecialchars($b['nama']) ?></td>
                <td class="text-center"><?= htmlspecialchars($b['kategori']) ?></td>
                <td class="text-center"><?= htmlspecialchars($b['satuan']) ?></td>
                <td class="text-right"><?= excelNumber($b['harga_beli']) ?></td>
                <td class="text-right"><?= excelNumber($b['harga_jual']) ?></td>
                <td class="text-right"><?= (int)$b['stok'] ?></td>
                <td class="text-right"><?= excelNumber($nilai) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="bg-total">
            <td colspan="7" class="text-right"><b>TOTAL</b></td>
            <td class="text-right"><b><?= (int)$totalStok ?></b></td>
            <td class="text-right"><b><?= excelNumber($totalNilai) ?></b></td>
        </tr>
    </tbody>
</table>

<?php
excelEnd();
exit;