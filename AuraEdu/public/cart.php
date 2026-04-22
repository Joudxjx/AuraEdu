<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $productId => $qty) {
            $productId = (int) $productId;
            $qty = max(0, (int) $qty);
            if ($qty === 0) {
                unset($_SESSION['cart'][$productId]);
            } elseif (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['qty'] = $qty;
            }
        }
    }

    if (isset($_POST['delete_item'])) {
        $productId = (int) $_POST['delete_item'];
        unset($_SESSION['cart'][$productId]);
    }

    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="container" style="margin-top: 40px; margin-bottom: 40px;">
  <h2 style="font-size: 32px; margin-bottom: 24px;">Shopping Cart</h2>
  <?php if (empty($_SESSION['cart'])): ?>
    <div class="card" style="text-align: center; padding: 40px;">
      <p style="font-size: 18px; color: var(--muted); margin-bottom: 20px;">Your cart is empty. Keep shopping to find a course or book!</p>
      <a class="btn" href="shop.php" style="font-size: 16px; padding: 12px 24px;">Keep Shopping</a>
    </div>
  <?php else: ?>
    <div style="display: flex; flex-wrap: wrap; gap: 30px;">
      
      <!-- Cart Items List -->
      <div style="flex: 2; min-width: 300px;">
        <p style="font-size: 16px; font-weight: bold; border-bottom: 1px solid var(--border); padding-bottom: 8px; margin-bottom: 16px;">
          <?php echo count($_SESSION['cart']); ?> <?php echo count($_SESSION['cart']) === 1 ? 'Item' : 'Items'; ?> in Cart
        </p>
        
        <form method="post" action="cart.php" id="cart-form">
          <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($_SESSION['cart'] as $item): ?>
              <div class="card" style="display: flex; gap: 16px; align-items: flex-start; padding: 16px; margin: 0; position: relative;">
                <div style="flex: 1;">
                  <h3 style="margin: 0 0 8px; font-size: 16px;">
                    <a href="product_detail.php?id=<?php echo (int) $item['product_id']; ?>" style="color: var(--text); text-decoration: none;">
                      <?php echo h($item['title']); ?>
                    </a>
                  </h3>
                  <button type="submit" name="delete_item" value="<?php echo (int) $item['product_id']; ?>" class="btn-outline" style="border: none; color: var(--primary); padding: 0; font-size: 14px; cursor: pointer; text-decoration: underline;">
                    Remove
                  </button>
                </div>
                
                <div style="text-align: right; min-width: 100px;">
                  <p class="price" style="margin: 0 0 8px; font-size: 18px;"><?php echo number_format((float) $item['price'], 2); ?> SAR</p>
                  <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                    <label for="qty_<?php echo (int) $item['product_id']; ?>" class="sr-only" aria-label="Quantity for <?php echo h($item['title']); ?>">Qty</label>
                    <input
                      id="qty_<?php echo (int) $item['product_id']; ?>"
                      type="number"
                      min="0"
                      name="qty[<?php echo (int) $item['product_id']; ?>]"
                      value="<?php echo (int) $item['qty']; ?>"
                      style="width: 60px; padding: 6px; margin: 0; text-align: center;"
                      onchange="document.getElementById('cart-form').submit();"
                    >
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="update_qty" value="1">
        </form>
        <form method="post" action="cart.php" style="margin-top: 16px;">
          <button class="btn-outline" type="submit" name="clear_cart" style="color: #dc3545; border-color: #dc3545;">Clear All Items</button>
        </form>
      </div>

      <!-- Checkout Summary -->
      <div style="flex: 1; min-width: 300px; max-width: 400px;">
        <div class="card" style="position: sticky; top: 20px;">
          <h3 style="margin-top: 0; color: var(--muted); font-size: 16px;">Total:</h3>
          <p class="price" style="font-size: 36px; margin: 8px 0 24px;"><?php echo number_format(cart_total(), 2); ?> SAR</p>
          
          <a class="btn" href="checkout.php" style="display: block; width: 100%; text-align: center; padding: 16px; font-size: 18px; margin-bottom: 16px;">
            Checkout
          </a>
          
          <hr style="border: 0; border-top: 1px solid var(--border); margin: 16px 0;">
          
          <p style="font-size: 12px; color: var(--muted); text-align: center; margin: 0;">
            Secure checkout powered by AuraEdu.
          </p>
        </div>
      </div>
      
    </div>
  <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
