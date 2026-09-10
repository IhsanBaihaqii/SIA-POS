<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

$sql = "SELECT t.*, p.nama AS nama_pelanggan, u.nama AS nama_user
        FROM transaksi t
        LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (t.no_transaksi LIKE ? OR p.nama LIKE ?)";
    $like = "%$search%";
    array_push($params, $like, $like);
}
if ($status !== '') {
    $sql .= " AND t.status_pembayaran = ?";
    $params[] = $status;
}
if ($tgl_mulai && $tgl_selesai) {
    $sql .= " AND t.tanggal BETWEEN ? AND ?";
    array_push($params, $tgl_mulai, $tgl_selesai);
}
$sql .= " ORDER BY t.tanggal DESC, t.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transaksiList = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Daftar Transaksi</h2>
    <a href="<?= url('pages/transaksi/buat.php') ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-plus mr-2"></i> Buat Transaksi
    </a>
</div>

<form method="GET" class="mb-4 bg-white p-4 rounded-lg shadow flex flex-wrap gap-2 items-end">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Cari</label>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="No transaksi / pelanggan"
               class="px-3 py-2 border border-gray-300 rounded-lg">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg">
            <option value="">Semua</option>
            <option value="menunggu" <?= $status == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
            <option value="lunas" <?= $status == 'lunas' ? 'selected' : '' ?>>Lunas</option>
            <option value="sebagian" <?= $status == 'sebagian' ? 'selected' : '' ?>>Sebagian</option>
            <option value="batal" <?= $status == 'batal' ? 'selected' : '' ?>>Batal</option>
        </select>
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
    <a href="<?= url('pages/transaksi/index.php') ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">Reset</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No Transaksi</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (count($transaksiList) > 0): ?>
                <?php foreach ($transaksiList as $t): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap font-mono text-sm"><?= htmlspecialchars($t['no_transaksi']) ?></td>
                    <td class="px-4 py-3 whitespace-nowrap"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($t['nama_pelanggan'] ?? '-') ?></td>
                    <td class="px-4 py-3 whitespace-nowrap text-right font-semibold">Rp <?= number_format($t['total'], 0, ',', '.') ?></td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <?php
                        $badge = [
                            'menunggu' => 'bg-yellow-100 text-yellow-800',
                            'lunas' => 'bg-green-100 text-green-800',
                            'sebagian' => 'bg-blue-100 text-blue-800',
                            'batal' => 'bg-red-100 text-red-800',
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs rounded-full <?= $badge[$t['status_pembayaran']] ?? 'bg-gray-100' ?>">
                            <?= ucfirst($t['status_pembayaran']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right">
                        <a href="<?= url('pages/transaksi/detail.php?id=' . $t['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-2" title="Detail">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <?php if ($t['status_pembayaran'] == 'menunggu' || $t['status_pembayaran'] == 'sebagian'): ?>
                            <a href="<?= url('pages/transaksi/detail.php?id=' . $t['id'] . '#pembayaran') ?>" class="text-green-600 hover:text-green-900 mr-2" title="Bayar">
                                <i class="fa-solid fa-money-bill"></i>
                            </a>
                        <?php endif; ?>
                        <a href="<?= url('pages/transaksi/hapus.php?id=' . $t['id']) ?>" onclick="return confirm('Yakin hapus transaksi ini? Stok akan dikembalikan.')" class="text-red-600 hover:text-red-900" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada data transaksi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>