<?php
// pages/dashboard.php
require_once __DIR__ . '/../config/auth.php';
requireLogin();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$totalPendapatan = getTotalPendapatan($pdo);
$totalBeban = getTotalBeban($pdo);
$totalHPP = getTotalHPP($pdo);
$labaRugi = $totalPendapatan - $totalHPP - $totalBeban;

$jumlahPelanggan = getCount($pdo, 'pelanggan');
$jumlahBarang = getCount($pdo, 'barang');
$jumlahTransaksi = getCount($pdo, 'transaksi');
$jumlahBeban = getCount($pdo, 'beban');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIA-POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include '../includes/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include '../includes/navbar.php'; ?>
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                <!-- Dashboard Content -->
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

                <!-- Statistik Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <i class="fa-solid fa-wallet text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pendapatan</p>
                                <p class="text-xl font-bold">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-red-100 rounded-full p-3 mr-4">
                                <i class="fa-solid fa-file-invoice-dollar text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Beban</p>
                                <p class="text-xl font-bold">Rp <?= number_format($totalBeban, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-3 mr-4">
                                <i class="fa-solid fa-chart-line text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Laba/Rugi</p>
                                <p class="text-xl font-bold <?= $labaRugi >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    Rp <?= number_format($labaRugi, 0, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-3 mr-4">
                                <i class="fa-solid fa-box text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">HPP</p>
                                <p class="text-xl font-bold">Rp <?= number_format($totalHPP, 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow p-6 flex items-center">
                        <i class="fa-solid fa-users text-3xl text-blue-500 mr-4"></i>
                        <div>
                            <p class="text-gray-500">Pelanggan</p>
                            <p class="text-2xl font-bold"><?= $jumlahPelanggan ?></p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 flex items-center">
                        <i class="fa-solid fa-boxes-stacked text-3xl text-yellow-500 mr-4"></i>
                        <div>
                            <p class="text-gray-500">Barang</p>
                            <p class="text-2xl font-bold"><?= $jumlahBarang ?></p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6 flex items-center">
                        <i class="fa-solid fa-cash-register text-3xl text-green-500 mr-4"></i>
                        <div>
                            <p class="text-gray-500">Transaksi</p>
                            <p class="text-2xl font-bold"><?= $jumlahTransaksi ?></p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>