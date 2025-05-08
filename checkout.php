<?php
  session_start();
  
  // Redirect if not logged in
  if(!isset($_SESSION['user_id'])) {
    header("Location: ./login.php?redirect=checkout");
    exit;
  }
  
  require_once './backend/config/database.php';
  require_once './backend/functions/cart_functions.php';
  require_once './backend/functions/user_functions.php';
  
  // Get cart items
  $cartItems = [];
  if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cartItems = getCartItems($conn, $_SESSION['cart']);
  }
  
  // Redirect if cart is empty
  if(count($cartItems) === 0) {
    header("Location: ./cart.php");
    exit;
  }
  
  // Calculate totals
  $subtotal = 0;
  foreach($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
  }
  
  $tax = $subtotal * 0.08; // 8% tax
  $shipping = 5.99; // $5.99 shipping
  $total = $subtotal + $tax + $shipping;
  
  // Get user information
  $user = getUserById($conn, $_SESSION['user_id']);
  $addresses = getUserAddresses($conn, $_SESSION['user_id']);
  $paymentMethods = getUserPaymentMethods($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - BookHaven</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/checkout.css">
</head>
<body>
  <?php include './frontend/components/header.php'; ?>
  
  <main class="container">
    <h1>Checkout</h1>
    
    <div class="checkout-container">
      <div class="checkout-main">
        <form id="checkout-form" action="./backend/api/place_order.php" method="POST">
          <div class="checkout-section shipping-address">
            <h2>Shipping Address</h2>
            
            <?php if(count($addresses) > 0): ?>
              <div class="saved-addresses">
                <h3>Select a Saved Address</h3>
                <?php foreach($addresses as $index => $address): ?>
                  <div class="address-option">
                    <input type="radio" name="selected_address" id="address-<?php echo $address['id']; ?>" value="<?php echo $address['id']; ?>" <?php echo ($index === 0) ? 'checked' : ''; ?>>
                    <label for="address-<?php echo $address['id']; ?>" class="address-card">
                      <div class="address-info">
                        <p><strong><?php echo $address['name']; ?></strong></p>
                        <p><?php echo $address['street_address']; ?></p>
                        <p><?php echo $address['city']; ?>, <?php echo $address['state']; ?> <?php echo $address['zip_code']; ?></p>
                        <p><?php echo $address['country']; ?></p>
                        <p>Phone: <?php echo $address['phone']; ?></p>
                      </div>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <div class="address-toggle">
                <button type="button" id="new-address-toggle" class="btn btn-link">
                  + Add New Address
                </button>
              </div>
            <?php endif; ?>
            
            <div class="new-address-form <?php echo (count($addresses) > 0) ? 'hidden' : ''; ?>">
              <div class="form-row">
                <div class="form-group">
                  <label for="name">Full Name</label>
                  <input type="text" id="name" name="name" required value="<?php echo $user['name']; ?>">
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" id="email" name="email" required value="<?php echo $user['email']; ?>">
                </div>
              </div>
              
              <div class="form-group">
                <label for="address">Street Address</label>
                <input type="text" id="address" name="address" required>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" id="city" name="city" required>
                </div>
                <div class="form-group">
                  <label for="state">State/Province</label>
                  <input type="text" id="state" name="state" required>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="zip">ZIP/Postal Code</label>
                  <input type="text" id="zip" name="zip" required>
                </div>
                <div class="form-group">
                  <label for="country">Country</label>
                  <select id="country" name="country" required>
                    <option value="US">United States</option>
                    <option value="CA">Canada</option>
                    <option value="UK">United Kingdom</option>
                    <option value="AU">Australia</option>
                    <option value="DE">Germany</option>
                    <option value="FR">France</option>
                  </select>
                </div>
              </div>
              
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
              </div>
              
              <?php if(count($addresses) > 0): ?>
                <div class="form-group save-address">
                  <label>
                    <input type="checkbox" name="save_address" value="1" checked>
                    Save this address for future orders
                  </label>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="checkout-section payment-method">
            <h2>Payment Method</h2>
            
            <?php if(count($paymentMethods) > 0): ?>
              <div class="saved-payment-methods">
                <h3>Select a Saved Payment Method</h3>
                <?php foreach($paymentMethods as $index => $method): ?>
                  <div class="payment-option">
                    <input type="radio" name="selected_payment" id="payment-<?php echo $method['id']; ?>" value="<?php echo $method['id']; ?>" <?php echo ($index === 0) ? 'checked' : ''; ?>>
                    <label for="payment-<?php echo $method['id']; ?>" class="payment-card">
                      <div class="card-type <?php echo strtolower($method['card_type']); ?>"></div>
                      <div class="card-info">
                        <p>**** **** **** <?php echo $method['last_four']; ?></p>
                        <p>Expires: <?php echo $method['expiry_month']; ?>/<?php echo $method['expiry_year']; ?></p>
                      </div>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <div class="payment-toggle">
                <button type="button" id="new-payment-toggle" class="btn btn-link">
                  + Add New Payment Method
                </button>
              </div>
            <?php endif; ?>
            
            <div class="new-payment-form <?php echo (count($paymentMethods) > 0) ? 'hidden' : ''; ?>">
              <div class="form-group">
                <label for="card-number">Card Number</label>
                <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" required>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="card-holder">Cardholder Name</label>
                  <input type="text" id="card-holder" name="card_holder" required>
                </div>
                <div class="form-group card-expiry">
                  <label>Expiration Date</label>
                  <div class="expiry-inputs">
                    <select id="expiry-month" name="expiry_month" required>
                      <?php for($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo sprintf("%02d", $i); ?>"><?php echo sprintf("%02d", $i); ?></option>
                      <?php endfor; ?>
                    </select>
                    <span>/</span>
                    <select id="expiry-year" name="expiry_year" required>
                      <?php $currentYear = (int)date('Y'); ?>
                      <?php for($i = $currentYear; $i <= $currentYear + 10; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group security-code">
                  <label for="security-code">Security Code (CVV)</label>
                  <input type="text" id="security-code" name="security_code" required>
                </div>
                <div class="form-group">
                  <label for="card-zip">Billing ZIP/Postal Code</label>
                  <input type="text" id="card-zip" name="card_zip" required>
                </div>
              </div>
              
              <?php if(count($paymentMethods) > 0): ?>
                <div class="form-group save-payment">
                  <label>
                    <input type="checkbox" name="save_payment" value="1" checked>
                    Save this payment method for future orders
                  </label>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="checkout-section shipping-method">
            <h2>Shipping Method</h2>
            <div class="shipping-options">
              <div class="shipping-option">
                <input type="radio" name="shipping_method" id="shipping-standard" value="standard" checked>
                <label for="shipping-standard">
                  <div class="shipping-info">
                    <h3>Standard Shipping</h3>
                    <p>Delivery in 3-5 business days</p>
                  </div>
                  <div class="shipping-price">$5.99</div>
                </label>
              </div>
              <div class="shipping-option">
                <input type="radio" name="shipping_method" id="shipping-express" value="express">
                <label for="shipping-express">
                  <div class="shipping-info">
                    <h3>Express Shipping</h3>
                    <p>Delivery in 1-2 business days</p>
                  </div>
                  <div class="shipping-price">$12.99</div>
                </label>
              </div>
            </div>
          </div>
        </form>
      </div>
      
      <div class="checkout-sidebar">
        <div class="order-summary">
          <h2>Order Summary</h2>
          
          <div class="cart-items-summary">
            <h3>Items (<?php echo count($cartItems); ?>)</h3>
            <?php foreach($cartItems as $item): ?>
              <div class="cart-item-summary">
                <div class="item-image">
                  <img src="<?php echo $item['cover_image']; ?>" alt="<?php echo $item['title']; ?>">
                </div>
                <div class="item-details">
                  <h4><?php echo $item['title']; ?></h4>
                  <p>Qty: <?php echo $item['quantity']; ?></p>
                  <p class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <div class="summary-breakdown">
            <div class="summary-item">
              <span>Subtotal</span>
              <span>$<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="summary-item">
              <span>Tax</span>
              <span>$<?php echo number_format($tax, 2); ?></span>
            </div>
            <div class="summary-item">
              <span>Shipping</span>
              <span>$<?php echo number_format($shipping, 2); ?></span>
            </div>
            <div class="summary-item total">
              <span>Total</span>
              <span>$<?php echo number_format($total, 2); ?></span>
            </div>
          </div>
        </div>
        
        <button type="submit" form="checkout-form" class="btn btn-primary place-order-btn">Place Order</button>
        
        <div class="secure-checkout">
          <div class="secure-icon">
            <i class="icon-lock"></i>
          </div>
          <p>Secure Checkout</p>
        </div>
      </div>
    </div>
  </main>

  <?php include './frontend/components/footer.php'; ?>
  
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/checkout.js"></script>
</body>
</html>