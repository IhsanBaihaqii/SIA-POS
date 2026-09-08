<?php
// includes/functions.php
require_once __DIR__ . '/../config/database.php';

function getTotalPendapatan($pdo, $tanggalMulai = null, $tanggalSelesai = null) {
    $sql = "SELECT COALESCE(SUM(total), 0) AS total FROM transaksi WHERE status_pembayaran = 'lunas'";
    $params = [];
    if ($tanggalMulai && $tanggalSelesai) {
        $sql .= " AND tanggal BETWEEN ? AND ?";
        $params = [$tanggalMulai, $tanggalSelesai];
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function getTotalBeban($pdo, $tanggalMulai = null, $tanggalSelesai = null) {
    $sql = "SELECT COALESCE(SUM(jumlah), 0) AS total FROM beban";
    $params = [];
    if ($tanggalMulai && $tanggalSelesai) {
        $sql .= " WHERE tanggal BETWEEN ? AND ?";
        $params = [$tanggalMulai, $tanggalSelesai];
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function getTotalHPP($pdo, $tanggalMulai = null, $tanggalSelesai = null) {
    $sql = "SELECT COALESCE(SUM(dt.harga_beli * dt.jumlah), 0) AS hpp
            FROM detail_transaksi dt
            JOIN transaksi t ON dt.transaksi_id = t.id
            WHERE t.status_pembayaran = 'lunas'";
    $params = [];
    if ($tanggalMulai && $tanggalSelesai) {
        $sql .= " AND t.tanggal BETWEEN ? AND ?";
        $params = [$tanggalMulai, $tanggalSelesai];
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

function getCount($pdo, $table) {
    $allowed = ['pelanggan', 'barang', 'transaksi', 'beban', 'users'];
    if (!in_array($table, $allowed)) return 0;
    $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
    return $stmt->fetchColumn();
}
?>