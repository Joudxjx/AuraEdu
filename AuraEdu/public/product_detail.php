<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

$productId = (int) ($_GET['id'] ?? 0);
$message = '';

if ($productId <= 0) {
    header('Location: shop.php');
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT p.product_id, p.title, p.product_type, p.price_sar, p.short_description, p.product_image, p.stock_qty,
            b.author, b.publisher_provider, b.publication_year, b.pages_count, b.language, b.book_format, b.delivery_format AS book_delivery,
            c.instructor, c.duration_hours, c.level, c.access_duration, c.delivery_format AS course_delivery
     FROM products p
     LEFT JOIN books b ON p.product_id = b.product_id AND p.product_type = 'book'
     LEFT JOIN courses c ON p.product_id = c.product_id AND p.product_type = 'course'
     WHERE p.product_id = ? AND p.is_active = 1"
);
mysqli_stmt_bind_param($stmt, 'i', $productId);
mysqli_stmt_execute($stmt);
$product = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($product);
mysqli_stmt_close($stmt);

if (!$item) {
    header('Location: shop.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = max(1, (int) ($_POST['qty'] ?? 1));

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$productId] = [
            'product_id' => $productId,
            'title' => $item['title'],
            'price' => (float) $item['price_sar'],
            'qty' => $qty
        ];
    }
    $message = 'Product added to cart.';
}

require_once __DIR__ . '/includes/header.php';
?>
<main class="product-detail">
  <div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <a href="shop.php">Shop</a> / 
      <a href="shop.php?product_type=<?php echo $item['product_type']; ?>"><?php echo ucfirst($item['product_type']); ?>s</a> / 
      <?php echo h($item['title']); ?>
    </nav>

    <?php if ($message !== ''): ?>
      <p class="notice"><?php echo h($message); ?></p>
    <?php endif; ?>

    <div class="product-grid-detail">
      <!-- Left: Book Cover / Image -->
      <div class="book-cover-wrapper">
        <?php $img = !empty($item['product_image']) ? $item['product_image'] : 'no-image.jpg'; ?>
        <img src="assets/images/products/<?php echo h($img); ?>" alt="<?php echo h($item['title']); ?>" class="book-cover">
        <?php if ($item['product_type'] === 'book' && $item['publication_year']): ?>
          <div class="badge"><?php echo (int)$item['publication_year']; ?> Edition</div>
        <?php elseif ($item['product_type'] === 'course' && $item['level']): ?>
          <div class="badge"><?php echo ucfirst(h($item['level'])); ?></div>
        <?php endif; ?>
      </div>

      <!-- Right: Content -->
      <div class="product-info">
        <h1 class="product-title"><?php echo h($item['title']); ?></h1>
        <p class="product-subtitle"><?php echo strtoupper($item['product_type']); ?> • <?php echo h($item['title']); ?></p>

        <div class="meta">
          <?php if ($item['product_type'] === 'book'): ?>
            <div class="meta-row"><strong>Author:</strong> <span><?php echo h($item['author'] ?? 'N/A'); ?></span></div>
            <div class="meta-row"><strong>Publisher:</strong> <span><?php echo h((string) ($item['publisher_provider'] ?? 'N/A')); ?></span></div>
            <div class="meta-row"><strong>Year:</strong> <span><?php echo h((string) ($item['publication_year'] ?? 'N/A')); ?></span></div>
            <div class="meta-row"><strong>Pages:</strong> <span><?php echo h((string) ($item['pages_count'] ?? 'N/A')); ?></span></div>
            <div class="meta-row"><strong>Language:</strong> <span><?php echo h((string) ($item['language'] ?? 'N/A')); ?></span></div>
            <div class="meta-row"><strong>Format:</strong> <span><?php echo h(str_replace('_', ' ', (string) ($item['book_format'] ?? 'N/A'))); ?> • <?php echo h((string) ($item['book_delivery'] ?? 'N/A')); ?></span></div>
          <?php else: ?>
            <div class="meta-row"><strong>Instructor:</strong> <span><?php echo h($item['instructor'] ?? 'N/A'); ?></span></div>
            <div class="meta-row"><strong>Duration:</strong> <span><?php echo h((string) ($item['duration_hours'] ?? 'N/A')); ?> Hours</span></div>
            <div class="meta-row"><strong>Level:</strong> <span><?php echo h(ucfirst((string) ($item['level'] ?? 'N/A'))); ?></span></div>
            <div class="meta-row"><strong>Access:</strong> <span><?php echo h((string) ($item['access_duration'] ?? 'N/A')); ?></span></div>
            <div class="meta-row"><strong>Language:</strong> <span>English</span></div>
            <div class="meta-row"><strong>Format:</strong> <span>Digital • <?php echo h((string) ($item['course_delivery'] ?? 'N/A')); ?></span></div>
          <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="description">
          <h3>Description</h3>
          <p><?php echo nl2br(h((string) $item['short_description'])); ?></p>
        </div>

        <!-- Price & Action -->
        <div class="price-action">
          <div class="price"><?php echo number_format((float) $item['price_sar'], 2); ?> <span class="currency">SAR</span></div>
          
          <form method="post" action="product_detail.php?id=<?php echo $productId; ?>" class="quantity-cart">
            <div class="quantity">
              <span class="qty-btn minus" aria-hidden="true">-</span>
              <input type="number" name="qty" value="1" min="1" max="<?php echo max(1, (int)$item['stock_qty']); ?>" id="qty-input" class="qty-input" aria-label="Quantity">
              <span class="qty-btn plus" aria-hidden="true">+</span>
            </div>
            <button class="add-to-cart" type="submit">
              <span class="icon">🛒</span>
              Add to Cart
            </button>
          </form>
        </div>

        <div class="delivery-info">
          <small>
            <?php if ($item['product_type'] === 'course' || (isset($item['book_delivery']) && $item['book_delivery'] === 'digital')): ?>
              <span class="check-icon">✅</span> Instant digital download • Accessible immediately after purchase
            <?php else: ?>
              <span class="check-icon">🚚</span> Reliable delivery • Usually arrives within 3-5 business days
            <?php endif; ?>
          </small>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
