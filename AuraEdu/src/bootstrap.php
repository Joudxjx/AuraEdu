<?php
declare(strict_types=1);

// Store PHP sessions inside the project so the cart works reliably.
$sessionPath = __DIR__ . '/tmp';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);
ini_set('session.save_path', $sessionPath);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// This cookie keeps the previous purchases after checkout.
const PURCHASE_COOKIE = 'purchase_history';

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

function db(): mysqli
{
    global $conn;
    return $conn;
}

function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_id']) && ($_SESSION['role'] ?? '') === 'admin';
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: signin.php');
        exit;
    }
}

function get_product(int $id): ?array
{
    // Read one product by its id.
    $stmt = mysqli_prepare(
        db(),
        'SELECT id, name, price, stock, image, description FROM Product WHERE id = ?'
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($stmt);
    return $product;
}

function get_products(string $search = '', int $limit = 0): array
{
    // Build one simple query for the shop and admin product list.
    $sql = 'SELECT id, name, price, stock, image, description FROM Product';
    $params = [];
    $types = '';

    if ($search !== '') {
        $sql .= ' WHERE name LIKE ? OR description LIKE ?';
        $like = '%' . $search . '%';
        $params[] = $like;
        $params[] = $like;
        $types = 'ss';
    }

    $sql .= ' ORDER BY id DESC';

    if ($limit > 0) {
        $sql .= ' LIMIT ' . $limit;
    }

    $rows = [];
    if ($types === '') {
        $result = mysqli_query(db(), $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    $stmt = mysqli_prepare(db(), $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

function ensure_cart(): void
{
    // Always keep the cart as an array inside the session.
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function sync_cart(): void
{
    // Refresh cart items from the database and remove invalid ones.
    ensure_cart();

    foreach (array_keys($_SESSION['cart']) as $productId) {
        $product = get_product((int) $productId);
        if (!$product) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $qty = min((int) $_SESSION['cart'][$productId]['qty'], (int) $product['stock']);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
            continue;
        }

        $_SESSION['cart'][$productId] = [
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => (float) $product['price'],
            'image' => $product['image'],
            'stock' => (int) $product['stock'],
            'qty' => $qty
        ];
    }
}

function cart_items(): array
{
    sync_cart();
    return array_values($_SESSION['cart']);
}

function cart_count(): int
{
    ensure_cart();
    return array_sum(array_column($_SESSION['cart'], 'qty'));
}

function cart_total(): float
{
    ensure_cart();
    $total = 0.0;

    foreach ($_SESSION['cart'] as $item) {
        $total += ((float) $item['price']) * ((int) $item['qty']);
    }

    return $total;
}

function add_to_cart(int $productId, int $qty, string &$message): bool
{
    ensure_cart();
    $qty = max(1, $qty);
    $product = get_product($productId);

    if (!$product) {
        $message = 'Product not found.';
        return false;
    }

    $currentQty = isset($_SESSION['cart'][$productId]) ? (int) $_SESSION['cart'][$productId]['qty'] : 0;
    $newQty = $currentQty + $qty;

    // Stop the user from adding more than the available stock.
    if ($newQty > (int) $product['stock']) {
        $message = 'Requested quantity is not available in stock.';
        return false;
    }

    $_SESSION['cart'][$productId] = [
        'id' => (int) $product['id'],
        'name' => $product['name'],
        'price' => (float) $product['price'],
        'image' => $product['image'],
        'qty' => $newQty
    ];

    $message = 'Product added to cart.';
    return true;
}

function update_cart_item(int $productId, int $qty, string &$message): bool
{
    ensure_cart();

    // Quantity 0 means remove the item from the cart.
    if ($qty <= 0) {
        unset($_SESSION['cart'][$productId]);
        return true;
    }

    $product = get_product($productId);
    if (!$product) {
        unset($_SESSION['cart'][$productId]);
        $message = 'One product was removed because it no longer exists.';
        return false;
    }

    if ($qty > (int) $product['stock']) {
        $message = 'Requested quantity is not available in stock.';
        return false;
    }

    $_SESSION['cart'][$productId] = [
        'id' => (int) $product['id'],
        'name' => $product['name'],
        'price' => (float) $product['price'],
        'image' => $product['image'],
        'qty' => $qty
    ];

    return true;
}

function purchase_history(): array
{
    if (!isset($_COOKIE[PURCHASE_COOKIE])) {
        return [];
    }

    $history = json_decode($_COOKIE[PURCHASE_COOKIE], true);
    return is_array($history) ? $history : [];
}

function save_purchase_history(array $items): void
{
    // Save the last purchases in a cookie instead of the database.
    $history = purchase_history();
    $timestamp = date('Y-m-d H:i:s');

    foreach ($items as $item) {
        $history[] = [
            'name' => $item['name'],
            'qty' => (int) $item['qty'],
            'price' => (float) $item['price'],
            'image' => $item['image'],
            'purchased_at' => $timestamp
        ];
    }

    $history = array_slice($history, -20);
    setcookie(PURCHASE_COOKIE, json_encode($history), time() + (86400 * 30), '/');
    $_COOKIE[PURCHASE_COOKIE] = json_encode($history);
}

function delete_product_image(string $image): void
{
    // Do not try to delete the fallback logo image.
    if ($image === '' || $image === 'logo.png') {
        return;
    }

    $path = __DIR__ . '/../public/assets/images/products/' . $image;
    if (is_file($path)) {
        unlink($path);
    }
}

function checkout_cart(string &$message): bool
{
    // Recheck the cart before changing stock in the database.
    ensure_cart();
    sync_cart();

    if (empty($_SESSION['cart'])) {
        $message = 'Your cart is empty.';
        return false;
    }

    mysqli_begin_transaction(db());

    try {
        foreach ($_SESSION['cart'] as $item) {
            $product = get_product((int) $item['id']);

            // Stop checkout if stock changed after the item was added.
            if (!$product || (int) $item['qty'] > (int) $product['stock']) {
                throw new RuntimeException('Stock is not available.');
            }

            $newStock = (int) $product['stock'] - (int) $item['qty'];
            $stmt = mysqli_prepare(db(), 'UPDATE Product SET stock = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ii', $newStock, $product['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        mysqli_commit(db());
        // Save the purchase history, then empty the cart session.
        save_purchase_history(array_values($_SESSION['cart']));
        $_SESSION['cart'] = [];
        $message = 'Purchase completed successfully.';
        return true;
    } catch (Throwable $e) {
        mysqli_rollback(db());
        $message = 'Purchase could not be completed.';
        return false;
    }
}
