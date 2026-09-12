<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$tgl_mulai   = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-t');

// ===== Pendapatan (transaksi lunas) =====
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total), 0) FROM transaksi 
                       WHERE status_pembayaran = 'lunas' AND tanggal BETWEEN ? AND ?");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$totalPendapatan = $stmt->fetchColumn();

// ===== HPP (harga beli x jumlah dari transaksi lunas) =====
$stmt = $pdo->prepare("SELECT COALESCE(SUM(dt.harga_beli * dt.jumlah), 0) 
                       FROM detail_transaksi dt 
                       JOIN transaksi t ON dt.transaksi_id = t.id 
                       WHERE t.status_pembayaran = 'lunas' AND t.tanggal BETWEEN ? AND ?");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$totalHPP = $stmt->fetchColumn();

// ===== Beban Operasional =====
$stmt = $pdo->prepare("SELECT COALESCE(SUM(jumlah), 0) FROM beban WHERE tanggal BETWEEN ? AND ?");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$totalBeban = $stmt->fetchColumn();

// ===== Detail Beban per jenis (group by nama_beban) =====
$stmt = $pdo->prepare("SELECT nama_beban, SUM(jumlah) AS total 
                       FROM beban 
                       WHERE tanggal BETWEEN ? AND ? 
                       GROUP BY nama_beban 
                       ORDER BY total DESC");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$bebanDetail = $stmt->fetchAll();

// ===== Laba/Rugi =====
$labaKotor = $totalPendapatan - $totalHPP;
$labaBersih = $labaKotor - $totalBeban;

// ===== Jumlah transaksi lunas =====
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transaksi 
                       WHERE status_pembayaran = 'lunas' AND tanggal BETWEEN ? AND ?");
$stmt->execute([$tgl_mulai, $tgl_selesai]);
$jumlahTransaksi = $stmt->fetchColumn();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan Laba / Rugi</h2>
        <p class="text-sm text-gray-500">
            Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($tgl_selesai)) ?>
        </p>
    </div>
    <a href="<?= url('pages/laporan/export/export_laba_rugi.php?tgl_mulai=' . $tgl_mulai . '&tgl_selesai=' . $tgl_selesai) ?>" 
       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-file-csv mr-2"></i> Export CSV
    </a>
</div>

<!-- Filter -->
<form method="GET" class="mb-6 bg-white p-4 rounded-lg shadow flex flex-wrap gap-2 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
        <input type="date" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" 
               class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
        <input type="date" name="tgl_selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" 
               class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        <i class="fa-solid fa-filter"></i> Terapkan
    </button>
    <a href="<?= url('pages/laporan/laba_rugi.php') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg">Bulan Ini</a>
</form>

<!-- Ringkasan Kartu -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
        <p class="text-xs text-gray-500 uppercase">Pendapatan</p>
        <p class="text-xl font-bold text-green-600 mt-1">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
        <p class="text-xs text-gray-400 mt-1"><?= $jumlahTransaksi ?> transaksi lunas</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
        <p class="text-xs text-gray-500 uppercase">HPP</p>
        <p class="text-xl font-bold text-purple-600 mt-1">Rp <?= number_format($totalHPP, 0, ',', '.') ?></p>
        <p class="text-xs text-gray-400 mt-1">Harga pokok penjualan</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
        <p class="text-xs text-gray-500 uppercase">Beban</p>
        <p class="text-xl font-bold text-red-600 mt-1">Rp <?= number_format($totalBeban, 0, ',', '.') ?></p>
        <p class="text-xs text-gray-400 mt-1">Operasional</p>
    </div>
    <div class="bg-white rounded-lg shadow p-5 border-l-4 <?= $labaBersih >= 0 ? 'border-blue-500' : 'border-red-700' ?>">
        <p class="text-xs text-gray-500 uppercase">Laba / Rugi Bersih</p>
        <p class="text-xl font-bold <?= $labaBersih >= 0 ? 'text-blue-600' : 'text-red-700' ?> mt-1">
            Rp <?= number_format($labaBersih, 0, ',', '.') ?>
        </p>
        <p class="text-xs text-gray-400 mt-1">Setelah HPP & beban</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Laporan Formal -->
    <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-4 text-lg">Laporan Laba / Rugi</h3>
        <table class="w-full text-sm">
            <tbody>
                <tr class="border-b">
                    <td class="py-2 font-semibold text-gray-700">Pendapatan Penjualan</td>
                    <td class="py-2 text-right">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600 pl-4">Harga Pokok Penjualan (HPP)</td>
                    <td class="py-2 text-right text-red-600">(Rp <?= number_format($totalHPP, 0, ',', '.') ?>)</td>
                </tr>
                <tr class="border-b-2 border-gray-300 bg-gray-50">
                    <td class="py-2 font-semibold">Laba Kotor</td>
                    <td class="py-2 text-right font-semibold">
                        Rp <?= number_format($labaKotor, 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td class="py-2 font-semibold text-gray-700 pt-4">Beban Operasional</td>
                    <td></td>
                </tr>
                <?php if (count($bebanDetail) > 0): ?>
                    <?php foreach ($bebanDetail as $bd): ?>
                        <tr class="border-b border-dashed">
                            <td class="py-1 text-gray-600 pl-4">- <?= htmlspecialchars($bd['nama_beban']) ?></td>
                            <td class="py-1 text-right text-red-600">Rp <?= number_format($bd['total'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="py-2 text-gray-400 pl-4 text-sm">Tidak ada beban pada periode ini.</td></tr>
                <?php endif; ?>
                <tr class="border-t border-gray-300">
                    <td class="py-2 font-semibold pl-4">Total Beban</td>
                    <td class="py-2 text-right font-semibold text-red-600">(Rp <?= number_format($totalBeban, 0, ',', '.') ?>)</td>
                </tr>
                <tr class="border-t-2 border-gray-400 bg-gray-100">
                    <td class="py-3 font-bold text-lg">LABA / RUGI BERSIH</td>
                    <td class="py-3 text-right font-bold text-lg <?= $labaBersih >= 0 ? 'text-blue-700' : 'text-red-700' ?>">
                        Rp <?= number_format($labaBersih, 0, ',', '.') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Ringkasan Rasio -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-4">Ringkasan</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Margin Kotor</span>
                <span class="font-semibold">
                    <?= $totalPendapatan > 0 ? round(($labaKotor / $totalPendapatan) * 100, 2) : 0 ?>%
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Margin Bersih</span>
                <span class="font-semibold <?= $labaBersih >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $totalPendapatan > 0 ? round(($labaBersih / $totalPendapatan) * 100, 2) : 0 ?>%
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Rata-rata / Transaksi</span>
                <span class="font-semibold">
                    Rp <?= $jumlahTransaksi > 0 ? number_format($totalPendapatan / $jumlahTransaksi, 0, ',', '.') : 0 ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>