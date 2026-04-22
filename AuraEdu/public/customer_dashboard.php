<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_customer();

$customerId = (int) $_SESSION['customer_id'];
$orderStmt = mysqli_prepare(
    $conn,
    "SELECT order_id, order_date, order_status, total_amount
     FROM orders
     WHERE customer_id = ?
     ORDER BY order_date DESC"
);
mysqli_stmt_bind_param($orderStmt, 'i', $customerId);
mysqli_stmt_execute($orderStmt);
$orders = mysqli_stmt_get_result($orderStmt);
mysqli_stmt_close($orderStmt);

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h2>Customer Dashboard</h2>
  <p>Welcome, <?php echo h((string) ($_SESSION['name'] ?? 'Customer')); ?>.</p>
  <p>
    <a class="btn" href="shop.php">Continue Shopping</a>
    <a class="btn alt" href="checkout.php">Go to Checkout</a>
  </p>
</section>

<section class="card">
  <h3>Your Orders</h3>
  <table aria-label="Customer orders">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Date</th>
        <th>Status</th>
        <th>Total (SAR)</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($order = mysqli_fetch_assoc($orders)): ?>
        <tr>
          <td>#<?php echo (int) $order['order_id']; ?></td>
          <td><?php echo h($order['order_date']); ?></td>
          <td><?php echo h($order['order_status']); ?></td>
          <td><?php echo number_format((float) $order['total_amount'], 2); ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
