<?php
/**
 * Get reviews for a book
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @param int $limit Number of reviews to return
 * @param int $offset Offset for pagination
 * @return array Array of reviews
 */
function getBookReviews($conn, $bookId, $limit = 10, $offset = 0) {
    $stmt = $conn->prepare("
        SELECT r.*, u.name as user_name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.book_id = :book_id
        ORDER BY r.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get average rating for a book
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @return float Average rating
 */
function getAverageRating($conn, $bookId) {
    $stmt = $conn->prepare("
        SELECT AVG(rating) FROM reviews WHERE book_id = :book_id
    ");
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->execute();
    
    $avg = $stmt->fetchColumn();
    return $avg ? round($avg, 1) : 0;
}

/**
 * Add a review
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @param int $userId User ID
 * @param int $rating Rating (1-5)
 * @param string $title Review title
 * @param string $content Review content
 * @return int|false New review ID or false on failure
 */
function addReview($conn, $bookId, $userId, $rating, $title, $content) {
    // Check if user has already reviewed this book
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM reviews
        WHERE book_id = :book_id AND user_id = :user_id
    ");
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        // Update existing review
        $stmt = $conn->prepare("
            UPDATE reviews
            SET rating = :rating, title = :title, content = :content, created_at = CURRENT_TIMESTAMP
            WHERE book_id = :book_id AND user_id = :user_id
        ");
    } else {
        // Insert new review
        $stmt = $conn->prepare("
            INSERT INTO reviews (book_id, user_id, rating, title, content)
            VALUES (:book_id, :user_id, :rating, :title, :content)
        ");
    }
    
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        return $conn->lastInsertId();
    }
    
    return false;
}

/**
 * Delete a review
 * @param PDO $conn Database connection
 * @param int $reviewId Review ID
 * @param int $userId User ID (for permission check)
 * @param bool $isAdmin Whether the user is an admin
 * @return bool Success or failure
 */
function deleteReview($conn, $reviewId, $userId, $isAdmin = false) {
    // Check if user owns the review or is admin
    if (!$isAdmin) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM reviews
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetchColumn() === 0) {
            return false; // User doesn't own this review
        }
    }
    
    // Delete the review
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = :id");
    $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
    
    return $stmt->execute();
}

/**
 * Get recent reviews
 * @param PDO $conn Database connection
 * @param int $limit Number of reviews to return
 * @return array Array of recent reviews
 */
function getRecentReviews($conn, $limit = 10) {
    $stmt = $conn->prepare("
        SELECT r.*, u.name as user_name, b.title as book_title
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN books b ON r.book_id = b.id
        ORDER BY r.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}