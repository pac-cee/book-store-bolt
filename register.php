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
  
  if($error === 'email') {
    $errorMsg = 'Email is already registered. Please use a different email or log in.';
  } elseif($error === 'password') {
    $errorMsg = 'Passwords do not match.';
  } elseif($error === 'empty') {
    $errorMsg = 'Please fill in all fields.';
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - BookHaven</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/auth.css">
</head>
<body>
  <?php include './frontend/components/header.php'; ?>
  
  <main class="auth-container">
    <div class="auth-form-container">
      <div class="auth-header">
        <h1>Create an Account</h1>
        <p>Join BookHaven to start exploring our collection.</p>
      </div>
      
      <?php if($errorMsg !== ''): ?>
        <div class="alert alert-error">
          <?php echo $errorMsg; ?>
        </div>
      <?php endif; ?>
      
      <form action="./backend/api/register.php" method="POST" class="auth-form">
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-input">
            <input type="password" id="password" name="password" required minlength="8">
            <button type="button" class="toggle-password">
              <i class="icon-eye"></i>
            </button>
          </div>
          <div class="password-requirements">
            <p>Password must be at least 8 characters</p>
          </div>
        </div>
        
        <div class="form-group">
          <label for="confirm-password">Confirm Password</label>
          <div class="password-input">
            <input type="password" id="confirm-password" name="confirm_password" required minlength="8">
            <button type="button" class="toggle-password">
              <i class="icon-eye"></i>
            </button>
          </div>
        </div>
        
        <div class="form-group terms">
          <input type="checkbox" id="terms" name="terms" required>
          <label for="terms">
            I agree to the <a href="./terms.php" target="_blank">Terms of Service</a> and <a href="./privacy.php" target="_blank">Privacy Policy</a>
          </label>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
      </form>
      
      <div class="auth-divider">
        <span>or</span>
      </div>
      
      <div class="social-auth">
        <button class="btn btn-social btn-google">
          <i class="icon-google"></i> Sign up with Google
        </button>
        <button class="btn btn-social btn-facebook">
          <i class="icon-facebook"></i> Sign up with Facebook
        </button>
      </div>
      
      <div class="auth-footer">
        <p>Already have an account? <a href="./login.php<?php echo $redirect ? "?redirect=$redirect" : ''; ?>">Sign in</a></p>
      </div>
    </div>
    
    <div class="auth-image">
      <div class="image-overlay">
        <div class="auth-quote">
          <blockquote>
            "Books are a uniquely portable magic."
          </blockquote>
          <cite>- Stephen King</cite>
        </div>
      </div>
    </div>
  </main>

  <?php include './frontend/components/footer.php'; ?>
  
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/auth.js"></script>
</body>
</html>