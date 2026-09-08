<?php
// includes/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
$user = currentUser();
?>
<!-- Sidebar -->
<aside class="w-64 bg-white shadow-md flex flex-col">
    <div class="p-4 border-b flex items-center space-x-2">
        <i class="fa-solid fa-calculator text-2xl text-blue-600"></i>
        <span class="text-xl font-bold text-gray-800">SIA-POS</span>
    </div>
    <nav class="flex-1 p-4 space-y-1">
        <a href="../dashboard.php" class="sidebar-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge mr-3 w-5"></i> Dashboard
        </a>
        <a href="../pelanggan/index.php" class="sidebar-link <?= strpos($_SERVER['REQUEST_URI'], '/pelanggan/') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-users mr-3 w-5"></i> Pelanggan
        </a>
        <a href="../barang/index.php" class="sidebar-link <?= strpos($_SERVER['REQUEST_URI'], '/barang/') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked mr-3 w-5"></i> Stok / Barang
        </a>
        <a href="../transaksi/index.php" class="sidebar-link <?= strpos($_SERVER['REQUEST_URI'], '/transaksi/') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-cash-register mr-3 w-5"></i> Transaksi
        </a>
        <a href="../beban/index.php" class="sidebar-link <?= strpos($_SERVER['REQUEST_URI'], '/beban/') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar mr-3 w-5"></i> Beban
        </a>
        <a href="../laporan/index.php" class="sidebar-link <?= strpos($_SERVER['REQUEST_URI'], '/laporan/') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line mr-3 w-5"></i> Laporan
        </a>
        <?php if ($user['role'] === 'admin'): ?>
        <a href="../user/index.php" class="sidebar-link <?= strpos($_SERVER['REQUEST_URI'], '/user/') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-user-gear mr-3 w-5"></i> Manajemen User
        </a>
        <?php endif; ?>
    </nav>
    <div class="p-4 border-t text-sm text-gray-500">
        <i class="fa-solid fa-circle-user mr-2"></i> <?= htmlspecialchars($user['nama']) ?> (<?= $user['role'] ?>)
    </div>
</aside>