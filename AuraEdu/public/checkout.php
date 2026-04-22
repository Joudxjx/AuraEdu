<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_customer();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'card';
    $customerId = (int) $_SESSION['customer_id'];
    $total = cart_total();

    if ($shippingAddress === '') {
        $message = 'Shipping address is required.';
    } else {
        mysqli_begin_transaction($conn);
        try {
            $orderStmt = mysqli_prepare(
                $conn,
                "INSERT INTO orders (customer_id, order_status, payment_method, shipping_address, total_amount)
                 VALUES (?, 'pending', ?, ?, ?)"
            );
            mysqli_stmt_bind_param($orderStmt, 'issd', $customerId, $paymentMethod, $shippingAddress, $total);
            mysqli_stmt_execute($orderStmt);
            $orderId = (int) mysqli_insert_id($conn);
            mysqli_stmt_close($orderStmt);

            $itemStmt = mysqli_prepare(
                $conn,
                "INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total)
                 VALUES (?, ?, ?, ?, ?)"
            );

            foreach ($_SESSION['cart'] as $item) {
                $productId = (int) $item['product_id'];
                $qty = (int) $item['qty'];
                $unitPrice = (float) $item['price'];
                $lineTotal = $qty * $unitPrice;
                mysqli_stmt_bind_param($itemStmt, 'iiidd', $orderId, $productId, $qty, $unitPrice, $lineTotal);
                mysqli_stmt_execute($itemStmt);
            }
            mysqli_stmt_close($itemStmt);

            mysqli_commit($conn);
            $_SESSION['cart'] = [];
            $message = 'Order placed successfully. Order ID: #' . $orderId;
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            $message = 'Could not place order. Please try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="container" style="margin-top: 40px; margin-bottom: 40px;">
  <h2 style="font-size: 32px; margin-bottom: 24px;">Checkout</h2>
  <?php if ($message !== ''): ?>
    <div class="card" style="text-align: center; padding: 40px;">
      <p class="notice" style="font-size: 18px; margin-bottom: 20px;"><?php echo h($message); ?></p>
      <a class="btn" href="shop.php">Continue Shopping</a>
    </div>
  <?php else: ?>
    <div style="display: flex; flex-wrap: wrap; gap: 30px;">
      
      <!-- Checkout Form -->
      <div style="flex: 2; min-width: 300px;">
        <div class="card" style="padding: 24px;">
          <h3 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 24px;">Billing & Shipping Details</h3>
          <form method="post" action="checkout.php" id="checkout-form">
            <label for="shipping_address" style="display: block; font-weight: bold; margin-bottom: 8px;">Shipping Address</label>
            <textarea id="shipping_address" name="shipping_address" rows="3" required placeholder="Enter your full shipping address here..."></textarea>

            <label for="payment_method" style="display: block; font-weight: bold; margin-top: 16px; margin-bottom: 8px;">Payment Method</label>
            <select id="payment_method" name="payment_method" required>
              <option value="card">Credit / Debit Card</option>
              <option value="cash_on_delivery">Cash on Delivery</option>
              <option value="bank_transfer">Bank Transfer</option>
            </select>
          </form>
        </div>
      </div>

      <!-- Order Summary -->
      <div style="flex: 1; min-width: 300px; max-width: 400px;">
        <div class="card" style="position: sticky; top: 20px; padding: 24px;">
          <h3 style="margin-top: 0; border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 16px;">Order Summary</h3>
          
          <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; max-height: 300px; overflow-y: auto;">
            <?php foreach ($_SESSION['cart'] as $item): ?>
              <div style="display: flex; justify-content: space-between; font-size: 14px;">
                <span style="color: var(--muted); flex: 1; padding-right: 12px;"><?php echo h($item['title']); ?> <strong style="color: var(--text);">x<?php echo (int) $item['qty']; ?></strong></span>
                <span style="font-weight: bold; white-space: nowrap;"><?php echo number_format((float) $item['price'] * (int) $item['qty'], 2); ?> SAR</span>
              </div>
            <?php endforeach; ?>
          </div>
          
          <hr style="border: 0; border-top: 1px solid var(--border); margin: 16px 0;">
          
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="margin: 0; color: var(--text);">Total:</h3>
            <p class="price" style="font-size: 24px; margin: 0;"><?php echo number_format(cart_total(), 2); ?> SAR</p>
          </div>
          
          <button class="btn" type="submit" form="checkout-form" style="display: block; width: 100%; text-align: center; padding: 16px; font-size: 18px; margin-bottom: 16px;">
            Complete Payment
          </button>
          
          <p style="font-size: 12px; color: var(--muted); text-align: center; margin: 0;">
            <i class="fas fa-lock" style="margin-right: 4px;"></i> Secure Checkout
          </p>
        </div>
      </div>
      
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
