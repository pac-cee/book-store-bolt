<?php
  session_start();
  require_once './backend/config/database.php';
  require_once './backend/functions/cart_functions.php';
  
  // Get cart items
  $cartItems = [];
  if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartItems = getCartItems($conn, $_SESSION['cart']);
  }
  
  // Calculate totals
  $subtotal = 0;
  foreach($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
  }
  
  $tax = $subtotal * 0.08; // 8% tax
  $shipping = ($subtotal > 0) ? 5.99 : 0; // $5.99 shipping, free if cart is empty
  $total = $subtotal + $tax + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - BookHaven</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/cart.css">
</head>
<body>
  <?php include './frontend/components/header.php'; ?>
  
  <main class="container">
    <h1>Your Shopping Cart</h1>
    
    <?php if(count($cartItems) > 0): ?>
      <div class="cart-container">
        <div class="cart-items">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($cartItems as $item): ?>
                <tr class="cart-item" data-book-id="<?php echo $item['id']; ?>">
                  <td class="product-info">
                    <img src="<?php echo $item['cover_image']; ?>" alt="<?php echo $item['title']; ?>">
                    <div>
                      <h3><?php echo $item['title']; ?></h3>
                      <p class="author">by <?php echo $item['author']; ?></p>
                    </div>
                  </td>
                  <td class="price">$<?php echo number_format($item['price'], 2); ?></td>
                  <td class="quantity">
                    <div class="quantity-control">
                      <button class="quantity-btn decrease" data-book-id="<?php echo $item['id']; ?>">-</button>
                      <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="99" data-book-id="<?php echo $item['id']; ?>">
                      <button class="quantity-btn increase" data-book-id="<?php echo $item['id']; ?>">+</button>
                    </div>
                  </td>
                  <td class="total">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                  <td class="action">
                    <button class="remove-item" data-book-id="<?php echo $item['id']; ?>">Remove</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          
          <div class="cart-actions">
            <a href="./catalog.php" class="btn btn-secondary">Continue Shopping</a>
            <button id="clear-cart" class="btn btn-outline">Clear Cart</button>
          </div>
        </div>
        
        <div class="cart-summary">
          <h2>Order Summary</h2>
          <div class="summary-item">
            <span>Subtotal</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
          </div>
          <div class="summary-item">
            <span>Tax (8%)</span>
            <span>$<?php echo number_format($tax, 2); ?></span>
          </div>
          <div class="summary-item">
            <span>Shipping</span>
            <span>$<?php echo number_format($shipping, 2); ?></span>
          </div>
          <div class="summary-total">
            <span>Total</span>
            <span>$<?php echo number_format($total, 2); ?></span>
          </div>
          
          <div class="promo-code">
            <h3>Promo Code</h3>
            <div class="promo-input">
              <input type="text" id="promo-code" placeholder="Enter promo code">
              <button id="apply-promo" class="btn btn-secondary">Apply</button>
            </div>
          </div>
          
          <a href="<?php echo isset($_SESSION['user_id']) ? './checkout.php' : './login.php?redirect=checkout'; ?>" class="btn btn-primary checkout-btn">
            <?php echo isset($_SESSION['user_id']) ? 'Proceed to Checkout' : 'Login to Checkout'; ?>
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="empty-cart">
        <div class="empty-cart-icon">
          <i class="icon-cart-empty"></i>
        </div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added any books to your cart yet.</p>
        <a href="./catalog.php" class="btn btn-primary">Browse Books</a>
      </div>
    <?php endif; ?>
  </main>

  <?php include './frontend/components/footer.php'; ?>
  
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/cart-page.js"></script>
</body>
</html>