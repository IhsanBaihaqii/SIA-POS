<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../includes/export_helper.php';
requireLogin();

$tgl_mulai   = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-t');

// ===== Ambil data =====
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM transaksi WHERE status_pembayaran='lunas' AND tanggal BETWEEN ? AND ?");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$totalPendapatan = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(dt.harga_beli * dt.jumlah), 0) FROM detail_transaksi dt JOIN transaksi t ON dt.transaksi_id = t.id WHERE t.status_pembayaran='lunas' AND t.tanggal BETWEEN ? AND ?");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$totalHPP = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah), 0) FROM beban WHERE tanggal BETWEEN ? AND ?");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$totalBeban = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT nama_beban, SUM(jumlah) AS total FROM beban WHERE tanggal BETWEEN ? AND ? GROUP BY nama_beban ORDER BY total DESC");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$bebanDetail = $stmt->fetchAll();

$labaKotor  = $totalPendapatan - $totalHPP;
$labaBersih = $labaKotor - $totalBeban;

$subtitle = 'Periode: ' . date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai));

excelHeaders('laporan_laba_rugi_' . $tgl_mulai . '_sd_' . $tgl_selesai . '.xls');
excelStart('LAPORAN LABA / RUGI', $subtitle);
?>

<table>
    <thead>
        <tr>
            <th style="width:350px;">Keterangan</th>
            <th style="width:150px;">Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Pendapatan Penjualan</td>
            <td class="text-right"><?= excelNumber($totalPendapatan) ?></td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;Harga Pokok Penjualan (HPP)</td>
            <td class="text-right"><?= excelNumber($totalHPP) ?></td>
        </tr>
        <tr class="bg-total">
            <td>Laba Kotor</td>
            <td class="text-right"><?= excelNumber($labaKotor) ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Beban Operasional</b></td>
        </tr>
        <?php if (count($bebanDetail) > 0): ?>
            <?php foreach ($bebanDetail as $bd): ?>
                <tr>
                    <td>&nbsp;&nbsp;- <?= htmlspecialchars($bd['nama_beban']) ?></td>
                    <td class="text-right"><?= excelNumber($bd['total']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td>&nbsp;&nbsp;<i>Tidak ada beban pada periode ini</i></td>
                <td class="text-right">0.00</td>
            </tr>
        <?php endif; ?>
        <tr class="bg-total">
            <td>Total Beban</td>
            <td class="text-right"><?= excelNumber($totalBeban) ?></td>
        </tr>
        <tr class="bg-total" style="background:#1e40af;color:#ffffff;">
            <td><b>LABA / RUGI BERSIH</b></td>
            <td class="text-right"><b><?= excelNumber($labaBersih) ?></b></td>
        </tr>
    </tbody>
</table>

<?php
excelEnd();
exit;