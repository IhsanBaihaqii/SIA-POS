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
$jumlah = 0;
$keterangan = '';
$tanggal = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah = (int)$_POST['jumlah'];
    $keterangan = trim($_POST['keterangan']);
    $tanggal = $_POST['tanggal'];
    if ($jumlah <= 0) {
        $errors[] = 'Jumlah harus lebih dari 0.';
    }
    if (empty($errors)) {
        // Update stok barang
        $stokBaru = $barang['stok'] + $jumlah;
        $stmt = $pdo->prepare("UPDATE barang SET stok = ? WHERE id = ?");
        $stmt->execute([$stokBaru, $id]);
        // Catat mutasi
        $stmt = $pdo->prepare("INSERT INTO stok_mutasi (barang_id, jenis, jumlah, keterangan, tanggal, user_id) VALUES (?, 'masuk', ?, ?, ?, ?)");
        $stmt->execute([$id, $jumlah, $keterangan, $tanggal, $_SESSION['user_id']]);
        header('Location: ' . url('pages/barang/riwayat_mutasi.php?barang_id=' . $id . '&msg=masuk'));
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Stok Masuk - <?= htmlspecialchars($barang['nama']) ?></h2>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Masuk</label>
        <input type="number" name="jumlah" value="<?= $jumlah ?>" min="1" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="<?= $tanggal ?>" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
        <textarea name="keterangan" rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($keterangan) ?></textarea>
    </div>
    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-arrow-down mr-2"></i> Proses Stok Masuk
    </button>
    <a href="<?= url('pages/barang/index.php') ?>" class="ml-2 text-gray-600 hover:text-gray-800">Batal</a>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>