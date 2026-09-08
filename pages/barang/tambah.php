<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$errors = [];
$kode = $nama = $satuan = '';
$kategori = 'produk';
$harga_beli = $harga_jual = 0;
$stok = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $kategori = $_POST['kategori'];
    $satuan = trim($_POST['satuan']);
    $harga_beli = (float)$_POST['harga_beli'];
    $harga_jual = (float)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];

    if ($kode === '' || $nama === '') {
        $errors[] = 'Kode dan nama wajib diisi.';
    }
    // Cek kode unik
    $stmt = $pdo->prepare("SELECT id FROM barang WHERE kode = ?");
    $stmt->execute([$kode]);
    if ($stmt->fetch()) {
        $errors[] = 'Kode barang sudah digunakan.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO barang (kode, nama, kategori, satuan, harga_beli, harga_jual, stok) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$kode, $nama, $kategori, $satuan, $harga_beli, $harga_jual, $stok]);
        header('Location: ' . url('pages/barang/index.php?msg=success'));
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Tambah Barang</h2>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white shadow rounded-lg p-6 max-w-2xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang</label>
            <input type="text" name="kode" value="<?= htmlspecialchars($kode) ?>" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select name="kategori" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="produk" <?= $kategori == 'produk' ? 'selected' : '' ?>>Produk</option>
                <option value="bahan" <?= $kategori == 'bahan' ? 'selected' : '' ?>>Bahan</option>
                <option value="kemasan" <?= $kategori == 'kemasan' ? 'selected' : '' ?>>Kemasan</option>
                <option value="lainnya" <?= $kategori == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
            <input type="text" name="satuan" value="<?= htmlspecialchars($satuan) ?>" placeholder="pcs, botol, pack..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
            <input type="number" name="harga_beli" value="<?= $harga_beli ?>" step="0.01" min="0"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
            <input type="number" name="harga_jual" value="<?= $harga_jual ?>" step="0.01" min="0"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
            <input type="number" name="stok" value="<?= $stok ?>" min="0"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    <div class="mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
            <i class="fa-solid fa-save mr-2"></i> Simpan
        </button>
        <a href="<?= url('pages/barang/index.php') ?>" class="ml-2 text-gray-600 hover:text-gray-800">Batal</a>
    </div>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>