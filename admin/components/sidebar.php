<aside class="admin-sidebar">
  <div class="sidebar-header">
    <a href="../admin/index.php" class="admin-logo">
      <span class="logo-text">BookHaven</span>
      <span class="logo-badge">Admin</span>
    </a>
    <button class="sidebar-close-btn">
      <i class="icon-close"></i>
    </button>
  </div>
  
  <nav class="sidebar-nav">
    <ul>
      <li>
        <a href="../admin/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
          <i class="icon-dashboard"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="../admin/orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>">
          <i class="icon-orders"></i> Orders
        </a>
      </li>
      <li>
        <a href="../admin/books.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'books.php' || basename($_SERVER['PHP_SELF']) === 'edit-book.php' || basename($_SERVER['PHP_SELF']) === 'add-book.php' ? 'active' : ''; ?>">
          <i class="icon-books"></i> Books
        </a>
      </li>
      <li>
        <a href="../admin/categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>">
          <i class="icon-categories"></i> Categories
        </a>
      </li>
      <li>
        <a href="../admin/users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
          <i class="icon-users"></i> Users
        </a>
      </li>
      <li>
        <a href="../admin/reviews.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reviews.php' ? 'active' : ''; ?>">
          <i class="icon-reviews"></i> Reviews
        </a>
      </li>
      <li>
        <a href="../admin/coupons.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'coupons.php' ? 'active' : ''; ?>">
          <i class="icon-coupon"></i> Coupons
        </a>
      </li>
      <li>
        <a href="../admin/reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>">
          <i class="icon-reports"></i> Reports
        </a>
      </li>
      <li>
        <a href="../admin/settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
          <i class="icon-settings"></i> Settings
        </a>
      </li>
    </ul>
  </nav>
  
  <div class="sidebar-footer">
    <a href="../backend/api/logout.php" class="logout-btn">
      <i class="icon-logout"></i> Logout
    </a>
  </div>
</aside>