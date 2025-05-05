<?php
/**
 * Get admin dashboard statistics
 * @param PDO $conn Database connection
 * @return array Statistics data
 */
function getAdminStats($conn) {
    // Total orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders");
    $stmt->execute();
    $totalOrders = $stmt->fetchColumn();
    
    // Orders this month
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM orders
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->execute();
    $ordersThisMonth = $stmt->fetchColumn();
    
    // Orders last month
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM orders
        WHERE MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    ");
    $stmt->execute();
    $ordersLastMonth = $stmt->fetchColumn();
    
    // Calculate orders change percentage
    $ordersChange = 0;
    if ($ordersLastMonth > 0) {
        $ordersChange = round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100);
    }
    
    // Total revenue
    $stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders");
    $stmt->execute();
    $totalRevenue = $stmt->fetchColumn() ?: 0;
    
    // Revenue this month
    $stmt = $conn->prepare("
        SELECT SUM(total_amount) FROM orders
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->execute();
    $revenueThisMonth = $stmt->fetchColumn() ?: 0;
    
    // Revenue last month
    $stmt = $conn->prepare("
        SELECT SUM(total_amount) FROM orders
        WHERE MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    ");
    $stmt->execute();
    $revenueLastMonth = $stmt->fetchColumn() ?: 0;
    
    // Calculate revenue change percentage
    $revenueChange = 0;
    if ($revenueLastMonth > 0) {
        $revenueChange = round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100);
    }
    
    // Total users
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetchColumn();
    
    // Users this month
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM users
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->execute();
    $usersThisMonth = $stmt->fetchColumn();
    
    // Users last month
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM users
        WHERE MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
    ");
    $stmt->execute();
    $usersLastMonth = $stmt->fetchColumn();
    
    // Calculate users change percentage
    $usersChange = 0;
    if ($usersLastMonth > 0) {
        $usersChange = round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100);
    }
    
    // Total books
    $stmt = $conn->prepare("SELECT COUNT(*) FROM books");
    $stmt->execute();
    $totalBooks = $stmt->fetchColumn();
    
    // Books added this month
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM books
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $stmt->execute();
    $booksThisMonth = $stmt->fetchColumn();
    
    return [
        'total_orders' => $totalOrders,
        'orders_this_month' => $ordersThisMonth,
        'orders_change' => $ordersChange,
        'total_revenue' => $totalRevenue,
        'revenue_this_month' => $revenueThisMonth,
        'revenue_change' => $revenueChange,
        'total_users' => $totalUsers,
        'users_this_month' => $usersThisMonth,
        'users_change' => $usersChange,
        'total_books' => $totalBooks,
        'books_change' => $booksThisMonth
    ];
}

/**
 * Get recent orders for admin
 * @param PDO $conn Database connection
 * @param int $limit Number of orders to return
 * @return array Recent orders
 */
function getRecentOrders($conn, $limit = 5) {
    $stmt = $conn->prepare("
        SELECT o.*, u.name as user_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get low stock books
 * @param PDO $conn Database connection
 * @param int $limit Number of books to return
 * @param int $threshold Low stock threshold
 * @return array Low stock books
 */
function getLowStockBooks($conn, $limit = 5, $threshold = 5) {
    $stmt = $conn->prepare("
        SELECT *
        FROM books
        WHERE stock_quantity <= :threshold
        ORDER BY stock_quantity ASC
        LIMIT :limit
    ");
    $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get all users for admin
 * @param PDO $conn Database connection
 * @param int $limit Number of users to return
 * @param int $offset Offset for pagination
 * @return array Users
 */
function getAllUsers($conn, $limit = 10, $offset = 0) {
    $stmt = $conn->prepare("
        SELECT id, name, email, is_admin, created_at,
               (SELECT COUNT(*) FROM orders WHERE user_id = users.id) as order_count
        FROM users
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get total users count
 * @param PDO $conn Database connection
 * @return int Total users
 */
function getTotalUsers($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    
    return $stmt->fetchColumn();
}

/**
 * Create a new admin user
 * @param PDO $conn Database connection
 * @param array $userData User data
 * @return int|false New user ID or false on failure
 */
function createAdminUser($conn, $userData) {
    // Check if email already exists
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM users WHERE email = :email
    ");
    $stmt->bindParam(':email', $userData['email']);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        return false; // Email already exists
    }
    
    // Hash password
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    // Insert new admin user
    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, is_admin)
        VALUES (:name, :email, :password, 1)
    ");
    $stmt->bindParam(':name', $userData['name']);
    $stmt->bindParam(':email', $userData['email']);
    $stmt->bindParam(':password', $hashedPassword);
    
    if ($stmt->execute()) {
        return $conn->lastInsertId();
    }
    
    return false;
}

/**
 * Toggle user admin status
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @return bool Success or failure
 */
function toggleUserAdminStatus($conn, $userId) {
    $stmt = $conn->prepare("
        UPDATE users
        SET is_admin = NOT is_admin
        WHERE id = :id
    ");
    $stmt->bindParam(':id', $userId);
    
    return $stmt->execute();
}

/**
 * Get all orders for admin
 * @param PDO $conn Database connection
 * @param string $status Filter by status (optional)
 * @param int $limit Number of orders to return
 * @param int $offset Offset for pagination
 * @return array Orders
 */
function getAllOrders($conn, $status = '', $limit = 10, $offset = 0) {
    $params = [];
    $sql = "
        SELECT o.*, u.name as user_name,
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE 1=1
    ";
    
    // Add status filter
    if (!empty($status)) {
        $sql .= " AND o.status = :status";
        $params[':status'] = $status;
    }
    
    // Add sorting and pagination
    $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
    $params[':limit'] = $limit;
    $params[':offset'] = $offset;
    
    // Prepare and execute
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        if ($key === ':limit' || $key === ':offset') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get total orders count
 * @param PDO $conn Database connection
 * @param string $status Filter by status (optional)
 * @return int Total orders
 */
function getTotalOrders($conn, $status = '') {
    $sql = "SELECT COUNT(*) FROM orders WHERE 1=1";
    $params = [];
    
    // Add status filter
    if (!empty($status)) {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }
    
    // Prepare and execute
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->execute();
    
    return $stmt->fetchColumn();
}

/**
 * Get order details
 * @param PDO $conn Database connection
 * @param int $orderId Order ID
 * @return array|false Order details or false if not found
 */
function getOrderDetails($conn, $orderId) {
    // Get order header
    $stmt = $conn->prepare("
        SELECT o.*, u.name as user_name, u.email as user_email,
               a.name as shipping_name, a.street_address, a.city, a.state, 
               a.zip_code, a.country, a.phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN addresses a ON o.shipping_address_id = a.id
        WHERE o.id = :id
    ");
    $stmt->bindParam(':id', $orderId);
    $stmt->execute();
    
    $order = $stmt->fetch();
    
    if (!$order) {
        return false;
    }
    
    // Get order items
    $stmt = $conn->prepare("
        SELECT oi.*, b.title as book_title, b.author as book_author, b.cover_image
        FROM order_items oi
        JOIN books b ON oi.book_id = b.id
        WHERE oi.order_id = :order_id
    ");
    $stmt->bindParam(':order_id', $orderId);
    $stmt->execute();
    
    $order['items'] = $stmt->fetchAll();
    
    return $order;
}

/**
 * Update order status
 * @param PDO $conn Database connection
 * @param int $orderId Order ID
 * @param string $status New status
 * @return bool Success or failure
 */
function updateOrderStatus($conn, $orderId, $status) {
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (!in_array($status, $validStatuses)) {
        return false;
    }
    
    $stmt = $conn->prepare("
        UPDATE orders
        SET status = :status, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    $stmt->bindParam(':id', $orderId);
    $stmt->bindParam(':status', $status);
    
    return $stmt->execute();
}

/**
 * Get all reviews for admin
 * @param PDO $conn Database connection
 * @param int $limit Number of reviews to return
 * @param int $offset Offset for pagination
 * @return array Reviews
 */
function getAllReviews($conn, $limit = 10, $offset = 0) {
    $stmt = $conn->prepare("
        SELECT r.*, u.name as user_name, b.title as book_title
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN books b ON r.book_id = b.id
        ORDER BY r.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get total reviews count
 * @param PDO $conn Database connection
 * @return int Total reviews
 */
function getTotalReviews($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reviews");
    $stmt->execute();
    
    return $stmt->fetchColumn();
}

/**
 * Get sales data for chart
 * @param PDO $conn Database connection
 * @param string $period Period (week, month, year)
 * @return array Sales data
 */
function getSalesData($conn, $period = 'month') {
    $sql = "";
    $labels = [];
    
    if ($period === 'week') {
        // Last 7 days
        $sql = "
            SELECT 
                DATE(created_at) as date,
                SUM(total_amount) as total
            FROM orders
            WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at)
        ";
        
        // Generate labels for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[$date] = date('D', strtotime($date));
        }
    } elseif ($period === 'month') {
        // Last 30 days
        $sql = "
            SELECT 
                DATE(created_at) as date,
                SUM(total_amount) as total
            FROM orders
            WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at)
        ";
        
        // Generate labels for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[$date] = date('M d', strtotime($date));
        }
    } else { // year
        // Last 12 months
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(total_amount) as total
            FROM orders
            WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY DATE_FORMAT(created_at, '%Y-%m')
        ";
        
        // Generate labels for last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $labels[$date] = date('M Y', strtotime("$date-01"));
        }
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize data array with zeros
    $data = [];
    foreach ($labels as $key => $label) {
        $data[$key] = 0;
    }
    
    // Fill in actual data
    foreach ($results as $row) {
        $key = $period === 'year' ? $row['month'] : $row['date'];
        if (isset($data[$key])) {
            $data[$key] = (float)$row['total'];
        }
    }
    
    return [
        'labels' => array_values($labels),
        'data' => array_values($data)
    ];
}