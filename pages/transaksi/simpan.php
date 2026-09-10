<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('pages/transaksi/buat.php'));
    exit;
}

$no_transaksi = trim($_POST['no_transaksi']);
$tanggal = $_POST['tanggal'];
$pelanggan_id = !empty($_POST['pelanggan_id']) ? (int)$_POST['pelanggan_id'] : null;
$keterangan = trim($_POST['keterangan'] ?? '');
$items = $_POST['items'] ?? [];

if (empty($items)) {
    header('Location: ' . url('pages/transaksi/buat.php?err=empty'));
    exit;
}

try {
    $pdo->beginTransaction();

    // Hitung total
    $total = 0;
    foreach ($items as $it) {
        $total += (float)$it['harga_jual'] * (int)$it['jumlah'];
    }

    // Insert transaksi
    $stmt = $pdo->prepare("INSERT INTO transaksi (no_transaksi, tanggal, pelanggan_id, user_id, total, status_pembayaran, keterangan) 
                           VALUES (?, ?, ?, ?, ?, 'menunggu', ?)");
    $stmt->execute([$no_transaksi, $tanggal, $pelanggan_id, $_SESSION['user_id'], $total, $keterangan]);
    $transaksi_id = $pdo->lastInsertId();

    // Insert detail & update stok
    foreach ($items as $it) {
        $barang_id = (int)$it['barang_id'];
        $jumlah = (int)$it['jumlah'];
        $harga_jual = (float)$it['harga_jual'];
        $harga_beli = (float)$it['harga_beli'];
        $subtotal = $harga_jual * $jumlah;

        $stmt = $pdo->prepare("INSERT INTO detail_transaksi (transaksi_id, barang_id, jumlah, harga_beli, harga_jual, subtotal) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$transaksi_id, $barang_id, $jumlah, $harga_beli, $harga_jual, $subtotal]);

        // Update stok
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
        $stmt->execute([$jumlah, $barang_id]);

        // Catat mutasi stok
        $stmt = $pdo->prepare("INSERT INTO stok_mutasi (barang_id, jenis, jumlah, keterangan, tanggal, user_id) 
                               VALUES (?, 'keluar', ?, ?, ?, ?)");
        $stmt->execute([$barang_id, $jumlah, 'Penjualan ' . $no_transaksi, $tanggal, $_SESSION['user_id']]);
    }

    $pdo->commit();
    header('Location: ' . url('pages/transaksi/detail.php?id=' . $transaksi_id . '&msg=created'));
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die('Gagal menyimpan transaksi: ' . $e->getMessage());
}