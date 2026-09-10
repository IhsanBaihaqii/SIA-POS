<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('pages/transaksi/buat.php'));
    exit;
}

$no_transaksi = trim($_POST['no_transaksi']);
$tanggal      = $_POST['tanggal'];
$pelanggan_id = !empty($_POST['pelanggan_id']) ? (int)$_POST['pelanggan_id'] : null;
$keterangan   = trim($_POST['keterangan'] ?? '');
$items        = $_POST['items'] ?? [];

if (empty($items)) {
    header('Location: ' . url('pages/transaksi/buat.php?err=empty'));
    exit;
}

// ============================================================
// 1. Validasi & ambil harga resmi dari database (anti manipulasi)
// ============================================================
$itemsValidated = [];
$total = 0;

foreach ($items as $it) {
    $barang_id = (int)$it['barang_id'];
    $jumlah    = (int)$it['jumlah'];

    if ($jumlah < 1) {
        die('Jumlah barang tidak valid.');
    }

    $stmt = $pdo->prepare("SELECT id, nama, harga_beli, harga_jual, stok FROM barang WHERE id = ?");
    $stmt->execute([$barang_id]);
    $b = $stmt->fetch();

    if (!$b) {
        die('Barang tidak ditemukan (ID: ' . $barang_id . ').');
    }
    if ($b['stok'] < $jumlah) {
        die('Stok tidak cukup untuk barang: ' . $b['nama'] . ' (tersedia: ' . $b['stok'] . ').');
    }

    $harga_jual = (float)$b['harga_jual'];
    $harga_beli = (float)$b['harga_beli'];
    $subtotal   = $harga_jual * $jumlah;

    $itemsValidated[] = [
        'barang_id'  => $b['id'],
        'jumlah'     => $jumlah,
        'harga_jual' => $harga_jual,
        'harga_beli' => $harga_beli,
        'subtotal'   => $subtotal,
    ];

    $total += $subtotal;
}

// ============================================================
// 2. Pastikan no_transaksi unik
// ============================================================
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transaksi WHERE no_transaksi = ?");
$stmt->execute([$no_transaksi]);
if ($stmt->fetchColumn() > 0) {
    $no_transaksi .= '-' . rand(100, 999);
}

// ============================================================
// 3. Simpan transaksi
// ============================================================
try {
    $pdo->beginTransaction();

    // Insert header transaksi
    $stmt = $pdo->prepare("INSERT INTO transaksi 
        (no_transaksi, tanggal, pelanggan_id, user_id, total, status_pembayaran, keterangan) 
        VALUES (?, ?, ?, ?, ?, 'menunggu', ?)");
    $stmt->execute([
        $no_transaksi,
        $tanggal,
        $pelanggan_id,
        $_SESSION['user_id'],
        $total,
        $keterangan
    ]);
    $transaksi_id = $pdo->lastInsertId();

    // Insert detail + update stok + catat mutasi
    $stmtDetail = $pdo->prepare("INSERT INTO detail_transaksi 
        (transaksi_id, barang_id, jumlah, harga_beli, harga_jual, subtotal) 
        VALUES (?, ?, ?, ?, ?, ?)");

    $stmtStok = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");

    $stmtMutasi = $pdo->prepare("INSERT INTO stok_mutasi 
        (barang_id, jenis, jumlah, keterangan, tanggal, user_id) 
        VALUES (?, 'keluar', ?, ?, ?, ?)");

    foreach ($itemsValidated as $it) {
        $stmtDetail->execute([
            $transaksi_id,
            $it['barang_id'],
            $it['jumlah'],
            $it['harga_beli'],
            $it['harga_jual'],
            $it['subtotal']
        ]);

        $stmtStok->execute([$it['jumlah'], $it['barang_id']]);

        $stmtMutasi->execute([
            $it['barang_id'],
            $it['jumlah'],
            'Penjualan ' . $no_transaksi,
            $tanggal,
            $_SESSION['user_id']
        ]);
    }

    $pdo->commit();
    header('Location: ' . url('pages/transaksi/detail.php?id=' . $transaksi_id . '&msg=created'));
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die('Gagal menyimpan transaksi: ' . $e->getMessage());
}