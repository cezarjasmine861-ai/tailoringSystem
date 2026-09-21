<?php
$pageTitle = $pageTitle ?? 'Dashboard';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Tailoring Record Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a class="brand" href="index.php">
                <span class="brand-mark">TR</span>
                <span>Tailoring Records</span>
            </a>
            <nav class="nav-links">
                <?php if (!empty($_SESSION['user_id'])): ?>
                <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">Dashboard</a>
                <a class="<?= $currentPage === 'records.php' ? 'active' : '' ?>" href="records.php">All Records</a>
                <a class="<?= $currentPage === 'reports.php' ? 'active' : '' ?>" href="reports.php">Reports</a>
                <div class="admin-menu">
                    <button class="admin-trigger" type="button" aria-expanded="false" aria-controls="admin-dropdown">&#128100; Admin <span class="admin-chevron">&#9662;</span></button>
                    <div class="admin-dropdown" id="admin-dropdown">
                        <a href="profile.php">My Profile</a>
                        <a href="change_password.php">Change Password</a>
                        <a class="admin-logout" href="logout.php">Logout</a>
                    </div>
                </div>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container page-content">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.querySelector('.admin-menu');
            const trigger = document.querySelector('.admin-trigger');
            if (!menu || !trigger) return;

            trigger.addEventListener('click', function () {
                const isOpen = menu.classList.toggle('open');
                trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', function (event) {
                if (!menu.contains(event.target)) {
                    menu.classList.remove('open');
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
