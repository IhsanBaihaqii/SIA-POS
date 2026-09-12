<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan</h2>
    <p class="text-sm text-gray-500 mt-1">Pilih jenis laporan yang ingin ditampilkan atau diekspor.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Laba / Rugi -->
    <a href="<?= url('pages/laporan/laba_rugi.php') ?>" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-blue-500">
        <div class="flex items-center mb-3">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
                <i class="fa-solid fa-chart-line text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Laba / Rugi</h3>
        </div>
        <p class="text-sm text-gray-500">Ringkasan pendapatan, HPP, beban operasional, dan laba/rugi bersih per periode.</p>
    </a>

    <!-- Pendapatan -->
    <a href="<?= url('pages/laporan/pendapatan.php') ?>" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-green-500">
        <div class="flex items-center mb-3">
            <div class="bg-green-100 rounded-full p-3 mr-4">
                <i class="fa-solid fa-wallet text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Pendapatan</h3>
        </div>
        <p class="text-sm text-gray-500">Detail transaksi lunas beserta total pendapatan dalam rentang tanggal tertentu.</p>
    </a>

    <!-- Stok -->
    <a href="<?= url('pages/laporan/stok.php') ?>" 
       class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-yellow-500">
        <div class="flex items-center mb-3">
            <div class="bg-yellow-100 rounded-full p-3 mr-4">
                <i class="fa-solid fa-boxes-stacked text-yellow-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Stok Barang</h3>
        </div>
        <p class="text-sm text-gray-500">Nilai persediaan, stok minimum, dan ringkasan mutasi stok masuk/keluar.</p>
    </a>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>