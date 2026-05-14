<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$items = cart_items();
$history = purchase_history();
$message = '';

// Complete the purchase and refresh the page data.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {
    checkout_cart($message);
    $items = cart_items();
    $history = purchase_history();
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Checkout</h1>

  <?php if ($message !== ''): ?>
    <p class="notice"><?php echo h($message); ?></p>
  <?php endif; ?>

  <?php if (empty($items)): ?>
    <p>Your cart is empty.</p>
    <p><a class="btn" href="shop.php">Go to Products</a></p>
  <?php else: ?>
    <div class="checkout-list">
      <?php foreach ($items as $item): ?>
        <div class="checkout-item">
          <span><?php echo h($item['name']); ?></span>
          <span><?php echo (int) $item['qty']; ?> x <?php echo number_format((float) $item['price'], 2); ?> SAR</span>
        </div>
      <?php endforeach; ?>
    </div>

    <p class="price">Total: <?php echo number_format(cart_total(), 2); ?> SAR</p>

    <form method="post" action="checkout.php" id="checkout-form">
      <div class="button-row">
        <button class="btn" type="submit" name="buy_now" value="1">Buy</button>
        <button class="btn alt" type="button" onclick="checkoutHelp()">Help</button>
      </div>
    </form>
  <?php endif; ?>
</section>

<section class="card history-section">
  <h2 class="history-title">Previous Purchases</h2>
  <?php if (empty($history)): ?>
    <p>No previous purchases yet.</p>
  <?php else: ?>
    <div class="history-list">
      <?php foreach (array_reverse($history) as $purchase): ?>
        <div class="history-item">
          <div class="history-main">
            <strong class="history-name"><?php echo h($purchase['name']); ?></strong>
            <span class="history-meta">Purchased item</span>
          </div>
          <div class="history-price">
            <?php echo (int) $purchase['qty']; ?> x <?php echo number_format((float) $purchase['price'], 2); ?> SAR
          </div>
          <div class="history-date">
            <?php echo h($purchase['purchased_at']); ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script>
function checkoutHelp() {
  // Explain what happens after clicking Buy.
  alert('Click Buy to complete the purchase. The cart will be emptied and the purchased items will be saved in cookies.');
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
