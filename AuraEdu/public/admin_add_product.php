<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';
require_admin();

$message = '';
$uploadDir = __DIR__ . '/assets/images/products/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['product_type'] ?? 'book';
    $title = trim($_POST['title'] ?? '');
    $price = (float) ($_POST['price_sar'] ?? 0);
    $desc = trim($_POST['short_description'] ?? '');
    $stock = (int) ($_POST['stock_qty'] ?? 0);
    $discount = (float) ($_POST['discount_percentage'] ?? 0);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $download = trim($_POST['download_link'] ?? '');

    // Handle Image Upload
    $imageName = 'no-image.jpg';
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
        // Insert into products supertype
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO products (product_type, title, price_sar, short_description, product_image, stock_qty, discount_percentage, is_featured, is_active, download_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssdssidiis', $type, $title, $price, $desc, $imageName, $stock, $discount, $isFeatured, $isActive, $download);
        
        if (mysqli_stmt_execute($stmt)) {
            $productId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Insert into subtypes
            if ($type === 'book') {
                $author = trim($_POST['author'] ?? '');
                $publisher = trim($_POST['publisher'] ?? '');
                $year = ($_POST['year'] !== '') ? (int)$_POST['year'] : null;
                $pages = ($_POST['pages'] !== '') ? (int)$_POST['pages'] : null;
                $language = trim($_POST['language'] ?? 'English');
                $bFormat = $_POST['book_format'] ?? 'hard_copy';
                $dFormat = $_POST['delivery_format'] ?? 'physical';

                $stmtBook = mysqli_prepare($conn, "INSERT INTO books (product_id, author, publisher_provider, publication_year, pages_count, language, book_format, delivery_format) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtBook, 'issiisss', $productId, $author, $publisher, $year, $pages, $language, $bFormat, $dFormat);
                mysqli_stmt_execute($stmtBook);
                mysqli_stmt_close($stmtBook);

            } elseif ($type === 'course') {
                $instructor = trim($_POST['instructor'] ?? '');
                $duration = ($_POST['duration'] !== '') ? (int)$_POST['duration'] : null;
                $level = $_POST['level'] ?? 'beginner';
                $access = trim($_POST['access_duration'] ?? 'Lifetime');
                $cdFormat = $_POST['course_delivery_format'] ?? 'digital';

                $stmtCourse = mysqli_prepare($conn, "INSERT INTO courses (product_id, instructor, duration_hours, level, access_duration, delivery_format) VALUES (?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtCourse, 'isisss', $productId, $instructor, $duration, $level, $access, $cdFormat);
                mysqli_stmt_execute($stmtCourse);
                mysqli_stmt_close($stmtCourse);
            }

            header('Location: admin_dashboard.php?msg=added');
            exit;
        } else {
            $message = 'Error adding product to database.';
        }
    } else {
        if ($message === '') $message = 'Title and Price are required.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="card">
  <h2>Add New Product</h2>
  <?php if ($message !== ''): ?>
    <p class="notice error"><?php echo h($message); ?></p>
  <?php endif; ?>

  <form method="post" action="admin_add_product.php" enctype="multipart/form-data">
    <h3>General Product Info</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div>
        <label for="product_type">Product Type</label>
        <select id="product_type" name="product_type" required>
          <option value="book">Book / Material</option>
          <option value="course">Course</option>
        </select>
      </div>
      <div>
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" required>
      </div>
      <div>
        <label for="price_sar">Price (SAR) *</label>
        <input id="price_sar" name="price_sar" type="number" min="0" step="0.01" required>
      </div>
      <div>
        <label for="stock_qty">Stock Quantity</label>
        <input id="stock_qty" name="stock_qty" type="number" min="0" value="0">
      </div>
      <div>
        <label for="product_image">Product Image Upload</label>
        <input type="file" id="product_image" name="product_image" accept="image/*">
      </div>
      <div>
        <label for="download_link">Download Link (if digital)</label>
        <input id="download_link" name="download_link" type="url" placeholder="https://...">
      </div>
    </div>
    
    <label for="short_description">Short Description</label>
    <textarea id="short_description" name="short_description" rows="3"></textarea>

    <div style="margin-top: 10px;">
      <label><input type="checkbox" name="is_featured" value="1"> Featured Product</label>
      <label style="margin-left: 20px;"><input type="checkbox" name="is_active" value="1" checked> Active (Visible in shop)</label>
    </div>

    <hr style="margin: 30px 0; border-color: var(--border);">
    
    <h3>Book Specific Info (Fill if Book)</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div>
        <label for="author">Author</label>
        <input id="author" name="author" type="text">
      </div>
      <div>
        <label for="publisher">Publisher / Provider</label>
        <input id="publisher" name="publisher" type="text">
      </div>
      <div>
        <label for="year">Publication Year</label>
        <input id="year" name="year" type="number" min="1900" max="2100">
      </div>
      <div>
        <label for="pages">Pages Count</label>
        <input id="pages" name="pages" type="number" min="1">
      </div>
      <div>
        <label for="book_format">Book Format</label>
        <select id="book_format" name="book_format">
          <option value="hard_copy">Hard Copy</option>
          <option value="soft_copy">Soft Copy</option>
        </select>
      </div>
      <div>
        <label for="delivery_format">Delivery Format</label>
        <select id="delivery_format" name="delivery_format">
          <option value="physical">Physical</option>
          <option value="digital">Digital</option>
        </select>
      </div>
    </div>

    <hr style="margin: 30px 0; border-color: var(--border);">

    <h3>Course Specific Info (Fill if Course)</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div>
        <label for="instructor">Instructor</label>
        <input id="instructor" name="instructor" type="text">
      </div>
      <div>
        <label for="duration">Duration (Hours)</label>
        <input id="duration" name="duration" type="number" min="1">
      </div>
      <div>
        <label for="level">Level</label>
        <select id="level" name="level">
          <option value="beginner">Beginner</option>
          <option value="intermediate">Intermediate</option>
          <option value="advanced">Advanced</option>
        </select>
      </div>
      <div>
        <label for="course_delivery_format">Delivery Format</label>
        <select id="course_delivery_format" name="course_delivery_format">
          <option value="digital">Digital Only</option>
        </select>
      </div>
    </div>

    <div style="margin-top: 30px;">
      <button class="btn" type="submit">Save Product</button>
      <a href="admin_dashboard.php" class="btn" style="background: var(--surface);">Cancel</a>
    </div>
  </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
