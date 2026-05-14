<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_admin();

$message = '';
$uploadDir = __DIR__ . '/assets/images/products/';
$productId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$product = get_product($productId);

// If the id is wrong, send the admin back to the product list.
if (!$product) {
    header('Location: admin_dashboard.php');
    exit;
}

// Update the product and replace the image only when needed.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = (string) $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $image = uniqid('prod_', true) . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
            delete_product_image((string) $product['image']);
        } else {
            $message = 'Invalid image format.';
        }
    }

    if ($message === '' && $name !== '' && $price > 0 && $stock >= 0) {
        $stmt = mysqli_prepare(db(), 'UPDATE Product SET name = ?, price = ?, stock = ?, image = ?, description = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'sdissi', $name, $price, $stock, $image, $description, $productId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header('Location: admin_dashboard.php?msg=' . urlencode('Product updated successfully.'));
        exit;
    }

    if ($message === '') {
        $message = 'Please enter a valid name, price, and stock.';
    }
}

$imagePath = $product['image'] !== '' ? 'assets/images/products/' . $product['image'] : 'assets/images/logo.png';

require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Modify Product</h1>

  <?php if ($message !== ''): ?>
    <p class="notice error"><?php echo h($message); ?></p>
  <?php endif; ?>

  <img src="<?php echo h($imagePath); ?>" alt="<?php echo h($product['name']); ?>" class="edit-preview">

  <form method="post" action="admin_edit_product.php?id=<?php echo (int) $productId; ?>" enctype="multipart/form-data" id="product-edit-form">
    <input type="hidden" name="id" value="<?php echo (int) $productId; ?>">

    <label for="name">Name</label>
    <input id="name" name="name" type="text" value="<?php echo h($product['name']); ?>" required>

    <label for="price">Price</label>
    <input id="price" name="price" type="number" min="0.01" step="0.01" value="<?php echo h((string) $product['price']); ?>" required>

    <label for="stock">Stock</label>
    <input id="stock" name="stock" type="number" min="0" value="<?php echo (int) $product['stock']; ?>" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?php echo h((string) $product['description']); ?></textarea>

    <label for="image">Image Upload</label>
    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp">

    <div class="button-row">
      <button class="btn" type="submit">Update</button>
      <a class="btn alt" href="admin_dashboard.php">Cancel</a>
    </div>
  </form>
</section>

<script>
document.getElementById('product-edit-form').addEventListener('submit', function (event) {
  var price = parseFloat(document.getElementById('price').value);
  var stock = parseInt(document.getElementById('stock').value, 10);

  // Stop the form if the basic values are not valid.
  if (document.getElementById('name').value.trim() === '' || !price || price <= 0 || stock < 0) {
    event.preventDefault();
    alert('Please enter valid product details.');
  }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
