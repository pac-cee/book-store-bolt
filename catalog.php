<?php
  session_start();
  require_once './backend/config/database.php';
  require_once './backend/functions/book_functions.php';
  
  // Get query parameters
  $category = isset($_GET['category']) ? $_GET['category'] : '';
  $search = isset($_GET['search']) ? $_GET['search'] : '';
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $perPage = 12;
  
  // Get books based on filters
  $books = getFilteredBooks($conn, $category, $search, $page, $perPage);
  $totalBooks = getTotalFilteredBooks($conn, $category, $search);
  $totalPages = ceil($totalBooks / $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Catalog - BookHaven</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/catalog.css">
</head>
<body>
  <?php include './frontend/components/header.php'; ?>
  
  <main class="container">
    <section class="catalog-header">
      <h1>Book Catalog</h1>
      <?php if($category): ?>
        <p>Category: <span class="highlight"><?php echo ucfirst($category); ?></span></p>
      <?php endif; ?>
      <?php if($search): ?>
        <p>Search results for: <span class="highlight"><?php echo htmlspecialchars($search); ?></span></p>
      <?php endif; ?>
    </section>

    <div class="catalog-container">
      <aside class="filters">
        <div class="filter-section">
          <h3>Categories</h3>
          <ul class="category-list">
            <li><a href="./catalog.php" <?php echo empty($category) ? 'class="active"' : ''; ?>>All Categories</a></li>
            <li><a href="./catalog.php?category=fiction" <?php echo $category === 'fiction' ? 'class="active"' : ''; ?>>Fiction</a></li>
            <li><a href="./catalog.php?category=non-fiction" <?php echo $category === 'non-fiction' ? 'class="active"' : ''; ?>>Non-Fiction</a></li>
            <li><a href="./catalog.php?category=mystery" <?php echo $category === 'mystery' ? 'class="active"' : ''; ?>>Mystery</a></li>
            <li><a href="./catalog.php?category=science-fiction" <?php echo $category === 'science-fiction' ? 'class="active"' : ''; ?>>Science Fiction</a></li>
            <li><a href="./catalog.php?category=biography" <?php echo $category === 'biography' ? 'class="active"' : ''; ?>>Biography</a></li>
            <li><a href="./catalog.php?category=history" <?php echo $category === 'history' ? 'class="active"' : ''; ?>>History</a></li>
          </ul>
        </div>
        
        <div class="filter-section">
          <h3>Price Range</h3>
          <div class="price-slider">
            <input type="range" id="price-min" min="0" max="100" value="0">
            <input type="range" id="price-max" min="0" max="100" value="100">
            <div class="price-values">
              <span id="price-min-value">$0</span> - <span id="price-max-value">$100</span>
            </div>
            <button class="btn btn-sm btn-secondary" id="apply-price-filter">Apply</button>
          </div>
        </div>
      </aside>

      <section class="catalog-books">
        <div class="catalog-controls">
          <div class="book-count">
            Showing <span><?php echo count($books); ?></span> of <span><?php echo $totalBooks; ?></span> books
          </div>
          <div class="sort-control">
            <label for="sort">Sort by:</label>
            <select id="sort" class="sort-select">
              <option value="newest">Newest</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
              <option value="name-asc">Name: A to Z</option>
              <option value="name-desc">Name: Z to A</option>
            </select>
          </div>
        </div>

        <?php if(count($books) > 0): ?>
          <div class="book-grid">
            <?php foreach($books as $book): ?>
              <div class="book-card" data-book-id="<?php echo $book['id']; ?>">
                <div class="book-cover">
                  <img src="<?php echo $book['cover_image']; ?>" alt="<?php echo $book['title']; ?>">
                </div>
                <div class="book-info">
                  <h3><?php echo $book['title']; ?></h3>
                  <p class="author">by <?php echo $book['author']; ?></p>
                  <p class="price">$<?php echo number_format($book['price'], 2); ?></p>
                  <div class="book-actions">
                    <button class="btn btn-primary add-to-cart" data-book-id="<?php echo $book['id']; ?>">Add to Cart</button>
                    <a href="./book.php?id=<?php echo $book['id']; ?>" class="btn btn-secondary">Details</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <?php if($totalPages > 1): ?>
            <div class="pagination">
              <?php if($page > 1): ?>
                <a href="?page=<?php echo $page-1; ?>&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>" class="pagination-item">Previous</a>
              <?php endif; ?>
              
              <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>" class="pagination-item <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
              <?php endfor; ?>
              
              <?php if($page < $totalPages): ?>
                <a href="?page=<?php echo $page+1; ?>&category=<?php echo $category; ?>&search=<?php echo urlencode($search); ?>" class="pagination-item">Next</a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="no-results">
            <h3>No books found</h3>
            <p>Try adjusting your search or browse all categories.</p>
            <a href="./catalog.php" class="btn btn-primary">Browse All Books</a>
          </div>
        <?php endif; ?>
      </section>
    </div>
  </main>

  <?php include './frontend/components/footer.php'; ?>
  
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/catalog.js"></script>
  <script src="./assets/js/cart.js"></script>
</body>
</html>