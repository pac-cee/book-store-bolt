<?php
  session_start();
  require_once './backend/config/database.php';
  require_once './backend/functions/book_functions.php';
  
  // Get featured books
  $featuredBooks = getFeaturedBooks($conn, 4);
  $newArrivals = getNewArrivals($conn, 8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BookHaven - Your Online Bookstore</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/home.css">
</head>
<body>
  <?php include './frontend/components/header.php'; ?>
  
  <main>
    <section class="hero">
      <div class="hero-content">
        <h1>Discover Your Next Favorite Book</h1>
        <p>Explore thousands of titles across every genre imaginable</p>
        <a href="./catalog.php" class="btn btn-primary">Browse Catalog</a>
      </div>
    </section>

    <section class="featured container">
      <h2>Featured Books</h2>
      <div class="book-grid">
        <?php foreach($featuredBooks as $book): ?>
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
    </section>

    <section class="new-arrivals container">
      <h2>New Arrivals</h2>
      <div class="book-grid">
        <?php foreach($newArrivals as $book): ?>
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
    </section>

    <section class="categories container">
      <h2>Browse Categories</h2>
      <div class="category-grid">
        <a href="./catalog.php?category=fiction" class="category-card">
          <div class="category-icon"><i class="icon-fiction"></i></div>
          <h3>Fiction</h3>
        </a>
        <a href="./catalog.php?category=non-fiction" class="category-card">
          <div class="category-icon"><i class="icon-non-fiction"></i></div>
          <h3>Non-Fiction</h3>
        </a>
        <a href="./catalog.php?category=mystery" class="category-card">
          <div class="category-icon"><i class="icon-mystery"></i></div>
          <h3>Mystery</h3>
        </a>
        <a href="./catalog.php?category=science-fiction" class="category-card">
          <div class="category-icon"><i class="icon-sci-fi"></i></div>
          <h3>Sci-Fi</h3>
        </a>
        <a href="./catalog.php?category=biography" class="category-card">
          <div class="category-icon"><i class="icon-biography"></i></div>
          <h3>Biography</h3>
        </a>
        <a href="./catalog.php?category=history" class="category-card">
          <div class="category-icon"><i class="icon-history"></i></div>
          <h3>History</h3>
        </a>
      </div>
    </section>
  </main>

  <?php include './frontend/components/footer.php'; ?>
  
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/cart.js"></script>
</body>
</html>