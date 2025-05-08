<?php
  session_start();
  
  // Redirect if already logged in
  if(isset($_SESSION['user_id'])) {
    header("Location: ./index.php");
    exit;
  }
  
  // Check for redirect parameter
  $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';
  
  // Check for error messages
  $error = isset($_GET['error']) ? $_GET['error'] : '';
  $errorMsg = '';
  
  if($error === 'invalid') {
    $errorMsg = 'Invalid email or password.';
  } elseif($error === 'empty') {
    $errorMsg = 'Please fill in all fields.';
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - BookHaven</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/auth.css">
</head>
<body>
  <?php include './frontend/components/header.php'; ?>
  
  <main class="auth-container">
    <div class="auth-form-container">
      <div class="auth-header">
        <h1>Login to Your Account</h1>
        <p>Welcome back! Please enter your details.</p>
      </div>
      
      <?php if($errorMsg !== ''): ?>
        <div class="alert alert-error">
          <?php echo $errorMsg; ?>
        </div>
      <?php endif; ?>
      
      <form action="./backend/api/login.php" method="POST" class="auth-form">
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-input">
            <input type="password" id="password" name="password" required>
            <button type="button" class="toggle-password">
              <i class="icon-eye"></i>
            </button>
          </div>
        </div>
        
        <div class="form-options">
          <div class="remember-me">
            <input type="checkbox" id="remember" name="remember" value="1">
            <label for="remember">Remember me</label>
          </div>
          <a href="./forgot-password.php" class="forgot-password">Forgot password?</a>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
      </form>
      
      <div class="auth-divider">
        <span>or</span>
      </div>
      
      <div class="social-auth">
        <button class="btn btn-social btn-google">
          <i class="icon-google"></i> Sign in with Google
        </button>
        <button class="btn btn-social btn-facebook">
          <i class="icon-facebook"></i> Sign in with Facebook
        </button>
      </div>
      
      <div class="auth-footer">
        <p>Don't have an account? <a href="./register.php<?php echo $redirect ? "?redirect=$redirect" : ''; ?>">Sign up</a></p>
      </div>
    </div>
    
    <div class="auth-image">
      <div class="image-overlay">
        <div class="auth-quote">
          <blockquote>
            "A reader lives a thousand lives before he dies. The man who never reads lives only one."
          </blockquote>
          <cite>- George R.R. Martin</cite>
        </div>
      </div>
    </div>
  </main>

  <?php include './frontend/components/footer.php'; ?>
  
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/auth.js"></script>
</body>
</html>