<?php
  session_start();
  require_once './backend/config/database.php';
  require_once './backend/functions/book_functions.php';
  require_once './backend/functions/review_functions.php';
  
  // Get book ID from URL
  $bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  
  // Redirect if no book ID
  if($bookId === 0) {
    header("Location: ./catalog.php");
    exit;
  }
  
  // Get book details
  $book = getBookById($conn, $bookId);
  
  // Redirect if book not found
  if(!$book) {
    header("Location: ./catalog.php");
    exit;
  }
  
  // Get related books
  $relatedBooks = getRelatedBooks($conn, $bookId, $book['category_id'], 4);
  
  // Get reviews
  $reviews = getBookReviews($conn, $bookId);
  $avgRating = getAverageRating($conn, $bookId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $book['title']; ?> - BookHaven</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/book.css">
</head>
<body>
  <?php include './frontend/components/header.php'; ?>
  
  <main class="container">
    <div class="book-detail-container">
      <div class="book-image">
        <img src="<?php echo $book['cover_image']; ?>" alt="<?php echo $book['title']; ?>">
      </div>
      
      <div class="book-details">
        <div class="breadcrumbs">
          <a href="./catalog.php">Catalog</a> &gt; 
          <a href="./catalog.php?category=<?php echo $book['category_slug']; ?>"><?php echo $book['category_name']; ?></a> &gt; 
          <span><?php echo $book['title']; ?></span>
        </div>
        
        <h1><?php echo $book['title']; ?></h1>
        <p class="author">by <a href="./catalog.php?author=<?php echo urlencode($book['author']); ?>"><?php echo $book['author']; ?></a></p>
        
        <div class="book-meta">
          <div class="rating">
            <div class="stars" data-rating="<?php echo $avgRating; ?>">
              <?php for($i = 1; $i <= 5; $i++): ?>
                <span class="star <?php echo ($i <= $avgRating) ? 'filled' : ''; ?>">★</span>
              <?php endfor; ?>
            </div>
            <span class="rating-count">(<?php echo count($reviews); ?> reviews)</span>
          </div>
          
          <div class="price-container">
            <span class="price">$<?php echo number_format($book['price'], 2); ?></span>
            <?php if($book['original_price'] > $book['price']): ?>
              <span class="original-price">$<?php echo number_format($book['original_price'], 2); ?></span>
              <span class="discount">Save <?php echo round((($book['original_price'] - $book['price']) / $book['original_price']) * 100); ?>%</span>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="book-actions">
          <div class="quantity">
            <button class="quantity-btn" id="decrease-quantity">-</button>
            <input type="number" id="quantity" value="1" min="1" max="99">
            <button class="quantity-btn" id="increase-quantity">+</button>
          </div>
          <button class="btn btn-primary" id="add-to-cart" data-book-id="<?php echo $book['id']; ?>">
            Add to Cart
          </button>
          <button class="btn btn-outline" id="add-to-wishlist" data-book-id="<?php echo $book['id']; ?>">
            <i class="icon-heart"></i> Add to Wishlist
          </button>
        </div>
        
        <div class="book-availability">
          <p><strong>Availability:</strong> <?php echo ($book['stock_quantity'] > 0) ? 'In Stock' : 'Out of Stock'; ?></p>
          <?php if($book['stock_quantity'] > 0 && $book['stock_quantity'] < 10): ?>
            <p class="stock-warning">Only <?php echo $book['stock_quantity']; ?> left in stock - order soon</p>
          <?php endif; ?>
        </div>
        
        <div class="book-description">
          <h3>Description</h3>
          <div class="description-content">
            <?php echo $book['description']; ?>
          </div>
        </div>
        
        <div class="book-details-table">
          <h3>Book Details</h3>
          <table>
            <tr>
              <th>Publisher</th>
              <td><?php echo $book['publisher']; ?></td>
            </tr>
            <tr>
              <th>Publication Date</th>
              <td><?php echo date('F j, Y', strtotime($book['publication_date'])); ?></td>
            </tr>
            <tr>
              <th>ISBN</th>
              <td><?php echo $book['isbn']; ?></td>
            </tr>
            <tr>
              <th>Pages</th>
              <td><?php echo $book['pages']; ?></td>
            </tr>
            <tr>
              <th>Language</th>
              <td><?php echo $book['language']; ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    
    <div class="book-tabs">
      <div class="tabs">
        <button class="tab-btn active" data-tab="reviews">Reviews</button>
        <button class="tab-btn" data-tab="related">Related Books</button>
      </div>
      
      <div class="tab-content reviews-tab active" id="reviews">
        <?php if(count($reviews) > 0): ?>
          <div class="reviews-container">
            <?php foreach($reviews as $review): ?>
              <div class="review">
                <div class="review-header">
                  <div class="review-user">
                    <div class="user-avatar">
                      <img src="./assets/images/avatar-placeholder.jpg" alt="User">
                    </div>
                    <div class="user-info">
                      <h4><?php echo $review['user_name']; ?></h4>
                      <p class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></p>
                    </div>
                  </div>
                  <div class="review-rating">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                      <span class="star <?php echo ($i <= $review['rating']) ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                  </div>
                </div>
                <div class="review-content">
                  <h4><?php echo $review['title']; ?></h4>
                  <p><?php echo $review['content']; ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="no-reviews">
            <p>No reviews yet. Be the first to review this book!</p>
          </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['user_id'])): ?>
          <div class="add-review">
            <h3>Write a Review</h3>
            <form id="review-form" action="./backend/api/add_review.php" method="POST">
              <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
              
              <div class="form-group">
                <label for="review-title">Title</label>
                <input type="text" id="review-title" name="title" required>
              </div>
              
              <div class="form-group">
                <label for="rating">Rating</label>
                <div class="rating-input">
                  <?php for($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($i === 5) ? 'checked' : ''; ?>>
                    <label for="star<?php echo $i; ?>">★</label>
                  <?php endfor; ?>
                </div>
              </div>
              
              <div class="form-group">
                <label for="review-content">Review</label>
                <textarea id="review-content" name="content" rows="4" required></textarea>
              </div>
              
              <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
          </div>
        <?php else: ?>
          <div class="login-to-review">
            <p><a href="./login.php">Sign in</a> to write a review</p>
          </div>
        <?php endif; ?>
      </div>
      
      <div class="tab-content related-tab" id="related">
        <?php if(count($relatedBooks) > 0): ?>
          <div class="book-grid">
            <?php foreach($relatedBooks as $relatedBook): ?>
              <div class="book-card" data-book-id="<?php echo $relatedBook['id']; ?>">
                <div class="book-cover">
                  <img src="<?php echo $relatedBook['cover_image']; ?>" alt="<?php echo $relatedBook['title']; ?>">
                </div>
                <div class="book-info">
                  <h3><?php echo $relatedBook['title']; ?></h3>
                  <p class="author">by <?php echo $relatedBook['author']; ?></p>
                  <p class="price">$<?php echo number_format($relatedBook['price'], 2); ?></p>
                  <div class="book-actions">
                    <button class="btn btn-primary add-to-cart" data-book-id="<?php echo $relatedBook['id']; ?>">Add to Cart</button>
                    <a href="./book.php?id=<?php echo $relatedBook['id']; ?>" class="btn btn-secondary">Details</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p>No related books found.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include './frontend/components/footer.php'; ?>
  
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/book.js"></script>
  <script src="./assets/js/cart.js"></script>
</body>
</html>