<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_admin();

$message = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $message = 'Product added successfully.';
    if ($_GET['msg'] === 'updated') $message = 'Product updated successfully.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($productId > 0) {
        // The foreign key constraint ON DELETE CASCADE in `books` and `courses` will automatically delete subtype data
        $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE product_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $productId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $message = 'Product deleted successfully.';
    }
}

// Search functionality
$search = trim($_GET['search'] ?? '');
$query = "SELECT product_id, title, product_type, price_sar, stock_qty, product_image FROM products";
$params = [];
$types = '';

if ($search !== '') {
    $query .= " WHERE title LIKE ? OR short_description LIKE ?";
    $likeSearch = "%{$search}%";
    $params = [$likeSearch, $likeSearch];
    $types = 'ss';
}

$query .= " ORDER BY product_id DESC";

if ($types !== '') {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $productsResult = mysqli_stmt_get_result($stmt);
} else {
    $productsResult = mysqli_query($conn, $query);
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Admin Dashboard - Manage Products</h2>
    <a class="btn" href="admin_add_product.php">Add New Product</a>
  </div>
  
  <?php if ($message !== ''): ?>
    <p class="notice" style="color: var(--primary); font-weight: bold;"><?php echo h($message); ?></p>
  <?php endif; ?>

  <form method="get" action="admin_dashboard.php" style="margin-top: 20px; display: flex; gap: 10px;">
    <input type="text" name="search" placeholder="Search by title or description..." value="<?php echo h($search); ?>" style="flex: 1;">
    <button class="btn" type="submit">Search</button>
    <?php if ($search !== ''): ?>
      <a href="admin_dashboard.php" class="btn" style="background: var(--surface);">Clear</a>
    <?php endif; ?>
  </form>
</section>

<section class="card">
  <h3>Products List</h3>
  <table class="table" style="width: 100%; margin-top: 15px;">
    <thead>
      <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Type</th>
        <th>Price (SAR)</th>
        <th>Stock</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($productsResult) === 0): ?>
        <tr><td colspan="6" style="text-align: center;">No products found.</td></tr>
      <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($productsResult)): ?>
          <tr>
            <td>
              <?php if ($row['product_image'] !== 'no-image.jpg'): ?>
                <img src="assets/images/products/<?php echo h($row['product_image']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
              <?php else: ?>
                <div style="width: 50px; height: 50px; background: var(--surface); display: flex; align-items: center; justify-content: center; border-radius: 4px; font-size: 0.8rem;">No Img</div>
              <?php endif; ?>
            </td>
            <td><?php echo h($row['title']); ?></td>
            <td><?php echo h(ucfirst($row['product_type'])); ?></td>
            <td class="price"><?php echo number_format((float) $row['price_sar'], 2); ?></td>
            <td><?php echo (int) $row['stock_qty']; ?></td>
            <td>
              <div style="display: flex; gap: 10px;">
                <a href="admin_edit_product.php?id=<?php echo (int) $row['product_id']; ?>" style="color: var(--primary);">Edit</a>
                <form method="post" action="admin_dashboard.php" onsubmit="return confirm('Are you sure you want to delete this product?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="product_id" value="<?php echo (int) $row['product_id']; ?>">
                  <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; text-decoration: underline; padding: 0;">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </tbody>
  </table>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
