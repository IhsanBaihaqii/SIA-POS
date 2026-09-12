<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . url('pages/beban/index.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM beban WHERE id = ?");
$stmt->execute([$id]);
$beban = $stmt->fetch();
if (!$beban) {
    header('Location: ' . url('pages/beban/index.php'));
    exit;
}

$errors = [];
$nama_beban = $beban['nama_beban'];
$jumlah     = $beban['jumlah'];
$tanggal    = $beban['tanggal'];
$keterangan = $beban['keterangan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_beban = trim($_POST['nama_beban']);
    $jumlah     = (float)$_POST['jumlah'];
    $tanggal    = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($nama_beban === '') {
        $errors[] = 'Nama beban wajib diisi.';
    }
    if ($jumlah <= 0) {
        $errors[] = 'Jumlah harus lebih dari 0.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE beban SET nama_beban = ?, jumlah = ?, tanggal = ?, keterangan = ? WHERE id = ?");
        $stmt->execute([$nama_beban, $jumlah, $tanggal, $keterangan, $id]);
        header('Location: ' . url('pages/beban/index.php?msg=updated'));
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Edit Beban</h2>
    <a href="<?= url('pages/beban/index.php') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Beban</label>
        <input type="text" name="nama_beban" value="<?= htmlspecialchars($nama_beban) ?>" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
        <input type="number" name="jumlah" value="<?= htmlspecialchars($jumlah) ?>" step="0.01" min="0" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
        <textarea name="keterangan" rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($keterangan) ?></textarea>
    </div>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-save mr-2"></i> Update
    </button>
    <a href="<?= url('pages/beban/index.php') ?>" class="ml-2 text-gray-600 hover:text-gray-800">Batal</a>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>