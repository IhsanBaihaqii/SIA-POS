<?php
// includes/header.php
require_once __DIR__ . '/../config/auth.php';
requireLogin();
$currentUser = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIA-POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            color: #374151;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }
        .sidebar-link:hover {
            background-color: #f3f4f6;
        }
        .sidebar-link.active {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include __DIR__ . '/navbar.php'; ?>
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">