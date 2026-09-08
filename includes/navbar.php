<?php
// includes/navbar.php
$user = currentUser();
?>
<!-- Navbar -->
<header class="bg-white shadow-sm px-6 py-3 flex justify-between items-center">
    <div>
        <h1 class="text-lg font-semibold text-gray-800">
            <?php
                $pageTitle = basename($_SERVER['PHP_SELF'], '.php');
                echo ucwords(str_replace('_', ' ', $pageTitle));
            ?>
        </h1>
    </div>
    <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600 hidden sm:block">
            <?= date('d M Y') ?>
        </span>
        <a href="../logout.php" class="text-gray-500 hover:text-red-500">
            <i class="fa-solid fa-right-from-bracket text-xl"></i>
        </a>
    </div>
</header>