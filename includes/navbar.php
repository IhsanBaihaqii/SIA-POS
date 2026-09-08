<?php
// includes/navbar.php
$currentUser = currentUser();
$pageTitle = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle = ucwords(str_replace('_', ' ', $pageTitle));
?>
<header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center">
    <div>
        <h1 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
    </div>
    <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600 hidden sm:block"><?= date('d M Y') ?></span>
        <a href="<?= url('logout.php') ?>" class="text-gray-500 hover:text-red-500">
            <i class="fa-solid fa-right-from-bracket text-xl"></i>
        </a>
    </div>
</header>