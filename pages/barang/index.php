<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$sql = "SELECT * FROM barang WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (nama LIKE ? OR kode LIKE ?)";
    $like = "%$search%";
    array_push($params, $like, $like);
}
if ($kategori !== '') {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
}
$sql .= " ORDER BY nama ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$barangList = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Stok / Barang</h2>
    <a href="<?= url('pages/barang/tambah.php') ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Barang
    </a>
</div>

<!-- Filter & Search -->
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama/kode..." 
           class="flex-1 min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    <select name="kategori" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Semua Kategori</option>
        <option value="produk" <?= $kategori == 'produk' ? 'selected' : '' ?>>Produk</option>
        <option value="bahan" <?= $kategori == 'bahan' ? 'selected' : '' ?>>Bahan</option>
        <option value="kemasan" <?= $kategori == 'kemasan' ? 'selected' : '' ?>>Kemasan</option>
        <option value="lainnya" <?= $kategori == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
    </select>
    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        <i class="fa-solid fa-filter"></i> Filter
    </button>
    <a href="<?= url('pages/barang/index.php') ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Reset</a>
</form>

<!-- Tabel -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (count($barangList) > 0): ?>
                <?php foreach ($barangList as $b): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-mono"><?= htmlspecialchars($b['kode']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($b['nama']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full 
                            <?= $b['kategori'] == 'produk' ? 'bg-green-100 text-green-800' : 
                                ($b['kategori'] == 'bahan' ? 'bg-yellow-100 text-yellow-800' : 
                                ($b['kategori'] == 'kemasan' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) ?>">
                            <?= htmlspecialchars($b['kategori']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($b['satuan']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right"><?= number_format($b['harga_beli'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right"><?= number_format($b['harga_jual'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold <?= $b['stok'] < 5 ? 'text-red-600' : '' ?>">
                        <?= $b['stok'] ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="<?= url('pages/barang/stok_masuk.php?id=' . $b['id']) ?>" class="text-green-600 hover:text-green-900 mr-2" title="Stok Masuk">
                            <i class="fa-solid fa-arrow-down"></i>
                        </a>
                        <a href="<?= url('pages/barang/stok_keluar.php?id=' . $b['id']) ?>" class="text-orange-600 hover:text-orange-900 mr-2" title="Stok Keluar">
                            <i class="fa-solid fa-arrow-up"></i>
                        </a>
                        <a href="<?= url('pages/barang/edit.php?id=' . $b['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="<?= url('pages/barang/hapus.php?id=' . $b['id']) ?>" onclick="return confirm('Yakin hapus barang ini?')" class="text-red-600 hover:text-red-900">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data barang.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>