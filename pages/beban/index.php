<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$search      = isset($_GET['search']) ? trim($_GET['search']) : '';
$tgl_mulai   = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

$sql = "SELECT b.*, u.nama AS nama_user 
        FROM beban b 
        LEFT JOIN users u ON b.user_id = u.id 
        WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (b.nama_beban LIKE ? OR b.keterangan LIKE ?)";
    $like = "%$search%";
    array_push($params, $like, $like);
}
if ($tgl_mulai && $tgl_selesai) {
    $sql .= " AND b.tanggal BETWEEN ? AND ?";
    array_push($params, $tgl_mulai, $tgl_selesai);
}
$sql .= " ORDER BY b.tanggal DESC, b.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bebanList = $stmt->fetchAll();

// Hitung total beban sesuai filter
$totalBeban = 0;
foreach ($bebanList as $b) {
    $totalBeban += $b['jumlah'];
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Beban Operasional</h2>
    <a href="<?= url('pages/beban/tambah.php') ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-plus mr-2"></i> Tambah Beban
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php
        if ($_GET['msg'] == 'success') echo 'Beban berhasil ditambahkan.';
        elseif ($_GET['msg'] == 'updated') echo 'Beban berhasil diperbarui.';
        elseif ($_GET['msg'] == 'deleted') echo 'Beban berhasil dihapus.';
        ?>
    </div>
<?php endif; ?>

<form method="GET" class="mb-4 bg-white p-4 rounded-lg shadow flex flex-wrap gap-2 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Cari</label>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nama / keterangan"
               class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
        <input type="date" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
        <input type="date" name="tgl_selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
        <i class="fa-solid fa-filter"></i> Filter
    </button>
    <a href="<?= url('pages/beban/index.php') ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Reset</a>
</form>

<!-- Summary Card -->
<div class="bg-white rounded-lg shadow p-4 mb-4 flex items-center justify-between">
    <div class="flex items-center">
        <div class="bg-red-100 rounded-full p-3 mr-4">
            <i class="fa-solid fa-file-invoice-dollar text-red-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Beban (sesuai filter)</p>
            <p class="text-2xl font-bold text-red-600">Rp <?= number_format($totalBeban, 0, ',', '.') ?></p>
        </div>
    </div>
    <div class="text-sm text-gray-500"><?= count($bebanList) ?> data</div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Beban</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (count($bebanList) > 0): ?>
                <?php foreach ($bebanList as $b): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($b['tanggal'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium"><?= htmlspecialchars($b['nama_beban']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($b['keterangan'] ?? '-') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($b['nama_user'] ?? '-') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-red-600">
                        Rp <?= number_format($b['jumlah'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="<?= url('pages/beban/edit.php?id=' . $b['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <a href="<?= url('pages/beban/hapus.php?id=' . $b['id']) ?>" 
                           onclick="return confirm('Yakin hapus beban ini?')"
                           class="text-red-600 hover:text-red-900" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data beban.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>