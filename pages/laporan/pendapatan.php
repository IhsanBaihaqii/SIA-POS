<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
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

$totalPendapatan = 0;
foreach ($transaksiList as $t) {
    $totalPendapatan += $t['total'];
}
$jumlahTransaksi = count($transaksiList);
$rataRata = $jumlahTransaksi > 0 ? $totalPendapatan / $jumlahTransaksi : 0;

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan Pendapatan</h2>
        <p class="text-sm text-gray-500">
            Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tgl_selesai)) ?>
        </p>
    </div>
    <a href="<?= url('pages/laporan/export/export_pendapatan.php?tgl_mulai=' . $tgl_mulai . '&tgl_selesai=' . $tgl_selesai) ?>" 
       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-file-csv mr-2"></i> Export CSV
    </a>
</div>

<form method="GET" class="mb-6 bg-white p-4 rounded-lg shadow flex flex-wrap gap-2 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
        <input type="date" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
        <input type="date" name="tgl_selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        <i class="fa-solid fa-filter"></i> Terapkan
    </button>
</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase">Total Pendapatan</p>
        <p class="text-2xl font-bold text-green-600 mt-1">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase">Jumlah Transaksi</p>
        <p class="text-2xl font-bold text-blue-600 mt-1"><?= $jumlahTransaksi ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
        <p class="text-xs text-gray-500 uppercase">Rata-rata / Transaksi</p>
        <p class="text-2xl font-bold text-purple-600 mt-1">Rp <?= number_format($rataRata, 0, ',', '.') ?></p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Transaksi</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Bayar</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (count($transaksiList) > 0): ?>
                <?php foreach ($transaksiList as $t): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                    <td class="px-4 py-3 whitespace-nowrap font-mono text-sm">
                        <a href="<?= url('pages/transaksi/detail.php?id=' . $t['id']) ?>" class="text-blue-600 hover:underline">
                            <?= htmlspecialchars($t['no_transaksi']) ?>
                        </a>
                    </td>
                    <td class="px-4 py-3"><?= htmlspecialchars($t['nama_pelanggan'] ?? 'Umum') ?></td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        <?= $t['tanggal_pembayaran'] ? date('d/m/Y H:i', strtotime($t['tanggal_pembayaran'])) : '-' ?>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right font-semibold">
                        Rp <?= number_format($t['total'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada transaksi lunas pada periode ini.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="4" class="px-4 py-3 text-right font-bold">TOTAL</td>
                <td class="px-4 py-3 text-right font-bold text-lg text-green-600">
                    Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>