<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$productId = (int) ($_GET['id'] ?? 0);
$product = get_product($productId);
$message = '';

// If the product does not exist, go back to the shop.
if (!$product) {
    header('Location: shop.php');
    exit;
}

// Add the typed quantity from the product page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_to_cart($productId, (int) ($_POST['qty'] ?? 1), $message);
    $product = get_product($productId);
}

$image = $product['image'] !== '' ? 'assets/images/products/' . $product['image'] : 'assets/images/logo.png';

require_once __DIR__ . '/includes/header.php';
?>
<?php if ($message !== ''): ?>
  <p class="notice"><?php echo h($message); ?></p>
<?php endif; ?>

<section class="detail-layout">
  <div class="detail-image">
    <img src="<?php echo h($image); ?>" alt="<?php echo h($product['name']); ?>" class="detail-photo">
  </div>

  <div class="detail-content">
    <p><a href="shop.php">Back to Products</a></p>
    <h1><?php echo h($product['name']); ?></h1>
    <p class="price"><?php echo number_format((float) $product['price'], 2); ?> SAR</p>
    <p>Available stock: <?php echo (int) $product['stock']; ?></p>
    <p><?php echo nl2br(h((string) $product['description'])); ?></p>

    <form method="post" action="product_detail.php?id=<?php echo (int) $productId; ?>" id="product-form" class="detail-form">
      <label for="qty">Quantity</label>
      <div class="quantity-picker">
        <button type="button" class="qty-btn" data-action="decrease" data-target="qty" aria-label="Decrease quantity">-</button>
        <input id="qty" name="qty" type="number" min="1" max="<?php echo max(1, (int) $product['stock']); ?>" value="1" required>
        <button type="button" class="qty-btn" data-action="increase" data-target="qty" aria-label="Increase quantity">+</button>
      </div>
      <div class="button-row">
        <button class="btn" type="submit">Add to Cart</button>
        <button class="btn alt" type="button" onclick="showHelp()">Help</button>
      </div>
    </form>
  </div>
</section>

<script>
function showHelp() {
  // Keep the help simple and visible for the user.
  alert('Use the plus and minus buttons or type the quantity, then click Add to Cart. The quantity cannot be more than the available stock.');
}

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

document.getElementById('product-form').addEventListener('submit', function (event) {
  var qty = parseInt(document.getElementById('qty').value, 10);
  var stock = <?php echo (int) $product['stock']; ?>;

  // Stop the form if the number is missing or too large.
  if (!qty || qty < 1 || qty > stock) {
    event.preventDefault();
    alert('Please enter a valid quantity.');
  }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
