<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$errors = [];
$nama = $no_hp = $alamat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);

    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO pelanggan (nama, no_hp, alamat) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $no_hp, $alamat]);
        header('Location: ' . url('pages/pelanggan/index.php?msg=success'));
        exit;
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Tambah Pelanggan</h2>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
        <input type="text" name="no_hp" value="<?= htmlspecialchars($no_hp) ?>"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
        <textarea name="alamat" rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($alamat) ?></textarea>
    </div>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-save mr-2"></i> Simpan
    </button>
    <a href="<?= url('pages/pelanggan/index.php') ?>" class="ml-2 text-gray-600 hover:text-gray-800">Batal</a>
</form>

<?php include __DIR__ . '/../../includes/footer.php'; ?>