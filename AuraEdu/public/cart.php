<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$message = '';

// Handle cart actions on the same page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_item'])) {
        unset($_SESSION['cart'][(int) $_POST['delete_item']]);
        $message = 'Product removed from cart.';
    }

    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $message = 'Cart cleared.';
    }

    if (isset($_POST['update_cart']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $productId => $qty) {
            $itemMessage = '';
            update_cart_item((int) $productId, (int) $qty, $itemMessage);
            if ($itemMessage !== '') {
                $message = $itemMessage;
            }
        }

        if ($message === '') {
            $message = 'Cart updated.';
        }
    }
}

// Read the latest cart after any changes.
$items = cart_items();

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Cart</h1>
  <?php if ($message !== ''): ?>
    <p class="notice"><?php echo h($message); ?></p>
  <?php endif; ?>

  <?php if (empty($items)): ?>
    <p>Your cart is empty.</p>
    <p><a class="btn" href="shop.php">Continue Shopping</a></p>
  <?php else: ?>
    <form method="post" action="cart.php" id="cart-form">
      <div class="cart-list">
        <?php foreach ($items as $item): ?>
          <?php $image = $item['image'] !== '' ? 'assets/images/products/' . $item['image'] : 'assets/images/logo.png'; ?>
          <div class="cart-item">
            <img src="<?php echo h($image); ?>" alt="<?php echo h($item['name']); ?>" class="cart-thumb">
            <div class="cart-main">
              <h3><a href="product_detail.php?id=<?php echo (int) $item['id']; ?>"><?php echo h($item['name']); ?></a></h3>
              <p><?php echo number_format((float) $item['price'], 2); ?> SAR</p>
            </div>
            <div class="cart-actions">
              <label for="qty_<?php echo (int) $item['id']; ?>">Quantity</label>
              <div class="quantity-picker">
                <button type="button" class="qty-btn" data-action="decrease" data-target="qty_<?php echo (int) $item['id']; ?>" aria-label="Decrease quantity">-</button>
                <input id="qty_<?php echo (int) $item['id']; ?>" name="qty[<?php echo (int) $item['id']; ?>]" type="number" min="0" max="<?php echo (int) ($item['stock'] ?? 0); ?>" value="<?php echo (int) $item['qty']; ?>" required>
                <button type="button" class="qty-btn" data-action="increase" data-target="qty_<?php echo (int) $item['id']; ?>" aria-label="Increase quantity">+</button>
              </div>
              <button class="btn alt" type="submit" name="delete_item" value="<?php echo (int) $item['id']; ?>">Delete</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="button-row">
        <button class="btn" type="submit" name="update_cart" value="1">Modify Quantity</button>
        <button class="btn alt" type="submit" name="clear_cart" value="1">Delete All</button>
        <a class="btn" href="checkout.php">Buy</a>
      </div>
    </form>

    <div class="card total-card">
      <strong>Total: <?php echo number_format(cart_total(), 2); ?> SAR</strong>
    </div>
  <?php endif; ?>
</section>

<script>
document.querySelectorAll('.qty-btn').forEach(function (button) {
  button.addEventListener('click', function () {
    var input = document.getElementById(button.dataset.target);
    var min = parseInt(input.min || '0', 10);
    var max = parseInt(input.max || '999999', 10);
    var value = parseInt(input.value || String(min), 10);

    if (button.dataset.action === 'increase') {
      value = Math.min(max, value + 1);
    } else {
      value = Math.max(min, value - 1);
    }

    input.value = value;
  });
});

document.getElementById('cart-form')?.addEventListener('submit', function (event) {
  var fields = this.querySelectorAll('input[type="number"]');

  // Make sure every quantity is a valid number.
  for (var i = 0; i < fields.length; i += 1) {
    if (fields[i].value === '' || parseInt(fields[i].value, 10) < 0) {
      event.preventDefault();
      alert('Please enter valid quantities. Use 0 to remove an item.');
      return;
    }
  }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
