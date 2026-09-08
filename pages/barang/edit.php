<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . url('pages/barang/index.php'));
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->execute([$id]);
$barang = $stmt->fetch();
if (!$barang) {
    header('Location: ' . url('pages/barang/index.php'));
    exit;
}

$errors = [];
$kode = $barang['kode'];
$nama = $barang['nama'];
$kategori = $barang['kategori'];
$satuan = $barang['satuan'];
$harga_beli = $barang['harga_beli'];
$harga_jual = $barang['harga_jual'];
$stok = $barang['stok'];

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
    // Cek kode unik selain id ini
    $stmt = $pdo->prepare("SELECT id FROM barang WHERE kode = ? AND id != ?");
    $stmt->execute([$kode, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Kode barang sudah digunakan.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE barang SET kode=?, nama=?, kategori=?, satuan=?, harga_beli=?, harga_jual=?, stok=? WHERE id=?");
        $stmt->execute([$kode, $nama, $kategori, $satuan, $harga_beli, $harga_jual, $stok, $id]);
        header('Location: ' . url('pages/barang/index.php?msg=updated'));
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Edit Barang</h2>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white shadow rounded-lg p-6 max-w-2xl">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- sama seperti tambah, hanya nilai sudah terisi -->
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
            <input type="text" name="satuan" value="<?= htmlspecialchars($satuan) ?>"
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
            <input type="number" name="stok" value="<?= $stok ?>" min="0"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    <div class="mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
            <i class="fa-solid fa-save mr-2"></i> Update
        </button>
        <a href="<?= url('pages/barang/index.php') ?>" class="ml-2 text-gray-600 hover:text-gray-800">Batal</a>
    </div>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>