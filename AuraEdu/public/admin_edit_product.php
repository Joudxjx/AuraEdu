<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_admin();

$message = '';
$uploadDir = __DIR__ . '/assets/images/products/';

$productId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
if ($productId === 0) {
    header('Location: admin_dashboard.php');
    exit;
}

// Fetch existing product data
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE product_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $productId);
mysqli_stmt_execute($stmt);
$prodResult = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($prodResult);
mysqli_stmt_close($stmt);

if (!$product) {
    header('Location: admin_dashboard.php');
    exit;
}

$type = $product['product_type'];
$subData = [];

if ($type === 'book') {
    $stmtB = mysqli_prepare($conn, "SELECT * FROM books WHERE product_id = ?");
    mysqli_stmt_bind_param($stmtB, 'i', $productId);
    mysqli_stmt_execute($stmtB);
    $subData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtB)) ?: [];
    mysqli_stmt_close($stmtB);
} elseif ($type === 'course') {
    $stmtC = mysqli_prepare($conn, "SELECT * FROM courses WHERE product_id = ?");
    mysqli_stmt_bind_param($stmtC, 'i', $productId);
    mysqli_stmt_execute($stmtC);
    $subData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtC)) ?: [];
    mysqli_stmt_close($stmtC);
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price = (float) ($_POST['price_sar'] ?? 0);
    $desc = trim($_POST['short_description'] ?? '');
    $stock = (int) ($_POST['stock_qty'] ?? 0);
    $discount = (float) ($_POST['discount_percentage'] ?? 0);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $download = trim($_POST['download_link'] ?? '');

    $imageName = $product['product_image']; // keep existing by default
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['product_image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $imageName = uniqid('prod_', true) . '.' . $ext;
            move_uploaded_file($tmpName, $uploadDir . $imageName);
        } else {
            $message = 'Invalid image format.';
        }
    }

    if ($title !== '' && $price > 0 && $message === '') {
        $stmtU = mysqli_prepare(
            $conn,
            "UPDATE products SET title = ?, price_sar = ?, short_description = ?, product_image = ?, stock_qty = ?, discount_percentage = ?, is_featured = ?, is_active = ?, download_link = ? WHERE product_id = ?"
        );
        mysqli_stmt_bind_param($stmtU, 'sdssidiisi', $title, $price, $desc, $imageName, $stock, $discount, $isFeatured, $isActive, $download, $productId);
        mysqli_stmt_execute($stmtU);
        mysqli_stmt_close($stmtU);

        if ($type === 'book') {
            $author = trim($_POST['author'] ?? '');
            $publisher = trim($_POST['publisher'] ?? '');
            $year = ($_POST['year'] !== '') ? (int)$_POST['year'] : null;
            $pages = ($_POST['pages'] !== '') ? (int)$_POST['pages'] : null;
            $language = trim($_POST['language'] ?? 'English');
            $bFormat = $_POST['book_format'] ?? 'hard_copy';
            $dFormat = $_POST['delivery_format'] ?? 'physical';

            $stmtBU = mysqli_prepare($conn, "UPDATE books SET author=?, publisher_provider=?, publication_year=?, pages_count=?, language=?, book_format=?, delivery_format=? WHERE product_id=?");
            mysqli_stmt_bind_param($stmtBU, 'ssiisssi', $author, $publisher, $year, $pages, $language, $bFormat, $dFormat, $productId);
            mysqli_stmt_execute($stmtBU);
            mysqli_stmt_close($stmtBU);
        } elseif ($type === 'course') {
            $instructor = trim($_POST['instructor'] ?? '');
            $duration = ($_POST['duration'] !== '') ? (int)$_POST['duration'] : null;
            $level = $_POST['level'] ?? 'beginner';
            $access = trim($_POST['access_duration'] ?? 'Lifetime');
            $cdFormat = $_POST['course_delivery_format'] ?? 'digital';

            $stmtCU = mysqli_prepare($conn, "UPDATE courses SET instructor=?, duration_hours=?, level=?, access_duration=?, delivery_format=? WHERE product_id=?");
            mysqli_stmt_bind_param($stmtCU, 'sisssi', $instructor, $duration, $level, $access, $cdFormat, $productId);
            mysqli_stmt_execute($stmtCU);
            mysqli_stmt_close($stmtCU);
        }

        header('Location: admin_dashboard.php?msg=updated');
        exit;
    } elseif ($message === '') {
        $message = 'Title and Price are required.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
  <h2>Edit Product: <?php echo h($product['title']); ?></h2>
  <?php if ($message !== ''): ?>
    <p class="notice error"><?php echo h($message); ?></p>
  <?php endif; ?>

  <form method="post" action="admin_edit_product.php?id=<?php echo $productId; ?>" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $productId; ?>">
    <input type="hidden" name="product_type" value="<?php echo h($type); ?>">

    <h3>General Product Info</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div>
        <label>Product Type</label>
        <input type="text" value="<?php echo h(ucfirst($type)); ?>" disabled style="background: var(--surface);">
      </div>
      <div>
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" value="<?php echo h($product['title']); ?>" required>
      </div>
      <div>
        <label for="price_sar">Price (SAR) *</label>
        <input id="price_sar" name="price_sar" type="number" min="0" step="0.01" value="<?php echo h((string)$product['price_sar']); ?>" required>
      </div>
      <div>
        <label for="stock_qty">Stock Quantity</label>
        <input id="stock_qty" name="stock_qty" type="number" min="0" value="<?php echo (int)$product['stock_qty']; ?>">
      </div>
      <div>
        <label for="product_image">Update Image</label>
        <input type="file" id="product_image" name="product_image" accept="image/*">
        <small>Current: <?php echo h($product['product_image']); ?></small>
      </div>
      <div>
        <label for="download_link">Download Link (if digital)</label>
        <input id="download_link" name="download_link" type="url" value="<?php echo h($product['download_link'] ?? ''); ?>" placeholder="https://...">
      </div>
    </div>
    
    <label for="short_description">Short Description</label>
    <textarea id="short_description" name="short_description" rows="3"><?php echo h($product['short_description'] ?? ''); ?></textarea>

    <div style="margin-top: 10px;">
      <label><input type="checkbox" name="is_featured" value="1" <?php if($product['is_featured']) echo 'checked'; ?>> Featured Product</label>
      <label style="margin-left: 20px;"><input type="checkbox" name="is_active" value="1" <?php if($product['is_active']) echo 'checked'; ?>> Active (Visible in shop)</label>
    </div>

    <hr style="margin: 30px 0; border-color: var(--border);">
    
    <?php if ($type === 'book'): ?>
      <h3>Book Specific Info</h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
          <label for="author">Author</label>
          <input id="author" name="author" type="text" value="<?php echo h($subData['author'] ?? ''); ?>">
        </div>
        <div>
          <label for="publisher">Publisher / Provider</label>
          <input id="publisher" name="publisher" type="text" value="<?php echo h($subData['publisher_provider'] ?? ''); ?>">
        </div>
        <div>
          <label for="year">Publication Year</label>
          <input id="year" name="year" type="number" min="1900" max="2100" value="<?php echo h((string)($subData['publication_year'] ?? '')); ?>">
        </div>
        <div>
          <label for="pages">Pages Count</label>
          <input id="pages" name="pages" type="number" min="1" value="<?php echo h((string)($subData['pages_count'] ?? '')); ?>">
        </div>
        <div>
          <label for="book_format">Book Format</label>
          <select id="book_format" name="book_format">
            <option value="hard_copy" <?php if(($subData['book_format'] ?? '') === 'hard_copy') echo 'selected'; ?>>Hard Copy</option>
            <option value="soft_copy" <?php if(($subData['book_format'] ?? '') === 'soft_copy') echo 'selected'; ?>>Soft Copy</option>
          </select>
        </div>
        <div>
          <label for="delivery_format">Delivery Format</label>
          <select id="delivery_format" name="delivery_format">
            <option value="physical" <?php if(($subData['delivery_format'] ?? '') === 'physical') echo 'selected'; ?>>Physical</option>
            <option value="digital" <?php if(($subData['delivery_format'] ?? '') === 'digital') echo 'selected'; ?>>Digital</option>
          </select>
        </div>
      </div>
    <?php elseif ($type === 'course'): ?>
      <h3>Course Specific Info</h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
          <label for="instructor">Instructor</label>
          <input id="instructor" name="instructor" type="text" value="<?php echo h($subData['instructor'] ?? ''); ?>">
        </div>
        <div>
          <label for="duration">Duration (Hours)</label>
          <input id="duration" name="duration" type="number" min="1" value="<?php echo h((string)($subData['duration_hours'] ?? '')); ?>">
        </div>
        <div>
          <label for="level">Level</label>
          <select id="level" name="level">
            <option value="beginner" <?php if(($subData['level'] ?? '') === 'beginner') echo 'selected'; ?>>Beginner</option>
            <option value="intermediate" <?php if(($subData['level'] ?? '') === 'intermediate') echo 'selected'; ?>>Intermediate</option>
            <option value="advanced" <?php if(($subData['level'] ?? '') === 'advanced') echo 'selected'; ?>>Advanced</option>
          </select>
        </div>
        <div>
          <label for="course_delivery_format">Delivery Format</label>
          <select id="course_delivery_format" name="course_delivery_format">
            <option value="digital" <?php if(($subData['delivery_format'] ?? '') === 'digital') echo 'selected'; ?>>Digital Only</option>
          </select>
        </div>
      </div>
    <?php endif; ?>

    <div style="margin-top: 30px;">
      <button class="btn" type="submit">Update Product</button>
      <a href="admin_dashboard.php" class="btn" style="background: var(--surface);">Cancel</a>
    </div>
  </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
