<?php
/**
 * Get user by ID
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @return array|false User data or false if not found
 */
function getUserById($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT id, name, email, is_admin, created_at
        FROM users
        WHERE id = :id
    ");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch();
}

/**
 * Get user by email
 * @param PDO $conn Database connection
 * @param string $email User email
 * @return array|false User data or false if not found
 */
function getUserByEmail($conn, $email) {
    $stmt = $conn->prepare("
        SELECT id, name, email, is_admin, created_at
        FROM users
        WHERE email = :email
    ");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetch();
}

/**
 * Update user profile
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param array $userData User data to update
 * @return bool Success or failure
 */
function updateUserProfile($conn, $userId, $userData) {
    $stmt = $conn->prepare("
        UPDATE users
        SET name = :name, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':name', $userData['name'], PDO::PARAM_STR);
    
    return $stmt->execute();
}

/**
 * Change user password
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param string $currentPassword Current password
 * @param string $newPassword New password
 * @return bool|string Success or error message
 */
function changeUserPassword($conn, $userId, $currentPassword, $newPassword) {
    // Get current password hash
    $stmt = $conn->prepare("
        SELECT password FROM users WHERE id = :id
    ");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch();
    
    if (!$user) {
        return 'User not found';
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        return 'Current password is incorrect';
    }
    
    // Update to new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("
        UPDATE users
        SET password = :password, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    
    return $stmt->execute();
}

/**
 * Get user addresses
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @return array User addresses
 */
function getUserAddresses($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT * FROM addresses
        WHERE user_id = :user_id
        ORDER BY is_default DESC, created_at DESC
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Add user address
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param array $addressData Address data
 * @param bool $isDefault Whether this is the default address
 * @return int|false New address ID or false on failure
 */
function addUserAddress($conn, $userId, $addressData, $isDefault = false) {
    // If this is the default address, unset other default addresses
    if ($isDefault) {
        $stmt = $conn->prepare("
            UPDATE addresses
            SET is_default = 0
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // Insert new address
    $stmt = $conn->prepare("
        INSERT INTO addresses (
            user_id, name, street_address, city, state, 
            zip_code, country, phone, is_default
        ) VALUES (
            :user_id, :name, :street_address, :city, :state, 
            :zip_code, :country, :phone, :is_default
        )
    ");
    
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':name', $addressData['name'], PDO::PARAM_STR);
    $stmt->bindParam(':street_address', $addressData['street_address'], PDO::PARAM_STR);
    $stmt->bindParam(':city', $addressData['city'], PDO::PARAM_STR);
    $stmt->bindParam(':state', $addressData['state'], PDO::PARAM_STR);
    $stmt->bindParam(':zip_code', $addressData['zip_code'], PDO::PARAM_STR);
    $stmt->bindParam(':country', $addressData['country'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $addressData['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':is_default', $isDefault, PDO::PARAM_BOOL);
    
    if ($stmt->execute()) {
        return $conn->lastInsertId();
    }
    
    return false;
}

/**
 * Update user address
 * @param PDO $conn Database connection
 * @param int $addressId Address ID
 * @param int $userId User ID (for permission check)
 * @param array $addressData Updated address data
 * @param bool $isDefault Whether this is the default address
 * @return bool Success or failure
 */
function updateUserAddress($conn, $addressId, $userId, $addressData, $isDefault = false) {
    // Check if address belongs to user
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM addresses
        WHERE id = :id AND user_id = :user_id
    ");
    $stmt->bindParam(':id', $addressId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->fetchColumn() === 0) {
        return false; // Address doesn't belong to this user
    }
    
    // If this is the default address, unset other default addresses
    if ($isDefault) {
        $stmt = $conn->prepare("
            UPDATE addresses
            SET is_default = 0
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // Update address
    $stmt = $conn->prepare("
        UPDATE addresses
        SET name = :name, street_address = :street_address, city = :city, 
            state = :state, zip_code = :zip_code, country = :country, 
            phone = :phone, is_default = :is_default, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    
    $stmt->bindParam(':id', $addressId, PDO::PARAM_INT);
    $stmt->bindParam(':name', $addressData['name'], PDO::PARAM_STR);
    $stmt->bindParam(':street_address', $addressData['street_address'], PDO::PARAM_STR);
    $stmt->bindParam(':city', $addressData['city'], PDO::PARAM_STR);
    $stmt->bindParam(':state', $addressData['state'], PDO::PARAM_STR);
    $stmt->bindParam(':zip_code', $addressData['zip_code'], PDO::PARAM_STR);
    $stmt->bindParam(':country', $addressData['country'], PDO::PARAM_STR);
    $stmt->bindParam(':phone', $addressData['phone'], PDO::PARAM_STR);
    $stmt->bindParam(':is_default', $isDefault, PDO::PARAM_BOOL);
    
    return $stmt->execute();
}

/**
 * Delete user address
 * @param PDO $conn Database connection
 * @param int $addressId Address ID
 * @param int $userId User ID (for permission check)
 * @return bool Success or failure
 */
function deleteUserAddress($conn, $addressId, $userId) {
    $stmt = $conn->prepare("
        DELETE FROM addresses
        WHERE id = :id AND user_id = :user_id
    ");
    $stmt->bindParam(':id', $addressId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    
    return $stmt->execute();
}

/**
 * Get user payment methods
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @return array User payment methods
 */
function getUserPaymentMethods($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT * FROM payment_methods
        WHERE user_id = :user_id
        ORDER BY is_default DESC, created_at DESC
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Add user payment method
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param array $paymentData Payment method data
 * @param bool $isDefault Whether this is the default payment method
 * @return int|false New payment method ID or false on failure
 */
function addUserPaymentMethod($conn, $userId, $paymentData, $isDefault = false) {
    // If this is the default payment method, unset other default payment methods
    if ($isDefault) {
        $stmt = $conn->prepare("
            UPDATE payment_methods
            SET is_default = 0
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // Insert new payment method
    $stmt = $conn->prepare("
        INSERT INTO payment_methods (
            user_id, card_type, last_four, expiry_month, expiry_year, is_default
        ) VALUES (
            :user_id, :card_type, :last_four, :expiry_month, :expiry_year, :is_default
        )
    ");
    
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':card_type', $paymentData['card_type'], PDO::PARAM_STR);
    $stmt->bindParam(':last_four', $paymentData['last_four'], PDO::PARAM_STR);
    $stmt->bindParam(':expiry_month', $paymentData['expiry_month'], PDO::PARAM_STR);
    $stmt->bindParam(':expiry_year', $paymentData['expiry_year'], PDO::PARAM_STR);
    $stmt->bindParam(':is_default', $isDefault, PDO::PARAM_BOOL);
    
    if ($stmt->execute()) {
        return $conn->lastInsertId();
    }
    
    return false;
}

/**
 * Delete user payment method
 * @param PDO $conn Database connection
 * @param int $paymentId Payment method ID
 * @param int $userId User ID (for permission check)
 * @return bool Success or failure
 */
function deleteUserPaymentMethod($conn, $paymentId, $userId) {
    $stmt = $conn->prepare("
        DELETE FROM payment_methods
        WHERE id = :id AND user_id = :user_id
    ");
    $stmt->bindParam(':id', $paymentId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    
    return $stmt->execute();
}

/**
 * Get user orders
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param int $limit Number of orders to return
 * @param int $offset Offset for pagination
 * @return array User orders
 */
function getUserOrders($conn, $userId, $limit = 10, $offset = 0) {
    $stmt = $conn->prepare("
        SELECT o.*, 
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o
        WHERE o.user_id = :user_id
        ORDER BY o.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get user wishlist
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @return array User wishlist
 */
function getUserWishlist($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT b.*, w.created_at as added_at
        FROM wishlists w
        JOIN books b ON w.book_id = b.id
        WHERE w.user_id = :user_id
        ORDER BY w.created_at DESC
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Add book to wishlist
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param int $bookId Book ID
 * @return bool Success or failure
 */
function addToWishlist($conn, $userId, $bookId) {
    // Check if already in wishlist
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM wishlists
        WHERE user_id = :user_id AND book_id = :book_id
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        return true; // Already in wishlist
    }
    
    // Add to wishlist
    $stmt = $conn->prepare("
        INSERT INTO wishlists (user_id, book_id)
        VALUES (:user_id, :book_id)
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    
    return $stmt->execute();
}

/**
 * Remove book from wishlist
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param int $bookId Book ID
 * @return bool Success or failure
 */
function removeFromWishlist($conn, $userId, $bookId) {
    $stmt = $conn->prepare("
        DELETE FROM wishlists
        WHERE user_id = :user_id AND book_id = :book_id
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    
    return $stmt->execute();
}

/**
 * Check if book is in user's wishlist
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param int $bookId Book ID
 * @return bool True if in wishlist, false otherwise
 */
function isInWishlist($conn, $userId, $bookId) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM wishlists
        WHERE user_id = :user_id AND book_id = :book_id
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchColumn() > 0;
}