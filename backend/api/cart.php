<?php
session_start();
require_once '../config/database.php';
require_once '../functions/cart_functions.php';
require_once '../functions/book_functions.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Set default response
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'cartCount' => 0
];

// Process cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            // Get parameters
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Validate book ID
            if ($bookId <= 0) {
                $response['message'] = 'Invalid book ID';
                break;
            }
            
            // Validate quantity
            if ($quantity <= 0) {
                $response['message'] = 'Quantity must be greater than zero';
                break;
            }
            
            // Check if book exists and is in stock
            if (!isBookInStock($conn, $bookId, $quantity)) {
                $response['message'] = 'This book is out of stock or has insufficient quantity';
                break;
            }
            
            // Add to cart
            $_SESSION['cart'] = addToCart($_SESSION['cart'], $bookId, $quantity);
            
            $response['success'] = true;
            $response['message'] = 'Item added to cart successfully';
            $response['cartCount'] = getCartCount($_SESSION['cart']);
            break;
            
        case 'update':
            // Get parameters
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Validate book ID
            if ($bookId <= 0 || !isset($_SESSION['cart'][$bookId])) {
                $response['message'] = 'Item not found in cart';
                break;
            }
            
            // Check if book exists and is in stock
            if ($quantity > 0 && !isBookInStock($conn, $bookId, $quantity)) {
                $response['message'] = 'Requested quantity is not available';
                break;
            }
            
            // Update cart
            $_SESSION['cart'] = updateCartItem($_SESSION['cart'], $bookId, $quantity);
            
            $response['success'] = true;
            $response['message'] = 'Cart updated successfully';
            $response['cartCount'] = getCartCount($_SESSION['cart']);
            break;
            
        case 'remove':
            // Get parameters
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            
            // Validate book ID
            if ($bookId <= 0 || !isset($_SESSION['cart'][$bookId])) {
                $response['message'] = 'Item not found in cart';
                break;
            }
            
            // Remove from cart
            $_SESSION['cart'] = removeFromCart($_SESSION['cart'], $bookId);
            
            $response['success'] = true;
            $response['message'] = 'Item removed from cart successfully';
            $response['cartCount'] = getCartCount($_SESSION['cart']);
            break;
            
        case 'clear':
            // Clear cart
            $_SESSION['cart'] = clearCart();
            
            $response['success'] = true;
            $response['message'] = 'Cart cleared successfully';
            $response['cartCount'] = 0;
            break;
            
        default:
            $response['message'] = 'Invalid action';
            break;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);