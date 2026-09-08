<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM pelanggan";
$params = [];
if ($search !== '') {
    $sql .= " WHERE nama LIKE ? OR no_hp LIKE ? OR alamat LIKE ?";
    $like = "%$search%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY nama ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pelangganList = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Data Pelanggan</h2>
    <a href="<?= url('pages/pelanggan/tambah.php') ?>" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Pelanggan
    </a>
</div>

<!-- Search -->
<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
           placeholder="Cari nama, no HP, alamat..." 
           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        <i class="fa-solid fa-search"></i> Cari
    </button>
    <?php if ($search): ?>
        <a href="<?= url('pages/pelanggan/index.php') ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
            Reset
        </a>
    <?php endif; ?>
</form>

<!-- Tabel -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No HP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (count($pelangganList) > 0): ?>
                <?php foreach ($pelangganList as $p): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($p['nama']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($p['no_hp']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($p['alamat']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="<?= url('pages/pelanggan/edit.php?id=' . $p['id']) ?>" 
                           class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="<?= url('pages/pelanggan/hapus.php?id=' . $p['id']) ?>" 
                           onclick="return confirm('Yakin hapus pelanggan ini?')"
                           class="text-red-600 hover:text-red-900">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data pelanggan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>