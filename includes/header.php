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
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Style -->
    <style>
        .sidebar-link {
            @apply flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition;
        }
        .sidebar-link.active {
            @apply bg-blue-50 text-blue-700 font-semibold;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar akan di-include di sini -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include 'navbar.php'; ?>
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">