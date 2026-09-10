<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . url('pages/transaksi/index.php'));
    exit;
}

try {
    $pdo->beginTransaction();

    // Ambil detail untuk mengembalikan stok
    $stmt = $pdo->prepare("SELECT barang_id, jumlah FROM detail_transaksi WHERE transaksi_id = ?");
    $stmt->execute([$id]);
    $details = $stmt->fetchAll();

    foreach ($details as $d) {
        // Kembalikan stok
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
        $stmt->execute([$d['jumlah'], $d['barang_id']]);

        // Catat mutasi masuk (pembatalan)
        $stmt = $pdo->prepare("INSERT INTO stok_mutasi (barang_id, jenis, jumlah, keterangan, tanggal, user_id) 
                               VALUES (?, 'masuk', ?, ?, CURDATE(), ?)");
        $stmt->execute([$d['barang_id'], $d['jumlah'], 'Pembatalan transaksi #' . $id, $_SESSION['user_id']]);
    }

    // Hapus transaksi (detail otomatis terhapus karena FK ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('Gagal menghapus transaksi: ' . $e->getMessage());
}

header('Location: ' . url('pages/transaksi/index.php?msg=deleted'));
exit;