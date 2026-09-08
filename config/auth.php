<?php
// config/auth.php
require_once __DIR__ . '/config.php';
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function hasRole($role) {
    return isLoggedIn() && $_SESSION['role'] === $role;
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['nama'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role']
    ];
}

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
?>