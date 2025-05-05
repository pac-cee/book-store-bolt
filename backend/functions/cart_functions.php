<?php
/**
 * Add item to cart
 * @param array $cart Current cart array
 * @param int $bookId Book ID
 * @param int $quantity Quantity to add
 * @return array Updated cart array
 */
function addToCart($cart, $bookId, $quantity = 1) {
    if (isset($cart[$bookId])) {
        $cart[$bookId] += $quantity;
    } else {
        $cart[$bookId] = $quantity;
    }
    
    return $cart;
}

/**
 * Update cart item quantity
 * @param array $cart Current cart array
 * @param int $bookId Book ID
 * @param int $quantity New quantity
 * @return array Updated cart array
 */
function updateCartItem($cart, $bookId, $quantity) {
    if ($quantity <= 0) {
        return removeFromCart($cart, $bookId);
    }
    
    $cart[$bookId] = $quantity;
    
    return $cart;
}

/**
 * Remove item from cart
 * @param array $cart Current cart array
 * @param int $bookId Book ID
 * @return array Updated cart array
 */
function removeFromCart($cart, $bookId) {
    if (isset($cart[$bookId])) {
        unset($cart[$bookId]);
    }
    
    return $cart;
}

/**
 * Clear cart
 * @return array Empty cart array
 */
function clearCart() {
    return [];
}

/**
 * Get total items in cart
 * @param array $cart Cart array
 * @return int Total number of items
 */
function getCartCount($cart) {
    if (!$cart) {
        return 0;
    }
    
    return array_sum($cart);
}

/**
 * Get cart items with book details
 * @param PDO $conn Database connection
 * @param array $cart Cart array
 * @return array Cart items with details
 */
function getCartItems($conn, $cart) {
    if (empty($cart)) {
        return [];
    }
    
    $bookIds = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($bookIds), '?'));
    
    $stmt = $conn->prepare("
        SELECT * FROM books WHERE id IN ($placeholders)
    ");
    
    // Bind all book IDs as parameters
    $index = 1;
    foreach ($bookIds as $id) {
        $stmt->bindValue($index++, $id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $books = $stmt->fetchAll();
    
    // Add quantity to each book
    $cartItems = [];
    foreach ($books as $book) {
        $bookId = $book['id'];
        $book['quantity'] = $cart[$bookId];
        $cartItems[] = $book;
    }
    
    return $cartItems;
}

/**
 * Calculate cart total
 * @param array $cartItems Cart items with details
 * @return float Cart total
 */
function calculateCartTotal($cartItems) {
    $total = 0;
    
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    return $total;
}

/**
 * Apply coupon to cart
 * @param PDO $conn Database connection
 * @param string $couponCode Coupon code
 * @param float $subtotal Cart subtotal
 * @return array Discount information or error
 */
function applyCoupon($conn, $couponCode, $subtotal) {
    $stmt = $conn->prepare("
        SELECT * FROM coupons 
        WHERE code = :code 
        AND is_active = 1
        AND starts_at <= NOW()
        AND expires_at >= NOW()
    ");
    $stmt->bindParam(':code', $couponCode);
    $stmt->execute();
    
    $coupon = $stmt->fetch();
    
    if (!$coupon) {
        return [
            'success' => false,
            'message' => 'Invalid or expired coupon code.'
        ];
    }
    
    // Check minimum order amount
    if ($subtotal < $coupon['min_order_amount']) {
        return [
            'success' => false,
            'message' => 'Minimum order amount of $' . number_format($coupon['min_order_amount'], 2) . ' required for this coupon.'
        ];
    }
    
    // Calculate discount
    $discount = 0;
    if ($coupon['type'] === 'percentage') {
        $discount = $subtotal * ($coupon['value'] / 100);
    } else { // fixed amount
        $discount = $coupon['value'];
        
        // Don't allow discount greater than subtotal
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }
    }
    
    return [
        'success' => true,
        'message' => 'Coupon applied successfully.',
        'discount' => $discount,
        'coupon' => $coupon
    ];
}