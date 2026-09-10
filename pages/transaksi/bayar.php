<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('pages/transaksi/index.php'));
    exit;
}

$transaksi_id = (int)$_POST['transaksi_id'];
$total = (float)$_POST['total'];
$jumlah_bayar = (float)$_POST['jumlah_bayar'];

$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id = ?");
$stmt->execute([$transaksi_id]);
$transaksi = $stmt->fetch();
if (!$transaksi) {
    header('Location: ' . url('pages/transaksi/index.php'));
    exit;
}

if ($jumlah_bayar >= $total) {
    $status = 'lunas';
    $kembalian = $jumlah_bayar - $total;
    $tanggal_pembayaran = date('Y-m-d H:i:s');
} else {
    $status = 'sebagian';
    $kembalian = 0;
    $tanggal_pembayaran = null;
}

$stmt = $pdo->prepare("UPDATE transaksi SET status_pembayaran = ?, jumlah_bayar = ?, kembalian = ?, tanggal_pembayaran = ? WHERE id = ?");
$stmt->execute([$status, $jumlah_bayar, $kembalian, $tanggal_pembayaran, $transaksi_id]);

header('Location: ' . url('pages/transaksi/detail.php?id=' . $transaksi_id . '&msg=paid'));
exit;