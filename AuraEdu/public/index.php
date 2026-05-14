<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$message = trim($_GET['msg'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $resultMessage = '';
    add_to_cart((int) ($_POST['product_id'] ?? 0), 1, $resultMessage);
    header('Location: index.php?msg=' . urlencode($resultMessage));
    exit;
}

$products = get_products('', 6);
$history = purchase_history();

require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="card">
    <h1>AuraEdu Store</h1>
    <p>Browse products, open the detail page, choose your quantity, and complete checkout from your cart.</p>
    <p>
      <a class="btn" href="shop.php">Shop Now</a>
      <a class="btn alt" href="contact.php">Contact Us</a>
    </p>
  </div>
</section>

<?php if ($message !== ''): ?>
  <p class="notice"><?php echo h($message); ?></p>
<?php endif; ?>

<section>
  <h2>Products</h2>
  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <?php $image = $product['image'] !== '' ? 'assets/images/products/' . $product['image'] : 'assets/images/logo.png'; ?>
      <article class="product-card">
        <a href="product_detail.php?id=<?php echo (int) $product['id']; ?>" class="product-image-link">
          <img src="<?php echo h($image); ?>" alt="<?php echo h($product['name']); ?>" class="product-image">
        </a>
        <div class="product-content">
          <h3 class="product-title">
            <a href="product_detail.php?id=<?php echo (int) $product['id']; ?>"><?php echo h($product['name']); ?></a>
          </h3>
          <p class="product-desc"><?php echo h((string) $product['description']); ?></p>
          <div class="product-footer">
            <span class="price"><?php echo number_format((float) $product['price'], 2); ?> SAR</span>
            <form method="post" action="index.php">
              <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
              <button class="btn btn-icon" type="submit" name="add_to_cart" aria-label="Add <?php echo h($product['name']); ?> to cart">
                <i class="fas fa-cart-plus" aria-hidden="true"></i>
              </button>
            </form>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<?php if (!empty($history)): ?>
  <section class="card history-section">
    <h2 class="history-title">Previous Purchases</h2>
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
  </section>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
