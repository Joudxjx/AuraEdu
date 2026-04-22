<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

// Handle Add to Cart from Shop page
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
    // Redirect to prevent form resubmission
    header('Location: shop.php?msg=added');
    exit;
}

$selectedType = trim($_GET['product_type'] ?? '');
$search = trim($_GET['search'] ?? '');

$sql = "SELECT product_id, title, product_type, price_sar, short_description, product_image
        FROM products
        WHERE is_active = 1";

$params = [];
$types = '';
if ($selectedType !== '') {
    $sql .= " AND product_type = ?";
    $params[] = $selectedType;
    $types .= 's';
}
if ($search !== '') {
    $sql .= " AND title LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}
$sql .= " ORDER BY product_id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

require_once __DIR__ . '/includes/header.php';
?>
<section class="card shop-filters">
  <h2>Products / Shop</h2>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
    <p class="notice">Product added to cart!</p>
  <?php endif; ?>
  <form method="get" action="shop.php" class="filter-form">
    <div class="filter-group">
      <label for="product_type">Type</label>
      <select id="product_type" name="product_type">
        <option value="">All Types</option>
        <option value="book" <?php echo ($selectedType === 'book') ? 'selected' : ''; ?>>Books</option>
        <option value="course" <?php echo ($selectedType === 'course') ? 'selected' : ''; ?>>Courses</option>
      </select>
    </div>

    <div class="filter-group">
      <label for="search">Search by title</label>
      <input id="search" name="search" type="text" value="<?php echo h($search); ?>">
    </div>
    
    <div class="filter-group">
      <label>&nbsp;</label>
      <button class="btn" type="submit">Filter</button>
    </div>
  </form>
</section>

<section class="grid product-grid">
  <?php while ($product = mysqli_fetch_assoc($products)): ?>
    <article class="card product-card">
      <a href="product_detail.php?id=<?php echo (int) $product['product_id']; ?>" class="product-image-link">
        <?php 
        $img = !empty($product['product_image']) ? $product['product_image'] : 'no-image.jpg';
        ?>
        <img src="assets/images/products/<?php echo h($img); ?>" alt="<?php echo h($product['title']); ?>" class="product-image">
      </a>
      
      <div class="product-content">
        <h3 class="product-title">
          <a href="product_detail.php?id=<?php echo (int) $product['product_id']; ?>">
            <?php echo h($product['title']); ?>
          </a>
        </h3>
        <p class="product-meta"><?php echo h(ucfirst((string) $product['product_type'])); ?></p>
        <p class="product-desc"><?php echo h((string) ($product['short_description'] ?? '')); ?></p>
        
        <div class="product-footer">
          <p class="price"><?php echo number_format((float) $product['price_sar'], 2); ?> SAR</p>
          <form method="post" action="shop.php" class="add-to-cart-form">
            <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
            <input type="hidden" name="title" value="<?php echo h($product['title']); ?>">
            <input type="hidden" name="price" value="<?php echo (float) $product['price_sar']; ?>">
            <button class="btn btn-icon" type="submit" name="add_to_cart" aria-label="Add to cart" title="Add to cart">
              <i class="fas fa-cart-plus" aria-hidden="true"></i>
            </button>
          </form>
        </div>
      </div>
    </article>
  <?php endwhile; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
