<?php

declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

// Handle Add to Cart from Index page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int) $_POST['product_id'];
    $qty = 1;
    $title = $_POST['title'];
    $price = (float) $_POST['price'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$productId] = [
            'product_id' => $productId,
            'title' => $title,
            'price' => $price,
            'qty' => $qty
        ];
    }
    header('Location: index.php?msg=added');
    exit;
}

$sql = "SELECT product_id, title, product_type, price_sar, short_description, product_image
        FROM products
        WHERE is_active = 1
        ORDER BY created_at DESC
        LIMIT 6";
$featured = mysqli_query($conn, $sql);

require_once __DIR__ . '/includes/header.php';
?>
<section class="card hero-section">
  <h2>Welcome to AuraEdu</h2>
  <p>Your one-stop online shopping system for educational books, courses, and more.</p>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
    <p class="notice">Product added to cart!</p>
  <?php endif; ?>
  <p>
    <a class="btn" href="shop.php">Start Shopping</a>
    <?php if (!is_customer_logged_in() && !is_admin_logged_in()): ?>
      <a class="btn alt" href="signup.php">Create Account</a>
    <?php endif; ?>
  </p>
</section>

<section>
  <h2>Featured Products</h2>
  <div class="grid product-grid">
    <?php while ($row = mysqli_fetch_assoc($featured)): ?>
      <article class="card product-card">
        <a href="product_detail.php?id=<?php echo (int) $row['product_id']; ?>" class="product-image-link">
          <?php 
          $img = !empty($row['product_image']) ? $row['product_image'] : 'no-image.jpg';
          ?>
          <img src="assets/images/products/<?php echo h($img); ?>" alt="<?php echo h($row['title']); ?>" class="product-image">
        </a>
        
        <div class="product-content">
          <h3 class="product-title">
            <a href="product_detail.php?id=<?php echo (int) $row['product_id']; ?>">
              <?php echo h($row['title']); ?>
            </a>
          </h3>
          <p class="product-meta"><?php echo h(ucfirst((string) $row['product_type'])); ?></p>
          <p class="product-desc"><?php echo h((string) ($row['short_description'] ?? '')); ?></p>
          
          <div class="product-footer">
            <p class="price"><?php echo number_format((float) $row['price_sar'], 2); ?> SAR</p>
            <form method="post" action="index.php" class="add-to-cart-form">
              <input type="hidden" name="product_id" value="<?php echo (int) $row['product_id']; ?>">
              <input type="hidden" name="title" value="<?php echo h($row['title']); ?>">
              <input type="hidden" name="price" value="<?php echo (float) $row['price_sar']; ?>">
              <button class="btn btn-icon" type="submit" name="add_to_cart" aria-label="Add to cart" title="Add to cart">
                <i class="fas fa-cart-plus" aria-hidden="true"></i>
              </button>
            </form>
          </div>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
</section>
<?php require_once __DIR__ . '../includes/footer.php'; ?>
