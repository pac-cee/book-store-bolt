<header class="site-header">
  <div class="header-container">
    <div class="logo">
      <a href="./index.php">
        <span class="logo-text">BookHaven</span>
      </a>
    </div>
    
    <div class="header-search">
      <form action="./catalog.php" method="GET">
        <input type="text" name="search" placeholder="Search for books, authors, or ISBN..." aria-label="Search">
        <button type="submit" aria-label="Search">
          <i class="icon-search"></i>
        </button>
      </form>
    </div>
    
    <nav class="header-nav">
      <ul>
        <li><a href="./catalog.php">Browse</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle">Categories</a>
          <div class="dropdown-menu">
            <a href="./catalog.php?category=fiction">Fiction</a>
            <a href="./catalog.php?category=non-fiction">Non-Fiction</a>
            <a href="./catalog.php?category=mystery">Mystery</a>
            <a href="./catalog.php?category=science-fiction">Science Fiction</a>
            <a href="./catalog.php?category=biography">Biography</a>
            <a href="./catalog.php?category=history">History</a>
            <a href="./catalog.php" class="view-all">View All Categories</a>
          </div>
        </li>
        <li><a href="./new-releases.php">New Releases</a></li>
        <li><a href="./bestsellers.php">Bestsellers</a></li>
      </ul>
    </nav>
    
    <div class="header-actions">
      <div class="header-action cart-action">
        <a href="./cart.php" class="action-icon" aria-label="Shopping Cart">
          <i class="icon-cart"></i>
          <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
          <?php endif; ?>
        </a>
      </div>
      
      <div class="header-action user-action">
        <?php if(isset($_SESSION['user_id'])): ?>
          <div class="dropdown">
            <button class="action-icon dropdown-toggle" aria-label="User Menu">
              <i class="icon-user"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-user-info">
                <p>Hello, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>!</p>
              </div>
              <a href="./account.php">My Account</a>
              <a href="./orders.php">My Orders</a>
              <a href="./wishlist.php">Wishlist</a>
              <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                <a href="./admin/index.php">Admin Dashboard</a>
              <?php endif; ?>
              <a href="./backend/api/logout.php">Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="./login.php" class="action-icon" aria-label="Login">
            <i class="icon-user"></i>
          </a>
        <?php endif; ?>
      </div>
      
      <button class="mobile-menu-toggle" aria-label="Toggle Menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>

<div class="mobile-menu">
  <div class="mobile-search">
    <form action="./catalog.php" method="GET">
      <input type="text" name="search" placeholder="Search..." aria-label="Search">
      <button type="submit" aria-label="Search">
        <i class="icon-search"></i>
      </button>
    </form>
  </div>
  
  <nav class="mobile-nav">
    <ul>
      <li><a href="./index.php">Home</a></li>
      <li><a href="./catalog.php">Browse</a></li>
      <li class="mobile-accordion">
        <a href="#" class="accordion-toggle">Categories</a>
        <div class="accordion-content">
          <a href="./catalog.php?category=fiction">Fiction</a>
          <a href="./catalog.php?category=non-fiction">Non-Fiction</a>
          <a href="./catalog.php?category=mystery">Mystery</a>
          <a href="./catalog.php?category=science-fiction">Science Fiction</a>
          <a href="./catalog.php?category=biography">Biography</a>
          <a href="./catalog.php?category=history">History</a>
          <a href="./catalog.php" class="view-all">View All Categories</a>
        </div>
      </li>
      <li><a href="./new-releases.php">New Releases</a></li>
      <li><a href="./bestsellers.php">Bestsellers</a></li>
      <li><a href="./cart.php">Cart</a></li>
      <?php if(isset($_SESSION['user_id'])): ?>
        <li class="mobile-accordion">
          <a href="#" class="accordion-toggle">My Account</a>
          <div class="accordion-content">
            <a href="./account.php">Account Settings</a>
            <a href="./orders.php">My Orders</a>
            <a href="./wishlist.php">Wishlist</a>
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
              <a href="./admin/index.php">Admin Dashboard</a>
            <?php endif; ?>
            <a href="./backend/api/logout.php">Logout</a>
          </div>
        </li>
      <?php else: ?>
        <li><a href="./login.php">Login</a></li>
        <li><a href="./register.php">Register</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</div>