<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function orderStatusClass(string $status): string
{
    return match ($status) {
        'Pending' => 'status-pending',
        'Ongoing' => 'status-progress',
        'Completed' => 'status-ready',
        default => '',
    };
}

function formatDate(?string $date): string
{
    return $date ? date('d M Y', strtotime($date)) : '-';
}
