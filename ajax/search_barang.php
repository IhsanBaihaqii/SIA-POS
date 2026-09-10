<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT id, kode, nama, satuan, harga_beli, harga_jual, stok FROM barang WHERE stok > 0";
$params = [];
if ($q !== '') {
    $sql .= " AND (nama LIKE ? OR kode LIKE ?)";
    $like = "%$q%";
    $params = [$like, $like];
}
$sql .= " ORDER BY nama ASC LIMIT 15";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);