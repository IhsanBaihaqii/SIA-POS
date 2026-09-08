<?php
// includes/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
$user = currentUser();
?>
<!-- Sidebar - Redesain dengan warna solid -->
<aside class="w-64 bg-slate-800 flex flex-col h-screen sticky top-0 shadow-xl">
    <!-- Brand area - solid background -->
    <div class="p-4 border-b border-slate-700/50 flex items-center space-x-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
            <i class="fa-solid fa-calculator text-white text-xl"></i>
        </div>
        <span class="text-xl font-bold text-white tracking-wide">SIA-POS</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
        <a href="../dashboard.php"
        class="sidebar-link group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
        <?= $currentPage == 'dashboard.php'
        ? 'bg-blue-600 text-white'
        : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
        <i class="fa-solid fa-gauge w-5 text-center text-base <?= $currentPage == 'dashboard.php' ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
        <span class="ml-3">Dashboard</span>
    </a>

    <a href="../pelanggan/index.php"
    class="sidebar-link group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
    <?= strpos($_SERVER['REQUEST_URI'], '/pelanggan/') !== false
    ? 'bg-blue-600 text-white'
    : 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
    <i class="fa-solid fa-users w-5 text-center text-base <?= strpos($_SERVER['REQUEST_URI'], '/pelanggan/') !== false ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
    <span class="ml-3">Pelanggan</span>
</a>

<a href="../barang/index.php"
class="sidebar-link group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
<?= strpos($_SERVER['REQUEST_URI'], '/barang/') !== false
? 'bg-blue-600 text-white'
: 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
<i class="fa-solid fa-boxes-stacked w-5 text-center text-base <?= strpos($_SERVER['REQUEST_URI'], '/barang/') !== false ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
<span class="ml-3">Stok / Barang</span>
</a>

<a href="../transaksi/index.php"
class="sidebar-link group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
<?= strpos($_SERVER['REQUEST_URI'], '/transaksi/') !== false
? 'bg-emerald-600 text-white'
: 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
<i class="fa-solid fa-cash-register w-5 text-center text-base <?= strpos($_SERVER['REQUEST_URI'], '/transaksi/') !== false ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
<span class="ml-3">Transaksi</span>
</a>

<a href="../beban/index.php"
class="sidebar-link group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
<?= strpos($_SERVER['REQUEST_URI'], '/beban/') !== false
? 'bg-amber-600 text-white'
: 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
<i class="fa-solid fa-file-invoice-dollar w-5 text-center text-base <?= strpos($_SERVER['REQUEST_URI'], '/beban/') !== false ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
<span class="ml-3">Beban</span>
</a>

<a href="../laporan/index.php"
class="sidebar-link group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
<?= strpos($_SERVER['REQUEST_URI'], '/laporan/') !== false
? 'bg-purple-600 text-white'
: 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
<i class="fa-solid fa-chart-line w-5 text-center text-base <?= strpos($_SERVER['REQUEST_URI'], '/laporan/') !== false ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
<span class="ml-3">Laporan</span>
</a>

<?php if ($user['role'] === 'admin'): ?>
<a href="../user/index.php"
class="sidebar-link group flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
<?= strpos($_SERVER['REQUEST_URI'], '/user/') !== false
? 'bg-rose-600 text-white'
: 'text-slate-300 hover:bg-slate-700 hover:text-white' ?>">
<i class="fa-solid fa-user-gear w-5 text-center text-base <?= strpos($_SERVER['REQUEST_URI'], '/user/') !== false ? 'text-white' : 'text-slate-400 group-hover:text-white' ?>"></i>
<span class="ml-3">Manajemen User</span>
</a>
<?php endif; ?>
</nav>

<!-- User profile - solid border -->
<div class="p-4 border-t border-slate-700/50">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
            <?= strtoupper(substr($user['nama'], 0, 1)) ?>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($user['nama']) ?></p>
            <p class="text-xs text-slate-400 capitalize"><?= htmlspecialchars($user['role']) ?></p>
        </div>
        <i class="fa-solid fa-chevron-right text-slate-500 text-xs"></i>
    </div>
</div>
</aside>