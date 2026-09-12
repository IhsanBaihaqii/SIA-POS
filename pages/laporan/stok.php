<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

// Ringkasan barang
$stmt = $pdo->query("SELECT * FROM barang ORDER BY nama ASC");
$barangList = $stmt->fetchAll();

$totalNilaiStok = 0;
$totalItemStok = 0;
$stokMinimum = [];

foreach ($barangList as $b) {
    $totalNilaiStok += $b['stok'] * $b['harga_beli'];
    $totalItemStok += $b['stok'];
    if ($b['stok'] <= 5) {
        $stokMinimum[] = $b;
    }
}

// Ringkasan mutasi (dalam 30 hari terakhir)
$stmt = $pdo->query("SELECT 
    SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) AS total_masuk,
    SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) AS total_keluar
    FROM stok_mutasi 
    WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$mutasi30 = $stmt->fetch();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan Stok Barang</h2>
        <p class="text-sm text-gray-500">Nilai persediaan & status stok seluruh barang.</p>
    </div>
    <a href="<?= url('pages/laporan/export/export_stok.php') ?>" 
       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-file-csv mr-2"></i> Export CSV
    </a>
</div>

<!-- Ringkasan -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
        <p class="text-xs text-gray-500 uppercase">Total Nilai Stok</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">Rp <?= number_format($totalNilaiStok, 0, ',', '.') ?></p>
        <p class="text-xs text-gray-400 mt-1">Berdasarkan harga beli</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase">Total Item</p>
        <p class="text-2xl font-bold text-green-600 mt-1"><?= number_format($totalItemStok, 0, ',', '.') ?></p>
        <p class="text-xs text-gray-400 mt-1"><?= count($barangList) ?> jenis barang</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
        <p class="text-xs text-gray-500 uppercase">Stok Masuk (30 hari)</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1"><?= number_format($mutasi30['total_masuk'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-orange-500">
        <p class="text-xs text-gray-500 uppercase">Stok Keluar (30 hari)</p>
        <p class="text-2xl font-bold text-orange-600 mt-1"><?= number_format($mutasi30['total_keluar'] ?? 0) ?></p>
    </div>
</div>

<!-- Stok Minimum -->
<?php if (count($stokMinimum) > 0): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg">
    <div class="flex items-start">
        <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-xl mr-3 mt-1"></i>
        <div>
            <p class="font-semibold text-yellow-800">Peringatan: <?= count($stokMinimum) ?> barang dengan stok rendah (≤ 5)</p>
            <p class="text-sm text-yellow-700 mt-1">
                <?php foreach ($stokMinimum as $sm): ?>
                    <span class="inline-block bg-white px-2 py-1 rounded text-xs mr-1 mb-1 border border-yellow-300">
                        <?= htmlspecialchars($sm['nama']) ?>: <?= $sm['stok'] ?>
                    </span>
                <?php endforeach; ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Tabel Barang -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nilai Persediaan</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (count($barangList) > 0): ?>
                <?php foreach ($barangList as $b): ?>
                <tr class="hover:bg-gray-50 <?= $b['stok'] <= 5 ? 'bg-red-50' : '' ?>">
                    <td class="px-4 py-3 whitespace-nowrap font-mono text-sm"><?= htmlspecialchars($b['kode']) ?></td>
                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($b['nama']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($b['kategori']) ?></td>
                    <td class="px-4 py-3 text-right">Rp <?= number_format($b['harga_beli'], 0, ',', '.') ?></td>
                    <td class="px-4 py-3 text-right">Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?></td>
                    <td class="px-4 py-3 text-right font-semibold <?= $b['stok'] <= 5 ? 'text-red-600' : '' ?>">
                        <?= $b['stok'] ?> <?= htmlspecialchars($b['satuan']) ?>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold">
                        Rp <?= number_format($b['stok'] * $b['harga_beli'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada data barang.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot class="bg-gray-50">
            <tr>
                <td colspan="6" class="px-4 py-3 text-right font-bold">TOTAL NILAI PERSEDIAAN</td>
                <td class="px-4 py-3 text-right font-bold text-lg text-blue-600">
                    Rp <?= number_format($totalNilaiStok, 0, ',', '.') ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>