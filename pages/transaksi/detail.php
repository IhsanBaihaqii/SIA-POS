<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . url('pages/transaksi/index.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT t.*, p.nama AS nama_pelanggan, p.no_hp, p.alamat, u.nama AS nama_user
                       FROM transaksi t
                       LEFT JOIN pelanggan p ON t.pelanggan_id = p.id
                       LEFT JOIN users u ON t.user_id = u.id
                       WHERE t.id = ?");
$stmt->execute([$id]);
$transaksi = $stmt->fetch();
if (!$transaksi) {
    header('Location: ' . url('pages/transaksi/index.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT dt.*, b.nama AS nama_barang, b.kode, b.satuan 
                       FROM detail_transaksi dt 
                       JOIN barang b ON dt.barang_id = b.id 
                       WHERE dt.transaksi_id = ?");
$stmt->execute([$id]);
$details = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Detail Transaksi</h2>
    <a href="<?= url('pages/transaksi/index.php') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php
        if ($_GET['msg'] == 'created') echo 'Transaksi berhasil dibuat.';
        elseif ($_GET['msg'] == 'paid') echo 'Pembayaran berhasil diproses.';
        ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Info Transaksi -->
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Transaksi</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">No Transaksi:</span> <span class="font-mono font-semibold"><?= htmlspecialchars($transaksi['no_transaksi']) ?></span></div>
                <div><span class="text-gray-500">Tanggal:</span> <?= date('d/m/Y', strtotime($transaksi['tanggal'])) ?></div>
                <div><span class="text-gray-500">Pelanggan:</span> <?= htmlspecialchars($transaksi['nama_pelanggan'] ?? 'Umum') ?></div>
                <div><span class="text-gray-500">Kasir:</span> <?= htmlspecialchars($transaksi['nama_user'] ?? '-') ?></div>
                <?php if ($transaksi['no_hp']): ?>
                    <div><span class="text-gray-500">No HP:</span> <?= htmlspecialchars($transaksi['no_hp']) ?></div>
                <?php endif; ?>
                <?php if ($transaksi['alamat']): ?>
                    <div class="col-span-2"><span class="text-gray-500">Alamat:</span> <?= htmlspecialchars($transaksi['alamat']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Item -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($details as $d): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium"><?= htmlspecialchars($d['nama_barang']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($d['kode']) ?></div>
                        </td>
                        <td class="px-4 py-3 text-right">Rp <?= number_format($d['harga_jual'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3 text-center"><?= $d['jumlah'] ?> <?= htmlspecialchars($d['satuan']) ?></td>
                        <td class="px-4 py-3 text-right font-semibold">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-bold">Total</td>
                        <td class="px-4 py-3 text-right font-bold text-lg text-blue-600">Rp <?= number_format($transaksi['total'], 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Panel Pembayaran -->
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Status Pembayaran</h3>
            <?php
            $badge = [
                'menunggu' => 'bg-yellow-100 text-yellow-800',
                'lunas' => 'bg-green-100 text-green-800',
                'sebagian' => 'bg-blue-100 text-blue-800',
                'batal' => 'bg-red-100 text-red-800',
            ];
            ?>
            <div class="mb-3">
                <span class="px-3 py-1 rounded-full text-sm font-semibold <?= $badge[$transaksi['status_pembayaran']] ?? 'bg-gray-100' ?>">
                    <?= ucfirst($transaksi['status_pembayaran']) ?>
                </span>
            </div>

            <?php if ($transaksi['jumlah_bayar'] !== null): ?>
                <div class="text-sm space-y-1 mt-3 border-t pt-3">
                    <div class="flex justify-between"><span class="text-gray-500">Jumlah Bayar</span> <span>Rp <?= number_format($transaksi['jumlah_bayar'], 0, ',', '.') ?></span></div>
                    <?php if ($transaksi['kembalian'] !== null && $transaksi['kembalian'] > 0): ?>
                        <div class="flex justify-between text-green-600 font-semibold"><span>Kembalian</span> <span>Rp <?= number_format($transaksi['kembalian'], 0, ',', '.') ?></span></div>
                    <?php endif; ?>
                    <?php if ($transaksi['tanggal_pembayaran']): ?>
                        <div class="flex justify-between"><span class="text-gray-500">Tanggal Bayar</span> <span><?= date('d/m/Y H:i', strtotime($transaksi['tanggal_pembayaran'])) ?></span></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($transaksi['status_pembayaran'] == 'menunggu' || $transaksi['status_pembayaran'] == 'sebagian'): ?>
        <div class="bg-white rounded-lg shadow p-6" id="pembayaran">
            <h3 class="font-semibold text-gray-800 mb-4">Proses Pembayaran</h3>
            <form action="<?= url('pages/transaksi/bayar.php') ?>" method="POST" id="formBayar">
                <input type="hidden" name="transaksi_id" value="<?= $transaksi['id'] ?>">
                <input type="hidden" name="total" value="<?= $transaksi['total'] ?>">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Uang Diterima</label>
                    <input type="number" name="jumlah_bayar" id="jumlah_bayar" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Total Tagihan</span> <span class="font-semibold">Rp <?= number_format($transaksi['total'], 0, ',', '.') ?></span></div>
                    <div class="flex justify-between mt-1"><span class="text-gray-500">Kembalian</span> <span id="previewKembalian" class="font-semibold text-green-600">Rp 0</span></div>
                    <div class="flex justify-between mt-1"><span class="text-gray-500">Kurang Bayar</span> <span id="previewKurang" class="font-semibold text-red-600">Rp 0</span></div>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                    <i class="fa-solid fa-money-bill-wave mr-2"></i> Proses Pembayaran
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const total = <?= (float)$transaksi['total'] ?>;
const inputBayar = document.getElementById('jumlah_bayar');
if (inputBayar) {
    inputBayar.addEventListener('input', () => {
        const bayar = parseFloat(inputBayar.value) || 0;
        const kembalian = bayar - total;
        const elKembalian = document.getElementById('previewKembalian');
        const elKurang = document.getElementById('previewKurang');
        if (kembalian >= 0) {
            elKembalian.textContent = 'Rp ' + kembalian.toLocaleString('id-ID');
            elKurang.textContent = 'Rp 0';
        } else {
            elKembalian.textContent = 'Rp 0';
            elKurang.textContent = 'Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
        }
    });
}

// Konfirmasi jika uang kurang dari total
const formBayar = document.getElementById('formBayar');
if (formBayar) {
    formBayar.addEventListener('submit', (e) => {
        const bayar = parseFloat(inputBayar.value) || 0;
        if (bayar < total) {
            if (!confirm('Uang diterima kurang dari total tagihan. Status akan menjadi "Sebagian". Lanjutkan?')) {
                e.preventDefault();
            }
        }
    });
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>