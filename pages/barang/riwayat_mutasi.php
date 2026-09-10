<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$barang_id = isset($_GET['barang_id']) ? (int)$_GET['barang_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT sm.*, b.nama AS nama_barang, u.nama AS nama_user
        FROM stok_mutasi sm
        LEFT JOIN barang b ON sm.barang_id = b.id
        LEFT JOIN users u ON sm.user_id = u.id
        WHERE 1=1";
$params = [];
if ($barang_id > 0) {
    $sql .= " AND sm.barang_id = ?";
    $params[] = $barang_id;
}
if ($search !== '') {
    $sql .= " AND (b.nama LIKE ? OR sm.keterangan LIKE ?)";
    $like = "%$search%";
    array_push($params, $like, $like);
}
$sql .= " ORDER BY sm.tanggal DESC, sm.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mutasiList = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Riwayat Mutasi Stok</h2>
    <a href="<?= url('pages/barang/index.php') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Daftar Barang
    </a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <select name="barang_id" class="px-3 py-2 border border-gray-300 rounded-lg">
        <option value="">Semua Barang</option>
        <?php
        $stmtBarang = $pdo->query("SELECT id, nama FROM barang ORDER BY nama");
        while ($b = $stmtBarang->fetch()) {
            $selected = ($b['id'] == $barang_id) ? 'selected' : '';
            echo "<option value='{$b['id']}' $selected>" . htmlspecialchars($b['nama']) . "</option>";
        }
        ?>
    </select>
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari keterangan..." 
           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg">
    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        <i class="fa-solid fa-search"></i> Filter
    </button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jenis</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (count($mutasiList) > 0): ?>
                <?php foreach ($mutasiList as $m): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($m['tanggal'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($m['nama_barang']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <?php if ($m['jenis'] == 'masuk'): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Masuk</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Keluar</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold">
                        <?= $m['jenis'] == 'masuk' ? '+' : '-' ?><?= $m['jumlah'] ?>
                    </td>
                    <td class="px-6 py-4"><?= htmlspecialchars($m['keterangan']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($m['nama_user'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data mutasi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>