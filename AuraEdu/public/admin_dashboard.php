<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_admin();

$message = trim($_GET['msg'] ?? '');
$search = trim($_GET['search'] ?? '');

// Delete the product from the database and from the current cart session.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product = get_product((int) ($_POST['delete_product'] ?? 0));

    if ($product) {
        $stmt = mysqli_prepare(db(), 'DELETE FROM Product WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $product['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        unset($_SESSION['cart'][(int) $product['id']]);
        delete_product_image((string) $product['image']);
        $message = 'Product deleted successfully.';
    }
}

// Reuse the same product list helper used by the shop page.
$products = get_products($search);

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <div class="page-actions">
    <h1>Admin Panel</h1>
    <div class="button-row">
      <a class="btn" href="admin_add_product.php">Add Product</a>
      <a class="btn alt" href="signin.php?logout=1">Logout</a>
    </div>
  </div>

  <?php if ($message !== ''): ?>
    <p class="notice"><?php echo h($message); ?></p>
  <?php endif; ?>

  <form method="get" action="admin_dashboard.php" class="search-bar">
    <label for="search" class="sr-only">Search products</label>
    <input id="search" name="search" type="text" value="<?php echo h($search); ?>" placeholder="Search products">
    <button class="btn" type="submit">Search</button>
  </form>
</section>

<section class="card">
  <table>
    <thead>
      <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
        <tr>
          <td colspan="5">No products found.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
          <?php $image = $product['image'] !== '' ? 'assets/images/products/' . $product['image'] : 'assets/images/logo.png'; ?>
          <tr>
            <td><img src="<?php echo h($image); ?>" alt="<?php echo h($product['name']); ?>" class="admin-thumb"></td>
            <td><?php echo h($product['name']); ?></td>
            <td><?php echo number_format((float) $product['price'], 2); ?> SAR</td>
            <td><?php echo (int) $product['stock']; ?></td>
            <td>
              <div class="table-actions">
                <a href="admin_edit_product.php?id=<?php echo (int) $product['id']; ?>">Modify</a>
                <form method="post" action="admin_dashboard.php?search=<?php echo urlencode($search); ?>" onsubmit="return confirm('Delete this product?');">
                  <button type="submit" name="delete_product" value="<?php echo (int) $product['id']; ?>">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
