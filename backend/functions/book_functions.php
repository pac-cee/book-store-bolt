<?php
/**
 * Get all books
 * @param PDO $conn Database connection
 * @param int $limit Number of books to return
 * @param int $offset Offset for pagination
 * @return array Array of books
 */
function getAllBooks($conn, $limit = 12, $offset = 0) {
    $stmt = $conn->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        ORDER BY b.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get total number of books
 * @param PDO $conn Database connection
 * @return int Total number of books
 */
function getTotalBooks($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM books");
    $stmt->execute();
    
    return $stmt->fetchColumn();
}

/**
 * Get featured books
 * @param PDO $conn Database connection
 * @param int $limit Number of books to return
 * @return array Array of featured books
 */
function getFeaturedBooks($conn, $limit = 4) {
    $stmt = $conn->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        WHERE b.featured = 1
        ORDER BY RAND()
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get new arrivals
 * @param PDO $conn Database connection
 * @param int $limit Number of books to return
 * @return array Array of new arrival books
 */
function getNewArrivals($conn, $limit = 8) {
    $stmt = $conn->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        ORDER BY b.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get books with filters
 * @param PDO $conn Database connection
 * @param string $category Category slug
 * @param string $search Search term
 * @param int $page Current page for pagination
 * @param int $perPage Items per page
 * @return array Array of filtered books
 */
function getFilteredBooks($conn, $category = '', $search = '', $page = 1, $perPage = 12) {
    $offset = ($page - 1) * $perPage;
    $params = [];
    $sql = "
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        WHERE 1=1
    ";
    
    // Add category filter
    if (!empty($category)) {
        $sql .= " AND c.slug = :category";
        $params[':category'] = $category;
    }
    
    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (b.title LIKE :search OR b.author LIKE :search OR b.isbn LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // Add sorting (default to newest)
    $sql .= " ORDER BY b.created_at DESC";
    
    // Add pagination
    $sql .= " LIMIT :limit OFFSET :offset";
    $params[':limit'] = $perPage;
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
 * Get total filtered books count
 * @param PDO $conn Database connection
 * @param string $category Category slug
 * @param string $search Search term
 * @return int Total number of filtered books
 */
function getTotalFilteredBooks($conn, $category = '', $search = '') {
    $params = [];
    $sql = "
        SELECT COUNT(*) FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        WHERE 1=1
    ";
    
    // Add category filter
    if (!empty($category)) {
        $sql .= " AND c.slug = :category";
        $params[':category'] = $category;
    }
    
    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (b.title LIKE :search OR b.author LIKE :search OR b.isbn LIKE :search)";
        $params[':search'] = "%$search%";
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
 * Get book by ID
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @return array|false Book data or false if not found
 */
function getBookById($conn, $bookId) {
    $stmt = $conn->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        WHERE b.id = :id
    ");
    $stmt->bindParam(':id', $bookId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch();
}

/**
 * Get related books
 * @param PDO $conn Database connection
 * @param int $bookId Current book ID
 * @param int $categoryId Category ID for related books
 * @param int $limit Number of books to return
 * @return array Array of related books
 */
function getRelatedBooks($conn, $bookId, $categoryId, $limit = 4) {
    $stmt = $conn->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        WHERE b.id != :book_id AND b.category_id = :category_id
        ORDER BY RAND()
        LIMIT :limit
    ");
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Search books
 * @param PDO $conn Database connection
 * @param string $query Search query
 * @param int $limit Number of books to return
 * @return array Array of matching books
 */
function searchBooks($conn, $query, $limit = 12) {
    $stmt = $conn->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        LEFT JOIN categories c ON b.category_id = c.id
        WHERE b.title LIKE :query OR b.author LIKE :query OR b.isbn LIKE :query
        ORDER BY 
            CASE 
                WHEN b.title LIKE :exact THEN 1
                WHEN b.title LIKE :start THEN 2
                ELSE 3
            END,
            b.title
        LIMIT :limit
    ");
    $searchParam = "%$query%";
    $exactParam = $query;
    $startParam = "$query%";
    
    $stmt->bindParam(':query', $searchParam, PDO::PARAM_STR);
    $stmt->bindParam(':exact', $exactParam, PDO::PARAM_STR);
    $stmt->bindParam(':start', $startParam, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Get books by category
 * @param PDO $conn Database connection
 * @param string $categorySlug Category slug
 * @param int $limit Number of books to return
 * @param int $offset Offset for pagination
 * @return array Array of books in the category
 */
function getBooksByCategory($conn, $categorySlug, $limit = 12, $offset = 0) {
    $stmt = $conn->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug
        FROM books b
        JOIN categories c ON b.category_id = c.id
        WHERE c.slug = :slug
        ORDER BY b.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':slug', $categorySlug, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Add a new book
 * @param PDO $conn Database connection
 * @param array $bookData Book data
 * @return int|false New book ID or false on failure
 */
function addBook($conn, $bookData) {
    $stmt = $conn->prepare("
        INSERT INTO books (
            title, author, description, price, original_price, cover_image, 
            category_id, isbn, publisher, publication_date, pages, 
            language, stock_quantity, featured
        ) VALUES (
            :title, :author, :description, :price, :original_price, :cover_image, 
            :category_id, :isbn, :publisher, :publication_date, :pages, 
            :language, :stock_quantity, :featured
        )
    ");
    
    $stmt->bindParam(':title', $bookData['title']);
    $stmt->bindParam(':author', $bookData['author']);
    $stmt->bindParam(':description', $bookData['description']);
    $stmt->bindParam(':price', $bookData['price']);
    $stmt->bindParam(':original_price', $bookData['original_price']);
    $stmt->bindParam(':cover_image', $bookData['cover_image']);
    $stmt->bindParam(':category_id', $bookData['category_id']);
    $stmt->bindParam(':isbn', $bookData['isbn']);
    $stmt->bindParam(':publisher', $bookData['publisher']);
    $stmt->bindParam(':publication_date', $bookData['publication_date']);
    $stmt->bindParam(':pages', $bookData['pages']);
    $stmt->bindParam(':language', $bookData['language']);
    $stmt->bindParam(':stock_quantity', $bookData['stock_quantity']);
    $stmt->bindParam(':featured', $bookData['featured']);
    
    if ($stmt->execute()) {
        return $conn->lastInsertId();
    }
    
    return false;
}

/**
 * Update a book
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @param array $bookData Updated book data
 * @return bool Success or failure
 */
function updateBook($conn, $bookId, $bookData) {
    $stmt = $conn->prepare("
        UPDATE books SET
            title = :title,
            author = :author,
            description = :description,
            price = :price,
            original_price = :original_price,
            cover_image = :cover_image,
            category_id = :category_id,
            isbn = :isbn,
            publisher = :publisher,
            publication_date = :publication_date,
            pages = :pages,
            language = :language,
            stock_quantity = :stock_quantity,
            featured = :featured,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    
    $stmt->bindParam(':id', $bookId);
    $stmt->bindParam(':title', $bookData['title']);
    $stmt->bindParam(':author', $bookData['author']);
    $stmt->bindParam(':description', $bookData['description']);
    $stmt->bindParam(':price', $bookData['price']);
    $stmt->bindParam(':original_price', $bookData['original_price']);
    $stmt->bindParam(':cover_image', $bookData['cover_image']);
    $stmt->bindParam(':category_id', $bookData['category_id']);
    $stmt->bindParam(':isbn', $bookData['isbn']);
    $stmt->bindParam(':publisher', $bookData['publisher']);
    $stmt->bindParam(':publication_date', $bookData['publication_date']);
    $stmt->bindParam(':pages', $bookData['pages']);
    $stmt->bindParam(':language', $bookData['language']);
    $stmt->bindParam(':stock_quantity', $bookData['stock_quantity']);
    $stmt->bindParam(':featured', $bookData['featured']);
    
    return $stmt->execute();
}

/**
 * Delete a book
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @return bool Success or failure
 */
function deleteBook($conn, $bookId) {
    $stmt = $conn->prepare("DELETE FROM books WHERE id = :id");
    $stmt->bindParam(':id', $bookId);
    
    return $stmt->execute();
}

/**
 * Update book stock
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @param int $quantity New stock quantity
 * @return bool Success or failure
 */
function updateBookStock($conn, $bookId, $quantity) {
    $stmt = $conn->prepare("
        UPDATE books 
        SET stock_quantity = :quantity, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    $stmt->bindParam(':id', $bookId);
    $stmt->bindParam(':quantity', $quantity);
    
    return $stmt->execute();
}

/**
 * Check if a book is in stock
 * @param PDO $conn Database connection
 * @param int $bookId Book ID
 * @param int $quantity Quantity to check
 * @return bool True if in stock, false otherwise
 */
function isBookInStock($conn, $bookId, $quantity = 1) {
    $stmt = $conn->prepare("
        SELECT stock_quantity FROM books WHERE id = :id
    ");
    $stmt->bindParam(':id', $bookId);
    $stmt->execute();
    
    $available = $stmt->fetchColumn();
    
    return $available >= $quantity;
}