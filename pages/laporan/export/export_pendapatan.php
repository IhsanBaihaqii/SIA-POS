<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../includes/export_helper.php';
requireLogin();

$tgl_mulai   = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-t');

$sql = "SELECT t.*, p.nama AS nama_pelanggan 
        FROM transaksi t 
        LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
        WHERE t.status_pembayaran = 'lunas' AND t.tanggal BETWEEN ? AND ? 
        ORDER BY t.tanggal ASC, t.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$transaksiList = $stmt->fetchAll();

$subtitle = 'Periode: ' . date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai));

excelHeaders('laporan_pendapatan_' . $tgl_mulai . '_sd_' . $tgl_selesai . '.xls');
excelStart('LAPORAN PENDAPATAN', $subtitle);
?>

<table>
    <thead>
        <tr>
            <th style="width:80px;">No</th>
            <th style="width:100px;">Tanggal</th>
            <th style="width:180px;">No Transaksi</th>
            <th style="width:200px;">Pelanggan</th>
            <th style="width:140px;">Tanggal Bayar</th>
            <th style="width:130px;">Total (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($transaksiList) > 0): ?>
            <?php $no = 1; $total = 0; foreach ($transaksiList as $t): $total += $t['total']; ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($t['no_transaksi']) ?></td>
                    <td><?= htmlspecialchars($t['nama_pelanggan'] ?? 'Umum') ?></td>
                    <td class="text-center"><?= $t['tanggal_pembayaran'] ? date('d/m/Y H:i', strtotime($t['tanggal_pembayaran'])) : '-' ?></td>
                    <td class="text-right"><?= excelNumber($t['total']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="bg-total">
                <td colspan="5" class="text-right"><b>TOTAL</b></td>
                <td class="text-right"><b><?= excelNumber($total) ?></b></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center"><i>Tidak ada transaksi lunas pada periode ini.</i></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
excelEnd();
exit;