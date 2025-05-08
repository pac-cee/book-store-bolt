<?php
  session_start();
  
  // Check if user is admin
  if(!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login.php");
    exit;
  }
  
  require_once '../backend/config/database.php';
  require_once '../backend/functions/admin_functions.php';
  
  // Get statistics
  $stats = getAdminStats($conn);
  
  // Get recent orders
  $recentOrders = getRecentOrders($conn, 5);
  
  // Get low stock books
  $lowStockBooks = getLowStockBooks($conn, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - BookHaven</title>
  <link rel="stylesheet" href="../assets/css/main.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="admin-layout">
    <?php include './components/sidebar.php'; ?>
    
    <div class="admin-content">
      <?php include './components/header.php'; ?>
      
      <main class="admin-main">
        <h1>Dashboard</h1>
        
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon orders-icon"></div>
            <div class="stat-content">
              <h3>Total Orders</h3>
              <p class="stat-value"><?php echo $stats['total_orders']; ?></p>
              <p class="stat-change positive">
                <i class="icon-arrow-up"></i> <?php echo $stats['orders_change']; ?>% from last month
              </p>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon revenue-icon"></div>
            <div class="stat-content">
              <h3>Revenue</h3>
              <p class="stat-value">$<?php echo number_format($stats['total_revenue'], 2); ?></p>
              <p class="stat-change positive">
                <i class="icon-arrow-up"></i> <?php echo $stats['revenue_change']; ?>% from last month
              </p>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon users-icon"></div>
            <div class="stat-content">
              <h3>Total Users</h3>
              <p class="stat-value"><?php echo $stats['total_users']; ?></p>
              <p class="stat-change positive">
                <i class="icon-arrow-up"></i> <?php echo $stats['users_change']; ?>% from last month
              </p>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon books-icon"></div>
            <div class="stat-content">
              <h3>Total Books</h3>
              <p class="stat-value"><?php echo $stats['total_books']; ?></p>
              <p class="stat-change positive">
                <i class="icon-arrow-up"></i> <?php echo $stats['books_change']; ?> new this month
              </p>
            </div>
          </div>
        </div>
        
        <div class="dashboard-row">
          <div class="dashboard-card">
            <div class="card-header">
              <h2>Recent Orders</h2>
              <a href="./orders.php" class="btn btn-link">View All</a>
            </div>
            <div class="card-content">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($recentOrders as $order): ?>
                    <tr>
                      <td>#<?php echo $order['id']; ?></td>
                      <td><?php echo $order['user_name']; ?></td>
                      <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                      <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                      <td>
                        <span class="status-badge <?php echo strtolower($order['status']); ?>">
                          <?php echo $order['status']; ?>
                        </span>
                      </td>
                      <td>
                        <a href="./order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline">View</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="dashboard-card">
            <div class="card-header">
              <h2>Low Stock Books</h2>
              <a href="./books.php?filter=low-stock" class="btn btn-link">View All</a>
            </div>
            <div class="card-content">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Book</th>
                    <th>Stock</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($lowStockBooks as $book): ?>
                    <tr>
                      <td class="book-info">
                        <img src="<?php echo $book['cover_image']; ?>" alt="<?php echo $book['title']; ?>">
                        <div>
                          <p class="book-title"><?php echo $book['title']; ?></p>
                          <p class="book-author"><?php echo $book['author']; ?></p>
                        </div>
                      </td>
                      <td>
                        <span class="stock-badge <?php echo ($book['stock_quantity'] === 0) ? 'out' : 'low'; ?>">
                          <?php echo ($book['stock_quantity'] === 0) ? 'Out of Stock' : $book['stock_quantity'] . ' left'; ?>
                        </span>
                      </td>
                      <td>
                        <a href="./edit-book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">Update Stock</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <div class="dashboard-row">
          <div class="dashboard-card full-width">
            <div class="card-header">
              <h2>Sales Overview</h2>
              <div class="period-selector">
                <button class="period-btn active" data-period="week">Week</button>
                <button class="period-btn" data-period="month">Month</button>
                <button class="period-btn" data-period="year">Year</button>
              </div>
            </div>
            <div class="card-content">
              <div class="chart-container">
                <canvas id="sales-chart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  
  <script src="../assets/js/admin/main.js"></script>
  <script src="../assets/js/admin/dashboard.js"></script>
</body>
</html>