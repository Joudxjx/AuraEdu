<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_admin();

$message = '';
$uploadDir = __DIR__ . '/assets/images/products/';

// Save a new product from the admin form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminId = (int) ($_SESSION['admin_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = '';

    // Upload the image only if the format is allowed.
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $image = uniqid('prod_', true) . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
        } else {
            $message = 'Invalid image format.';
        }
    }

    if ($message === '' && $adminId > 0 && $name !== '' && $price > 0 && $stock >= 0) {
        $stmt = mysqli_prepare(db(), 'INSERT INTO Product (admin_id, name, price, stock, image, description) VALUES (?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'isdiss', $adminId, $name, $price, $stock, $image, $description);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header('Location: admin_dashboard.php?msg=' . urlencode('Product added successfully.'));
        exit;
    }

    if ($message === '') {
        $message = 'Please enter a valid name, price, and stock.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Add Product</h1>

  <?php if ($message !== ''): ?>
    <p class="notice error"><?php echo h($message); ?></p>
  <?php endif; ?>

  <form method="post" action="admin_add_product.php" enctype="multipart/form-data" id="product-admin-form">
    <label for="name">Name</label>
    <input id="name" name="name" type="text" required>

    <label for="price">Price</label>
    <input id="price" name="price" type="number" min="0.01" step="0.01" required>

    <label for="stock">Stock</label>
    <input id="stock" name="stock" type="number" min="0" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"></textarea>

    <label for="image">Image Upload</label>
    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp">

    <div class="button-row">
      <button class="btn" type="submit">Save</button>
      <a class="btn alt" href="admin_dashboard.php">Cancel</a>
    </div>
  </form>
</section>

<script>
document.getElementById('product-admin-form').addEventListener('submit', function (event) {
  var price = parseFloat(document.getElementById('price').value);
  var stock = parseInt(document.getElementById('stock').value, 10);

  // Keep the admin form simple and valid before submit.
  if (document.getElementById('name').value.trim() === '' || !price || price <= 0 || stock < 0) {
    event.preventDefault();
    alert('Please enter valid product details.');
  }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
