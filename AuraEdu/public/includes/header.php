<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="<?php echo h(current_lang()); ?>" dir="<?php echo h(current_dir()); ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AuraEdu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <a class="skip-link" href="#main-content">Skip to content</a>
  <header class="site-header" role="banner">
    <div class="container header-row">
      <a href="index.php" class="brand" aria-label="AuraEdu home">
        <img src="assets/images/logo.png" alt="AuraEdu logo" class="brand-logo">
      </a>

      <nav class="site-nav" aria-label="Main navigation">
        <a href="index.php"><i class="fas fa-house" aria-hidden="true"></i><span>Home</span></a>
        <a href="shop.php"><i class="fas fa-box-open" aria-hidden="true"></i><span>Products</span></a>
        <a href="contact.php"><i class="fas fa-location-dot" aria-hidden="true"></i><span>Contact Us</span></a>
        <?php if (is_admin_logged_in()): ?>
          <a href="admin_dashboard.php"><i class="fas fa-user-shield" aria-hidden="true"></i><span>Admin Panel</span></a>
          <a href="signin.php?logout=1"><i class="fas fa-right-from-bracket" aria-hidden="true"></i><span>Logout</span></a>
        <?php else: ?>
          <a href="signin.php"><i class="fas fa-lock" aria-hidden="true"></i><span>Admin Login</span></a>
        <?php endif; ?>
      </nav>

      <a href="cart.php" class="cart-link" aria-label="Open cart">
        <i class="fas fa-shopping-cart" aria-hidden="true"></i>
        <span class="cart-badge"><?php echo (int) cart_count(); ?></span>
      </a>
    </div>
  </header>
  <main id="main-content" class="container" role="main">
