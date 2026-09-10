<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT id, nama, no_hp, alamat FROM pelanggan";
$params = [];
if ($q !== '') {
    $sql .= " WHERE nama LIKE ? OR no_hp LIKE ?";
    $like = "%$q%";
    $params = [$like, $like];
}
$sql .= " ORDER BY nama ASC LIMIT 15";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);