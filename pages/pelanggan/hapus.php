<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: ' . url('pages/pelanggan/index.php?msg=deleted'));
exit;