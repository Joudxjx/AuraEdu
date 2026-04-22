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
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Include FontAwesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <a class="skip-link" href="#main-content">Skip to content</a>
  <header role="banner" class="udemy-header">
    <div class="container header-container">
      <a href="index.php" class="logo-link" aria-label="AuraEdu Home">
        <img src="assets/images/Logo.png" alt="AuraEdu Logo" class="header-logo">
      </a>
      
      <div class="header-nav" role="navigation" aria-label="Main Navigation">
        <a href="shop.php" class="nav-link">Categories</a>
      </div>

      <div class="header-search">
        <form method="get" action="shop.php" class="search-form" role="search">
          <i class="fas fa-search search-icon" aria-hidden="true"></i>
          <input type="text" name="search" placeholder="Search for anything" aria-label="Search for products">
        </form>
      </div>

      <div class="header-actions">
        <?php if (is_admin_logged_in()): ?>
          <a href="admin_dashboard.php" class="nav-link">Admin</a>
        <?php endif; ?>

        <?php if (is_customer_logged_in()): ?>
          <a href="customer_dashboard.php" class="nav-link">Dashboard</a>
        <?php endif; ?>
        
        <a href="cart.php" class="cart-link" aria-label="Shopping Cart">
          <i class="fas fa-shopping-cart" aria-hidden="true"></i>
          <?php if (cart_count() > 0): ?>
            <span class="cart-badge"><?php echo (int) cart_count(); ?></span>
          <?php endif; ?>
        </a>

        <?php if (is_customer_logged_in() || is_admin_logged_in()): ?>
          <a href="signin.php?logout=1" class="btn-outline">Sign Out</a>
        <?php else: ?>
          <a href="signin.php" class="btn-outline">Log in</a>
          <a href="signup.php" class="btn-solid">Sign up</a>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <main id="main-content" class="container" role="main">
