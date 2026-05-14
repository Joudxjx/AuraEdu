<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$message = trim($_GET['msg'] ?? '');
$search = trim($_GET['search'] ?? '');

// Add one item directly from the product list.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $resultMessage = '';
    add_to_cart((int) ($_POST['product_id'] ?? 0), 1, $resultMessage);
    header('Location: shop.php?search=' . urlencode($search) . '&msg=' . urlencode($resultMessage));
    exit;
}

// Load products with an optional search word.
$products = get_products($search);

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Products</h1>
  <form method="get" action="shop.php" class="search-bar" role="search">
    <label for="search" class="sr-only">Search products</label>
    <input id="search" name="search" type="text" value="<?php echo h($search); ?>" placeholder="Search products">
    <button class="btn" type="submit">Search</button>
  </form>
</section>

<?php if ($message !== ''): ?>
  <p class="notice"><?php echo h($message); ?></p>
<?php endif; ?>

<section class="product-grid">
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
        <p>Stock: <?php echo (int) $product['stock']; ?></p>
        <div class="product-footer">
          <span class="price"><?php echo number_format((float) $product['price'], 2); ?> SAR</span>
          <form method="post" action="shop.php?search=<?php echo urlencode($search); ?>">
            <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
            <button class="btn btn-icon" type="submit" name="add_to_cart" aria-label="Add <?php echo h($product['name']); ?> to cart">
              <i class="fas fa-cart-plus" aria-hidden="true"></i>
            </button>
          </form>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
