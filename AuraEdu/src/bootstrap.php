<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_lang(): string
{
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'], true)) {
        $_SESSION['lang'] = $_GET['lang'];
    }

    return $_SESSION['lang'] ?? 'en';
}

function current_dir(): string
{
    return current_lang() === 'ar' ? 'rtl' : 'ltr';
}

function is_customer_logged_in(): bool
{
    return isset($_SESSION['customer_id']) && ($_SESSION['role'] ?? '') === 'customer';
}

function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_id']) && ($_SESSION['role'] ?? '') === 'admin';
}

function require_customer(): void
{
    if (!is_customer_logged_in()) {
        header('Location: signin.php');
        exit;
    }
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: signin.php');
        exit;
    }
}

function cart_count(): int
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    return array_sum(array_column($_SESSION['cart'], 'qty'));
}

function cart_total(): float
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0.0;
    }

    $total = 0.0;
    foreach ($_SESSION['cart'] as $item) {
        $total += ((float) $item['price']) * ((int) $item['qty']);
    }
    return $total;
}
